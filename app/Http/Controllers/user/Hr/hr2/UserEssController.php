<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\user\Hr\hr2\EssRequest;
use App\Models\user\Hr\hr3\ClaimsHr3;
use App\Models\user\Hr\hr2\PayrollRequestHr2;
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEssController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            abort(403, 'Employee not found.');
        }

      // Approved shifts (used for Leave)
        $approvedShifts = Shift::where('employee_id', $employee->employee_id)
            ->where('is_active', 1)
            ->where('status', 'approved')
            ->get();

        // Active shifts (used for Request Shift)
        $activeShifts = Shift::where('employee_id', $employee->employee_id)
            ->where('is_active', 1)
            ->get();
        
        // ESS Requests
        $essRequests = EssRequest::where('employee_id', $employee->employee_id)->get();

        // Claims
        $claims = ClaimsHr3::where('employee_id', $employee->employee_id)->get();

        // Payroll Requests
        $payrollRequests = PayrollRequestHr2::where('employee_id', $employee->employee_id)
            ->get()
            ->map(function($p) {
                return (object)[
                    'id' => $p->id,
                    'ess_id' => $p->ess_id, 
                    'type' => 'Payroll',
                    'details' => $p->details, 
                    'salary' => $p->status === 'approved' ? $p->salary : null,
                    'status' => $p->status,
                    'created_at' => $p->created_at,
                ];
            });
        // Latest approved payroll request
        $latestPayroll = PayrollRequestHr2::where('employee_id', $employee->employee_id)
            ->where('status', 'approved')
            ->latest('created_at')
            ->first();
        // Include Shift Requests in history
        $shiftRequests = Shift::where('employee_id', $employee->employee_id)
            ->where('requested_by', $employee->employee_id)
            ->where('status', 'pending')
            ->get()
            ->map(function($s) {
                return (object)[
                    'id' => $s->id,
                    'ess_id' => 'SHIFT' . $s->id,
                    'type' => 'Request Shift',
                    'details' => $s->shift_name . ' on ' . $s->day_of_week,
                    'shift_id' => $s->id,
                    'status' => $s->status,
                    'created_at' => $s->created_at,
                ];
            });

        // Merge all history
        $history = $essRequests->concat($claims)->concat($payrollRequests)->concat($shiftRequests)
            ->sortByDesc('created_at');

      return view('hr.hr2.ess', compact(
            'employee',
            'approvedShifts',
            'activeShifts',
            'history',
            'latestPayroll'
        ));
    }

    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        $request->validate([
            'type' => 'required|string',
            'details' => 'required|string|max:2000',
        ]);

        // --- GENERATE UNIQUE ESS_ID ---
        $totalRequests = EssRequest::count() + PayrollRequestHr2::count();
        $newEssId = 'ESS' . str_pad($totalRequests + 1, 4, '0', STR_PAD_LEFT);
        while (EssRequest::where('ess_id', $newEssId)->exists() || PayrollRequestHr2::where('ess_id', $newEssId)->exists()) {
            $totalRequests++;
            $newEssId = 'ESS' . str_pad($totalRequests + 1, 4, '0', STR_PAD_LEFT);
        }

        // --- Handle Request Shift ---
        if ($request->type === 'Request Shift') {
            $shift = Shift::where('employee_id', $employee->employee_id)
                ->where('is_active', 1)
                ->first();

            if (!$shift) {
                return redirect()->back()->with('error', 'No active shift found to request.');
            }

            // Update existing shift instead of creating a duplicate
            $shift->update([
                'requested_by' => $employee->employee_id,
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', "Shift request submitted successfully.");
        }

        // --- Handle Payroll Request ---
        if ($request->type === 'Payroll') {
            $exists = PayrollRequestHr2::where('employee_id', $employee->employee_id)
                ->where('status', 'pending')
                ->exists();

            if($exists) {
                return redirect()->back()->with('error', 'You already have a pending payroll request.');
            }

            PayrollRequestHr2::create([
                'ess_id'      => $newEssId,
                'employee_id' => $employee->employee_id,
                'salary'      => $employee->salary ?? 0,
                'status'      => 'pending',
                'details'     => $request->details,
            ]);

            return redirect()->back()->with('success', 'Payroll request submitted successfully.');
        }

        if ($request->type === 'Leave') {

        $approvedShift = Shift::where('employee_id', $employee->employee_id)
            ->where('is_active', 1)
            ->where('status', 'approved')
            ->first();

        if (!$approvedShift) {
            return redirect()->back()->with('error', 'You cannot request leave because you do not have an approved shift.');
        }
    }

        // --- Other ESS Requests ---
        EssRequest::create([
            'ess_id'      => $newEssId, 
            'employee_id' => $employee->employee_id,
            'type'        => $request->type,
            'details'     => $request->details,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', "Request submitted successfully.");
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