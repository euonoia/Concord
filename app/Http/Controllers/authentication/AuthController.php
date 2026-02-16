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
        'username'  => 'required|string|max:50|unique:users',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|min:8|confirmed',
        'role_slug' => 'required|string|in:hr_employee,logistics_employee,finance_employee,core_employee,patient,patient_guardian', 
    ]);

    $userType = match (true) {
        str_contains($validated['role_slug'], 'employee') => 'staff',
        str_contains($validated['role_slug'], 'patient')  => 'patient',
        default                                           => 'patient',
    };


    $user = User::create([
        'username'  => $validated['username'],
        'email'     => $validated['email'],
        'password'  => Hash::make($validated['password']),
        'user_type' => $userType, 
        'role_slug' => $validated['role_slug'], 
        'is_active' => true,
        'uuid'   => (string) \Illuminate\Support\Str::uuid(),
    ]);

  
    Auth::login($user);

    
    return $this->redirectByUserRole($user);
}
  public function login(Request $request)
{

    $request->validate([
        'login'    => ['required', 'string'],
        'password' => ['required'],
    ]);

   
    $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    
    $credentials = [
        $loginType   => $request->login,
        'password'   => $request->password,
        'is_active'  => 1, 
        'deleted_at' => null, 
    ];

    $remember = $request->has('remember');

 
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        

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

  
    protected function redirectByUserRole($user)
    {
        return match ($user->role_slug) {
            'hr_admin', 'hr_employee'           => redirect()->route('hr.dashboard'),
            'logistics_admin', 'logistics_employee' => redirect()->route('logistics.dashboard'),
            'finance_admin', 'finance_employee' => redirect()->route('finance.dashboard'),
            'core_admin', 'core_employee'       => redirect()->route('core.dashboard'),
            'sys_super_admin'                   => redirect()->route('admin.dashboard'),
            'patient', 'patient_guardian' => redirect()->route('patients.dashboard'),
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