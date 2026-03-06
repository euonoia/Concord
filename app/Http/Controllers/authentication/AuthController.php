<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:50|unique:users', 
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'role_slug' => 'required|string|in:hr_admin,hr_employee,logistics_employee,finance_employee,core_admin,core_employee,patient,patient_guardian,admin,doctor,nurse,head_nurse,receptionist,billing',
            // Profile fields for the Employee table
            'first_name' => 'required_if:role_slug,hr_admin,hr_employee,logistics_employee,finance_employee,core_admin,core_employee,admin,doctor,nurse,head_nurse,receptionist,billing|string|max:255',
            'last_name'  => 'required_if:role_slug,hr_admin,hr_employee,logistics_employee,finance_employee,core_admin,core_employee,admin,doctor,nurse,head_nurse,receptionist,billing|string|max:255',
        ]);

        // Determine user type
        $userType = match (true) {
            str_contains($validated['role_slug'], 'employee') || str_contains($validated['role_slug'], 'admin') || in_array($validated['role_slug'], ['doctor', 'nurse', 'head_nurse', 'receptionist', 'billing']) => 'staff',
            str_contains($validated['role_slug'], 'patient')  => 'patient',
            default                                           => 'patient',
        };

        // Use a Transaction to ensure data integrity
        $user = DB::transaction(function () use ($validated, $userType, $request) {
            
            // 1. Create the User (Auth record)
            $user = User::create([
                'username'  => $validated['username'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'user_type' => $userType,
                'role_slug' => $validated['role_slug'],
                'is_active' => true,
            ]);

            // 2. Create the Employee Profile if the user is staff
            if ($userType === 'staff') {
                Employee::create([
                    'user_id'     => $user->id,         
                    'employee_id' => $user->username,   
                    'first_name'  => $request->first_name,
                    'last_name'   => $request->last_name,
                    'hire_date'   => now(),
                    'is_on_duty'  => true,
                ]);
            }

            return $user;
        });

        Auth::login($user);
        return $this->redirectByUserRole($user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Support login via email or PAT-XXXX username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType   => $request->login,
            'password'   => $request->password,
            'is_active'  => 1,
            'deleted_at' => null,
        ];

        if (Auth::validate($credentials)) {
            $user = User::where($loginType, $request->login)->first();
            
            if (!$user || !$user->is_active) {
                return back()->withErrors(['login' => 'Account is inactive.'])->withInput();
            }

            // Store user info in session for 2FA
            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->has('remember'));

            // Send OTP
            $this->sendOtpForUser($user);

            return redirect()->route('portal.2fa');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login'));
    }

    /**
     * Show the 2FA verification form.
     */
    public function show2fa(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('portal.login');
        }

        return view('authentication.2fa');
    }

    /**
     * Verify the 2FA code and log in the user.
     */
    public function verify2fa(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('2fa_user_id');
        $user = User::findOrFail($userId);

        $otp = \App\Models\OTP::where('identifier', $user->email)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp || $otp->otp_code !== $request->otp_code || $otp->isExpired() || $otp->attempts >= 5) {
            if ($otp) $otp->increment('attempts');
            return back()->withErrors(['otp_code' => 'Invalid or expired verification code.']);
        }

        // Success: Mark OTP as used
        $otp->update(['is_used' => true]);

        // Log the user in
        Auth::login($user, $request->session()->get('2fa_remember'));

        // Cleanup session
        $request->session()->forget(['2fa_user_id', '2fa_remember']);
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return $this->redirectByUserRole($user);
    }

    /**
     * Resend the 2FA code.
     */
    public function resend2fa(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('portal.login');
        }

        $user = User::findOrFail($request->session()->get('2fa_user_id'));
        $this->sendOtpForUser($user);

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    /**
     * Helper to send OTP to user.
     */
    protected function sendOtpForUser($user)
    {
        // Invalidate previous OTPs
        \App\Models\OTP::where('identifier', $user->email)->where('is_used', false)->update(['is_used' => true]);

        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        \App\Models\OTP::create([
            'identifier' => $user->email,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
            'is_used' => false,
        ]);

        Mail::to($user->email)->send(new \App\Mail\OTPMail($otpCode));
    }

    protected function redirectByUserRole($user)
    {
        return match ($user->role_slug) {
            'hr_admin', 'sys_super_admin'           => redirect()->route('admin.dashboard'),
            'hr_employee'                           => redirect()->route('hr.dashboard'),
            'logistics_admin', 'logistics_employee' => redirect()->route('logistics.dashboard'),
            'finance_admin', 'finance_employee'     => redirect()->route('finance.dashboard'),
            'core_admin', 'core_employee'           => redirect()->route('core.dashboard'),
            'patient', 'patient_guardian'           => redirect()->route('patients.dashboard'),
            // Core1 Granular Roles
            'admin'                                 => redirect()->route('core1.admin.dashboard'),
            'doctor'                                => redirect()->route('core1.doctor.dashboard'),
            'nurse', 'head_nurse'                   => redirect()->route('core1.nurse.dashboard'),
            'receptionist'                          => redirect()->route('core1.receptionist.dashboard'),
            'billing'                               => redirect()->route('core1.billing.dashboard'),
            default                                 => redirect('/'),
        };
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}