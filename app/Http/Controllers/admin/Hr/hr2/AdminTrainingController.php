<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\admin\Hr\hr2\Competency;
use App\Models\Employee;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;

class AdminTrainingController extends Controller
{
    /**
     * Allow only HR2 Admin
     */
    private function authorizeHr2()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->authorizeHr2();

        $departments = Department::where('is_active', 1)->get();

        return view('admin.hr2.training', compact('departments'));
    }

    public function getSpecializations($dept)
    {
        $this->authorizeHr2();

        $specs = DepartmentSpecialization::where('dept_code', $dept)
            ->where('is_active', 1)
            ->orderBy('specialization_name')
            ->get();

        return response()->json($specs);
    }

    public function getCompetencies($dept, $spec)
    {
        $this->authorizeHr2();

        $competencies = Competency::where('department_id', $dept)
            ->where('specialization_name', $spec)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return response()->json($competencies);
    }

    public function getEligibleEmployees(Request $request)
    {
        $this->authorizeHr2();

        $employees = EmployeeCompetencyCompletion::join(
                'employees',
                'employees.employee_id',
                '=',
                'employee_competency_completion_hr2.employee_id'
            )
            ->join(
                'competency_hr2',
                'competency_hr2.competency_code',
                '=',
                'employee_competency_completion_hr2.competency_code'
            )
            // HR3 Training Schedule
            ->leftJoin('training_schedule_hr3', function($join) {
                $join->on('employees.employee_id', '=', 'training_schedule_hr3.employee_id')
                     ->on('competency_hr2.competency_code', '=', 'training_schedule_hr3.competency_code');
            })
            // HR2 Training Scores
            ->leftJoin('employee_training_scores_hr2', function($join) {
                $join->on('employees.employee_id', '=', 'employee_training_scores_hr2.employee_id')
                     ->on('competency_hr2.competency_code', '=', 'employee_training_scores_hr2.competency_code');
            })
            // Evaluator Name
            ->leftJoin('employees as evaluator', 'evaluator.employee_id', '=', 'employee_training_scores_hr2.evaluated_by')
            
            ->where('competency_hr2.department_id', $request->department_id)
            ->where('competency_hr2.specialization_name', $request->specialization)
            ->where('competency_hr2.competency_code', $request->competency_code)
            ->where('employee_competency_completion_hr2.status', 'completed')

            ->select(
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',
                'employee_competency_completion_hr2.completed_at',
                'training_schedule_hr3.training_date',
                'training_schedule_hr3.training_time',
                'training_schedule_hr3.venue',
                'employee_training_scores_hr2.total_score as training_score',
                'employee_training_scores_hr2.evaluated_by',
                'evaluator.first_name as eval_fname', 
                'evaluator.last_name as eval_lname'
            )
            ->orderBy('employees.last_name')
            ->get();

        return response()->json($employees);
    }

    public function getValidatedEmployees(Request $request)
    {
        $this->authorizeHr2();

        $data = DB::table('validated_training_performance_hr1 as vtp')
            ->join('employees', 'employees.employee_id', '=', 'vtp.employee_id')
            ->leftJoin('employees as evaluator', 'evaluator.employee_id', '=', 'vtp.evaluated_by')

            ->select(
                'vtp.employee_id',
                'employees.first_name',
                'employees.last_name',
                'vtp.weighted_average',
                'vtp.status',
                'vtp.evaluated_at',
                'evaluator.first_name as eval_fname',
                'evaluator.last_name as eval_lname'
            )
            ->where('vtp.status', 'completed')
            ->orderByDesc('vtp.evaluated_at')
            ->get();

        return response()->json($data);
    }
}