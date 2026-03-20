<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\user\Hr\hr2\EssRequest;
use App\Models\user\Hr\hr3\ClaimsHr3;
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserEssController extends Controller
{
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Get active shifts assigned to employee
        $allShifts = Shift::where('employee_id', $employee->employee_id)
            ->where('is_active', 1)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get();

        // ESS requests & claims history
        $essRequests = EssRequest::where('employee_id', $employee->employee_id)->orderBy('created_at','desc')->get();
        $claims = ClaimsHr3::where('employee_id', $employee->employee_id)->orderBy('created_at','desc')->get();
        $history = $essRequests->merge($claims)->sortByDesc('created_at');

        return view('hr.hr2.ess', compact('employee','allShifts','history'));
    }

    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        $request->validate([
            'type' => 'required|string',
            'details' => 'required|string|max:2000',
        ]);

        // Handle Shift Request
        if ($request->type === 'Request Shift') {
            $shift = Shift::where('employee_id', $employee->employee_id)
                ->where('is_active', 1)
                ->first();

            if (!$shift) {
                return redirect()->back()->with('error', 'No active shift found to request.');
            }

            Shift::create([
                'employee_id' => $shift->employee_id,
                'shift_name' => $shift->shift_name,
                'day_of_week' => $shift->day_of_week,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'is_active' => 1,
                'requested_by' => $employee->employee_id,
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', "Shift request submitted successfully.");
        }

        // Other ESS types: Leave, Profile Update, Document
        EssRequest::create([
            'ess_id' => 'ESS' . str_pad(EssRequest::count() + 1, 4, '0', STR_PAD_LEFT),
            'employee_id' => $employee->employee_id,
            'type' => $request->type,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', "Request submitted successfully.");
    }
}