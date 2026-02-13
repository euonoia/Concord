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
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => 1])) {
                
                /** @var \App\Models\User $user */
                $user = Auth::user(); 
                
                $request->session()->regenerate();

              
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                return $this->redirectByUserRole($user);
            }

            return back()->withErrors(['email' => 'Invalid credentials.']);
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