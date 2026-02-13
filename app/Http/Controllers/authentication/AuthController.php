<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Registration is usually only for Patients in a Hospital System.
     * Staff are usually created by an Admin/HR.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'patient',           // Default for self-reg
            'role_slug' => 'patient_standard',  // Default role
            'is_active' => true,
        ]);

        Auth::login($user);
        return redirect()->route('patient.dashboard');
    }

  public function login(Request $request)
{
    // 1. Validate the input as a generic 'login' string
    $request->validate([
        'login'    => ['required', 'string'],
        'password' => ['required'],
    ]);

    // 2. Determine if the input is an email or a username
    // filter_var returns the email if valid, or false if it's just a string
    $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // 3. Prepare credentials with the robust 'is_active' check
    $credentials = [
        $loginType   => $request->login,
        'password'   => $request->password,
        'is_active'  => 1, // Only allow active users
        'deleted_at' => null, // Ensure they aren't soft-deleted
    ];

    $remember = $request->has('remember');

    // 4. Attempt login
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Audit log update
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Use our robust redirection logic
        return $this->redirectByUserRole($user);
    }

    // 5. If failed, return with input so they don't have to re-type the username
    return back()->withErrors([
        'login' => 'The provided credentials do not match our records or account is inactive.',
    ])->withInput($request->only('login'));
}

    /**
     * Logic to send users to their specific subsystem
     */
    protected function redirectByUserRole($user)
    {
        return match ($user->role_slug) {
            'hr_admin', 'hr_employee'           => redirect()->route('hr.dashboard'),
            'logistics_admin', 'logistics_employee' => redirect()->route('logistics.dashboard'),
            'finance_admin', 'finance_employee' => redirect()->route('finance.dashboard'),
            'core_admin', 'core_employee'       => redirect()->route('core.dashboard'),
            'sys_super_admin'                   => redirect()->route('admin.dashboard'),
            'patient_standard', 'patient_guardian' => redirect()->route('patient.portal'),
            default => redirect('/'),
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