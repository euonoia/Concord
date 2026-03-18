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
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3', 'admin_ultra'])) {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->authorizeHr3();

        /*
        |--------------------------------------------------------------------------
        | GET ELIGIBLE EMPLOYEES
        |--------------------------------------------------------------------------
        | Only show employees who have been ENROLLED via the HR2 Sync Tool.
        | We join with 'competency_enroll_hr2' to ensure they have an active path.
        */
        $eligibleEmployees = DB::table('competency_enroll_hr2 as ce')
            ->join('employees as e', 'ce.employee_id', '=', 'e.employee_id')
            ->select(
                'e.employee_id',
                'e.first_name',
                'e.last_name',
                'e.specialization'
            )
            ->where('ce.status', 'enrolled') // Only those synced in HR2
            ->distinct()
            ->orderBy('e.first_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET TRAINERS (HR2 Admins)
        |--------------------------------------------------------------------------
        */
        $availableTrainers = DB::table('users')
            ->join('employees', 'users.id', '=', 'employees.user_id')
            ->where('users.role_slug', 'admin_hr2')
            ->select('employees.employee_id', 'employees.first_name', 'employees.last_name')
            ->get();

        $schedules = TrainingScheduleHr3::with(['trainer', 'presenter', 'employee'])
            ->latest()
            ->get();

        return view(
            'admin.hr3.schedule.training_index',
            compact('eligibleEmployees', 'schedules', 'availableTrainers')
        );
    }

    /**
     * AJAX/Fetch call for the Schedule Modal
     * Returns only the competencies the user is ACTUALLY enrolled in (from HR2)
     */
    public function getCompetencies($emp_id)
    {
        // 1. Get basic employee info
        $employeeInfo = DB::table('employees')
            ->where('employee_id', $emp_id)
            ->first(['department_id', 'specialization']);

        // 2. Get ONLY the competencies assigned via HR2 Sync
        $competencies = DB::table('competency_enroll_hr2')
            ->where('employee_id', $emp_id)
            ->where('status', 'enrolled')
            ->get(['competency_code']);

        return response()->json([
            'info' => $employeeInfo,
            'competencies' => $competencies
        ]);
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
            'presented_by'    => $loggedInEmployee ? $loggedInEmployee->employee_id : 'SYSTEM',
        ]);

        return back()->with('success', 'Face-to-face training scheduled for the HR2 enrolled competency.');
    }
}