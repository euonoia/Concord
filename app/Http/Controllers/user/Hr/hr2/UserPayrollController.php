<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\user\Hr\hr2\PayrollRequestHr2;
use Illuminate\Support\Facades\Auth;

class UserPayrollController extends Controller
{
    // Show payroll request page
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Optional: get salary info (assuming stored in employee table)
        $salary = $employee->salary ?? 0;

        // Get payroll request history
        $history = PayrollRequestHr2::where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.hr2.payroll', compact('employee', 'salary', 'history'));
    }

    // Store payroll request
    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Validate (salary must be positive decimal)
        $request->validate([
            'salary' => 'required|numeric|min:0',
        ]);

        // Prevent duplicate pending requests
        $exists = PayrollRequestHr2::where('employee_id', $employee->employee_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You already have a pending payroll request.');
        }

        PayrollRequestHr2::create([
            'employee_id' => $employee->employee_id,
            'salary'      => $request->salary,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Payroll request submitted successfully.');
    }
}