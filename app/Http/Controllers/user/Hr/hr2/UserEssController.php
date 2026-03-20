<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\EssRequest;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserEssController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return redirect()->back()->with('error', 'Employee not found.');

        // Get all active shifts for the dropdown
        $allShifts = Shift::where('employee_id', $employee->employee_id)
            ->where('is_active', 1)
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();

        // Get request history
        $requests = EssRequest::where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.hr2.ess', compact('requests', 'employee', 'allShifts'));
    }

    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        $request->validate([
            'type'       => 'required|string',
            'details'    => 'required|string|max:2000',
            'shift_id'   => 'required_if:type,Leave',
            'leave_date' => 'required_if:type,Leave|nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:leave_date',
        ]);

        // Robust Check: Prevent duplicate leave for the same date
        if ($request->type === 'Leave') {
            $exists = EssRequest::where('employee_id', $employee->employee_id)
                ->where('leave_date', $request->leave_date)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();
            
            if ($exists) {
                return redirect()->back()->with('error', 'A leave request already exists for this date.');
            }
        }

        // Generate custom ESS ID
        $lastEss = EssRequest::orderBy('created_at', 'desc')->first();
        $lastNumber = $lastEss ? (int) preg_replace('/\D/', '', $lastEss->ess_id) : 0;
        $ess_id = 'ESS' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        // Save to Database
        EssRequest::create([
            'ess_id'      => $ess_id,
            'employee_id' => $employee->employee_id,
            'shift_id'    => $request->shift_id, 
            'type'        => $request->type,
            'details'     => $request->details,
            'leave_date'  => $request->leave_date, // Now saving to dedicated column
            'end_date'    => $request->end_date ?? $request->leave_date,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', "Request $ess_id submitted successfully.");
    }

    public function payrollIndex()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $requests = \App\Models\admin\Hr\hr4\PayrollEssRequest::where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.ess_payroll.index', compact('employee', 'requests'));
    }

    public function payrollStore(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $request->validate([
            'request_type' => 'required|in:Payroll,Bonus,Deduction,Advance,Other',
            'details' => 'nullable|string|max:500',
        ]);

        \App\Models\admin\Hr\hr4\PayrollEssRequest::create([
            'employee_id' => $employee->employee_id,
            'request_type' => $request->request_type,
            'details' => $request->details ?? 'N/A',
            'status' => 'pending',
            'requested_date' => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Payroll request submitted successfully. Please wait for admin approval.');
    }
}