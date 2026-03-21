<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;

use App\Models\user\Core\core1\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'  => 'required|string|max:50|unique:users',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'role_slug' => 'required|string|in:admin_hr1,admin_hr2,admin_hr3,admin_hr4,admin_logistics1,admin_logistics2,admin_core1,admin_core2,patient,admin,doctor,nurse,head_nurse,billing_officer,receptionist, admin_financials, patient',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $userType = str_contains($validated['role_slug'], 'patient') ? 'patient' : 'staff';

        $user = DB::transaction(function () use ($validated, $userType, $request) {
            $user = User::create([
                'username'  => $validated['username'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'user_type' => $userType,
                'role_slug' => $validated['role_slug'],
                'is_active' => 1,
            ]);

            if ($userType === 'staff') {
                Employee::create([
                    'user_id' => $user->id,
                    'employee_id' => $user->username,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'hire_date' => now(),
                    'is_on_duty' => true,
                ]);
            }

            if ($userType === 'patient') {
                Patient::create([
                    'patient_id' => $user->username,
                    'mrn' => Patient::generateMRN(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $user->email,
                    'registration_status' => 'PRE_REGISTERED',
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
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
            'is_active' => 1,
            'deleted_at' => null,
        ];

        // Step 1: Validate credentials without logging in
        if (Auth::validate($credentials)) {
            $user = User::where($loginType, $request->login)->first();

            /*
            // Step 2: Store user ID in session for 2FA
            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->has('remember'));

            // Step 3: Send OTP
            $this->sendOtpForUser($user);

            return redirect()->route('portal.2fa');
            */

            // --- DIRECT LOGIN (OTP DISABLED) ---
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return $this->redirectByUserRole($user);
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records or account is inactive.',
        ])->withInput($request->only('login'));
    }

    /*
    public function show2fa(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) {
            return redirect()->route('portal.login');
        }

        return view('authentication.2fa');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('portal.login');
        }

        $user = User::findOrFail($userId);

        // Verify OTP
        $otp = \App\Models\OTP::where('identifier', $user->email)
            ->where('otp_code', $request->otp_code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            // Mark OTP as used
            $otp->update(['is_used' => true]);

            // Final Login
            Auth::login($user, $request->session()->get('2fa_remember', false));
            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            $request->session()->regenerate();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return $this->redirectByUserRole($user);
        }

        // Handle failure
        $otp = \App\Models\OTP::where('identifier', $user->email)
            ->where('is_used', false)
            ->latest()
            ->first();

        if ($otp) {
            $otp->increment('attempts');
            if ($otp->attempts >= 5) {
                $otp->update(['is_used' => true]);
                return redirect()->route('portal.login')->withErrors(['otp' => 'Too many failed attempts. Please login again.']);
            }
        }

        return back()->withErrors(['otp_code' => 'Invalid or expired verification code.']);
    }

    public function resend2fa(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('portal.login');
        }

        $user = User::findOrFail($userId);
        $this->sendOtpForUser($user);

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    protected function sendOtpForUser($user)
    {
        // Invalidate old OTPs
        \App\Models\OTP::where('identifier', $user->email)->where('is_used', false)->update(['is_used' => true]);

        // Generate 6-digit code
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store
        \App\Models\OTP::create([
            'identifier' => $user->email,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'is_used' => false,
        ]);

        // Send Email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OTPMail($otpCode));
    }
    */

    protected function redirectByUserRole($user)    {
        $role = $user->role_slug;

        return match (true) {
                // --- HR MODULAR DASHBOARDS ---
                $role === 'admin_hr1' => redirect()->route('admin.hr1.dashboard'),
                $role === 'admin_hr2' => redirect()->route('admin.hr2.dashboard'),
                $role === 'admin_hr3' => redirect()->route('admin.hr3.dashboard'),
                $role === 'admin_hr4' => redirect()->route('admin.hr4.dashboard'),

                // --- LOGISTICS MODULAR DASHBOARDS ---
                $role === 'admin_logistics1' => redirect()->route('admin.logistics1.dashboard'),
                $role === 'admin_logistics2' => redirect()->route('admin.logistics2.dashboard'),

                // --- CORE MODULAR DASHBOARDS ---
                $role === 'admin_core1' => redirect()->route('core1.admin.dashboard'),
                $role === 'admin_core2' => redirect()->route('admin.core2.dashboard'),
                // Core2 admin 
                $role === 'core_admin' => redirect()->route('core2.dashboard'),

                // --- FINANCIALS ---
                $role === 'admin_financials' => redirect()->route('admin.financials.dashboard'),

                // --- FALLBACKS FOR GENERAL STAFF ---
                $role === 'doctor' => redirect()->route('core1.doctor.dashboard'),
                $role === 'nurse' => redirect()->route('core1.nurse.dashboard'),
                $role === 'head_nurse' => redirect()->route('core1.nurse.dashboard'),
                $role === 'receptionist' => redirect()->route('core1.receptionist.dashboard'),
                $role === 'billing_officer' => redirect()->route('core1.billing.dashboard'),
                $role === 'employee' => redirect()->route('hr.dashboard'),

                // --- PATIENTS ---
                $role === 'patient' => redirect()->route('core1.patient.dashboard'),

                default => redirect('/'),
            };    }
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}