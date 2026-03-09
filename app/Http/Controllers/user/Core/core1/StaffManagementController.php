<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class StaffManagementController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role_slug', ['doctor', 'nurse', 'head_nurse', 'receptionist', 'billing_officer'])
            ->latest()
            ->paginate(20);
        
        return view('core.core1.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('core.core1.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:doctor,nurse,head_nurse,receptionist,billing_officer',
            'department' => 'nullable|string',
            'specialization' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // Mapping name to first_name/last_name for Employee table
        $nameParts = explode(' ', $validated['name'], 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '.';

        DB::transaction(function () use ($validated, $firstName, $lastName) {
            // 1. Create the Global User
            $user = User::create([
                'username'  => $validated['email'], // Use email as username for consistency
                'email'     => $validated['email'],
                'password'  => Hash::make('password'),
                'user_type' => 'staff',
                'role_slug' => $validated['role'], // Aligned roles
                'is_active' => true,
            ]);

            // 2. Create the Employee Profile
            Employee::create([
                'user_id'     => $user->id,
                'employee_id' => strtoupper(substr($validated['role'], 0, 3)) . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'phone'       => $validated['phone'],
                'specialization' => $validated['specialization'],
                'hire_date'   => now(),
                'is_on_duty'  => true,
            ]);
        });

        return redirect()->route('core1.staff.index')->with('success', 'Staff member added successfully and synced with global employee records.');
    }
}

