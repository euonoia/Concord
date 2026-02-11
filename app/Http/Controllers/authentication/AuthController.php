<?php

namespace App\Http\Controllers\authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Required for password encryption

class AuthController extends Controller
{
    /**
     * Handle user registration (Patient)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Requires a password_confirmation field in your form
            'patient_code' => 'required|unique:users,patient_code',
        ]);

        // 1. Create the Patient User
        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), 
            'patient_code' => $validated['patient_code'],
            'status' => 'active',
        ]);

        // 2. Log the user in immediately
        Auth::login($user);

        // 3. Redirect to the dashboard
        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('core/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle user logout
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}