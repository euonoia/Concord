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

        // 1. Get employees who have completed competencies in HR2
        $eligibleEmployees = DB::table('employee_competency_completion_hr2')
            ->where('status', 'completed')
            ->select('employee_id')
            ->distinct()
            ->get();

        // 2. Get all Employees who have the 'admin_hr2' role slug (Selected as Trainer)
        $availableTrainers = DB::table('users')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.role_slug', 'admin_hr2')
            ->select('employees.employee_id', 'employees.first_name', 'employees.last_name')
            ->get();

        /** * 3. Fetch schedules and join with employees to get Presenter name 
         * (Assuming your model has a 'presenter' relationship linked to presented_by)
         */
        $schedules = TrainingScheduleHr3::with(['trainer', 'presenter'])->latest()->get();

        return view('admin.hr3.schedule.training_index', compact('eligibleEmployees', 'schedules', 'availableTrainers'));
    }

    public function store(Request $request)
    {
        $this->authorizeHr3();

        $request->validate([
            'employee_id'     => 'required',
            'competency_code' => 'required',
            'training_date'   => 'required|date',
            'training_time'   => 'required',
            'venue'           => 'required',
            'trainer_id'      => 'required', // Selected HR2 Admin
        ]);

        // AUTOMATION: Get the Employee ID of the logged-in user (The Presenter)
        $loggedInEmployee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        TrainingScheduleHr3::create([
            'employee_id'     => $request->employee_id,
            'competency_code' => $request->competency_code,
            'training_date'   => $request->training_date,
            'training_time'   => $request->training_time,
            'venue'           => $request->venue,
            'notes'           => $request->notes,
            'trainer_id'      => $request->trainer_id, // From dropdown
            'presented_by'    => $loggedInEmployee ? $loggedInEmployee->employee_id : 'SYSTEM', // Logged in User
        ]);

        return back()->with('success', 'Training scheduled successfully. You are marked as the presenter.');
    }

    public function getCompetencies($emp_id)
    {
        $employeeInfo = DB::table('employees')
            ->where('employee_id', $emp_id)
            ->first(['department_id', 'specialization']);

        $competencies = DB::table('employee_competency_completion_hr2')
            ->where('employee_id', $emp_id)
            ->where('status', 'completed') 
            ->get(['competency_code']);

        return response()->json([
            'info' => $employeeInfo,
            'competencies' => $competencies
        ]);
    }
}