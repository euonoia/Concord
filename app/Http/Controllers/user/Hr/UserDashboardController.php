<?php

namespace App\Http\Controllers\user\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\admin\Hr\hr3\Shift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
        {
            $employee = Employee::with('position')->where('user_id', Auth::id())->first();
            $allShifts = collect(); 

            if ($employee) {
                $allShifts = \App\Models\admin\Hr\hr3\Shift::where('employee_id', $employee->employee_id)
                    ->where('is_active', 1)
                    ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                    ->get();
            }

            return view('hr.dashboard', compact('employee', 'allShifts'));
        }
}