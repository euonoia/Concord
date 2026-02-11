<?php

namespace App\Http\Controllers\authentication;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmployeeAuthController extends Controller
{
    /**
     * Show the employee login form.
     */
    public function showLogin()
    {
        return view('authentication.employee_login');
    }

    /**
     * Handle an employee authentication attempt.
     */
  public function login(Request $request)
    {
        $credentials = $request->validate([
            'employee_code' => ['required', 'string'],
            'password'      => ['required', 'string'],
        ]);

        if (Auth::guard('employee')->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect to the HR Dashboard
            return redirect()->intended(route('hr.dashboard'));
        }

        return back()->withErrors([
            'employee_code' => 'The provided credentials do not match our staff records.',
        ]);
    }

    /**
     * Optional: Create a new employee (Registration/Onboarding)
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_code'  => 'required|unique:employees,employee_code',
            'firstname'      => 'required|string|max:100',
            'lastname'       => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
            'department_id'  => 'required|integer',
            'password'       => 'required|min:8|confirmed',
        ]);

        Employee::create([
            'employee_code'  => $request->employee_code,
            'firstname'      => $request->firstname,
            'lastname'       => $request->lastname,
            'specialization' => $request->specialization,
            'department_id'  => $request->department_id,
            'password'       => Hash::make($request->password),
        ]);

        return redirect()->route('staff.login')->with('success', 'Employee registered successfully.');
    }

    
    public function destroy(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}