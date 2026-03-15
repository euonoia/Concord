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

        /*
        |--------------------------------------------------------------------------
        | Get employees who completed HR2 competencies
        |--------------------------------------------------------------------------
        */
        $eligibleEmployees = DB::table('employee_competency_completion_hr2 as ecc')
            ->join('employees as e', 'ecc.employee_id', '=', 'e.employee_id')
            ->where('ecc.status', 'completed')
            ->select(
                'e.employee_id',
                'e.first_name',
                'e.last_name'
            )
            ->distinct()
            ->orderBy('e.first_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Get HR2 Admins as Trainers
        |--------------------------------------------------------------------------
        */
        $availableTrainers = DB::table('users')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.role_slug', 'admin_hr2')
            ->select(
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Training schedules
        |--------------------------------------------------------------------------
        */
        $schedules = TrainingScheduleHr3::with(['trainer', 'presenter', 'employee'])
            ->latest()
            ->get();

        return view(
            'admin.hr3.schedule.training_index',
            compact('eligibleEmployees', 'schedules', 'availableTrainers')
        );
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
            'trainer_id'      => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logged-in user becomes presenter automatically
        |--------------------------------------------------------------------------
        */
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
            'trainer_id'      => $request->trainer_id,
            'presented_by'    => $loggedInEmployee
                ? $loggedInEmployee->employee_id
                : 'SYSTEM',
        ]);

        return back()->with(
            'success',
            'Training scheduled successfully. You are marked as the presenter.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch employee info + competencies
    |--------------------------------------------------------------------------
    */
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