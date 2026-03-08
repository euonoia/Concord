<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee; 
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
        // Updated to include your admin_hr, admin_logistics, and admin_core roles
        'role_slug' => 'required|string|in:admin_hr1,admin_hr2,admin_hr3,admin_hr4,admin_logistics1,admin_logistics2,admin_core1,admin_core2,patient,admin,doctor,nurse',
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
    ]);

    // Simplified logic: If it's not a patient, it's staff
    $userType = str_contains($validated['role_slug'], 'patient') ? 'patient' : 'staff';

    $user = DB::transaction(function () use ($validated, $userType, $request) {
        
        $user = User::create([
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'user_type' => $userType,
            'role_slug' => $validated['role_slug'],
            'is_active' => 1,
            // UUID is usually handled by a boot method in the Model, 
            // but you can also use: 'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

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

        if (Auth::attempt($credentials, $request->has('remember'))) {
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
        $role === 'admin_core1' => redirect()->route('admin.core1.dashboard'),
        $role === 'admin_core2' => redirect()->route('admin.core2.dashboard'),

        // --- FINANCIALS ---
        $role === 'finance' => redirect()->route('admin.financials.dashboard'),

        // --- FALLBACKS FOR GENERAL STAFF ---
        $role === 'doctor'       => redirect()->route('core1.doctor.dashboard'),
        $role === 'nurse'        => redirect()->route('core1.nurse.dashboard'),
        $role === 'receptionist' => redirect()->route('core1.receptionist.dashboard'),
        
        // --- PATIENTS ---
       $role === 'patient' => redirect()->route('patients.dashboard'),

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