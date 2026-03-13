<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\Employee; 
use App\Models\admin\Hr\hr3\TrainingScheduleHr3;
use Illuminate\Support\Facades\Auth;

class UserTrainingController extends Controller
{
    public function index()
    {
        if (!Auth::check()) return redirect()->route('portal.login');

        // Find the employee record linked to the logged-in user
        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        // Get only the schedules assigned to this specific employee
        $sessions = TrainingScheduleHr3::with(['trainer'])
            ->where('employee_id', $employeeRecord->employee_id)
            ->orderBy('training_date', 'asc')
            ->get();

        return view('hr.hr2.training', compact('sessions'));
    }
}