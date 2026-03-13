<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\Hr\hr3\TrainingScheduleHr3;

class AdminTrainingScheduleController extends Controller
{
    private function authorizeHr3()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3'])) {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->authorizeHr3();

        $eligibleEmployees = DB::table('employee_competency_completion_hr2')
            ->where('status', 'completed')
            ->select('employee_id')
            ->distinct()
            ->get();

        // Eager load the trainer relationship
        $schedules = TrainingScheduleHr3::with('trainer')->latest()->get();

        return view('admin.hr3.schedule.training_index', compact('eligibleEmployees', 'schedules'));
    }

    public function store(Request $request)
    {
        $this->authorizeHr3();

        $request->validate([
            'employee_id' => 'required',
            'competency_code' => 'required',
            'training_date' => 'required|date',
            'training_time' => 'required',
        ]);

        // AUTOMATION: Find the admin's employee profile
        $adminEmployee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        TrainingScheduleHr3::create([
            'employee_id'     => $request->employee_id,
            'competency_code' => $request->competency_code,
            'training_date'   => $request->training_date,
            'training_time'   => $request->training_time,
            'venue'           => $request->venue,
            'notes'           => $request->notes,
            'trainer_id'      => $adminEmployee ? $adminEmployee->employee_id : 'SYSTEM_ADMIN',
        ]);

        return back()->with('success', 'Training scheduled successfully.');
    }

    public function getCompetencies($emp_id)
    {
        // 1. Get Trainee Basic Info
        $employeeInfo = DB::table('employees')
            ->where('employee_id', $emp_id)
            ->first(['department_id', 'specialization']);

        // 2. Get Competencies from the specific HR2 table
        // We filter by 'completed' status as per your requirement
        $competencies = DB::table('employee_competency_completion_hr2')
            ->where('employee_id', $emp_id)
            ->where('status', 'completed') 
            ->get(['competency_code', 'verified_by', 'verification_notes']);

        return response()->json([
            'info' => $employeeInfo,
            'competencies' => $competencies
        ]);
    }
}