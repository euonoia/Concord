<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Competency;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\user\Hr\hr2\CompetencyEnroll;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use Illuminate\Support\Facades\DB;

class AdminTrainingEvaluationController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | TRAINING MATRIX FILTER PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $departments = Department::where('is_active', 1)->get();
        return view('admin.hr2.training_evaluation', compact('departments'));
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN MATRIX (only if training exists)
    |--------------------------------------------------------------------------
    */
    public function showEvaluation(Request $request)
    {
        $employee_id = $request->employee_id;
        $competency_code = $request->competency_code;

        if (!$employee_id || !$competency_code) {
            return redirect()->route('hr2.training')
                ->with('error', 'Missing parameters.');
        }

        // BLOCK if no training schedule
        $trainingExists = DB::table('training_schedule_hr3')
            ->where('employee_id', $employee_id)
            ->where('competency_code', $competency_code)
            ->exists();

        if (!$trainingExists) {
            return redirect()->route('hr2.training')
                ->with('error', 'Training must be scheduled first in HR3.');
        }

        // BLOCK if already evaluated
        $alreadyEvaluated = EmployeeTrainingScore::where('employee_id', $employee_id)
            ->where('competency_code', $competency_code)
            ->exists();

        if ($alreadyEvaluated) {
            return redirect()->route('hr2.training')
                ->with('error', 'Employee already evaluated.');
        }

        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        $competency = Competency::where('competency_code', $competency_code)->firstOrFail();

        return view('admin.hr2.training_evaluation_matrix', compact('employee', 'competency'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE EVALUATION
    |--------------------------------------------------------------------------
    */
    public function storeEvaluation(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'competency_code' => 'required',
            'scores' => 'required|array'
        ]);

        // BLOCK if no training schedule
        $trainingExists = DB::table('training_schedule_hr3')
            ->where('employee_id', $request->employee_id)
            ->where('competency_code', $request->competency_code)
            ->exists();

        if (!$trainingExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Training must be scheduled before evaluation.'
            ], 422);
        }

        $exists = EmployeeTrainingScore::where('employee_id', $request->employee_id)
            ->where('competency_code', $request->competency_code)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Evaluation already exists.'
            ], 422);
        }

        $evaluator = Employee::where('user_id', Auth::id())->first();
        $evaluatorId = $evaluator ? $evaluator->employee_id : 'ADMIN';

        $total = array_sum($request->scores);

        EmployeeTrainingScore::create([
            'employee_id' => $request->employee_id,
            'competency_code' => $request->competency_code,
            'scores' => json_encode([
                'ratings' => $request->scores,
                'remarks' => $request->remarks ?? []
            ]),
            'total_score' => $total,
            'status' => 'completed',
            'evaluated_by' => $evaluatorId,
            'evaluated_at' => now()
        ]);

        // Update enrollment status
        CompetencyEnroll::where('employee_id', $request->employee_id)
            ->where('competency_code', $request->competency_code)
            ->update([
                'status' => 'completed'
            ]);

        return response()->json([
            'status' => 'success',
            'total_score' => $total
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD ELIGIBLE EMPLOYEES (WITH TRAINING CHECK)
    |--------------------------------------------------------------------------
    */
    public function getEligibleEmployees(Request $request)
    {
        $employees = EmployeeCompetencyCompletion::join('employees', 'employees.employee_id', '=', 'employee_competency_completion_hr2.employee_id')
            ->join('competency_hr2', 'competency_hr2.competency_code', '=', 'employee_competency_completion_hr2.competency_code')

            // TRAINING SCHEDULE (HR3)
            ->leftJoin('training_schedule_hr3', function($join) {
                $join->on('employees.employee_id', '=', 'training_schedule_hr3.employee_id')
                     ->on('competency_hr2.competency_code', '=', 'training_schedule_hr3.competency_code');
            })

            // EVALUATION SCORE
            ->leftJoin('employee_training_scores_hr2', function($join) {
                $join->on('employees.employee_id', '=', 'employee_training_scores_hr2.employee_id')
                     ->on('competency_hr2.competency_code', '=', 'employee_training_scores_hr2.competency_code');
            })

            ->where('competency_hr2.department_id', $request->department_id)
            ->where('competency_hr2.specialization_name', $request->specialization)
            ->where('competency_hr2.competency_code', $request->competency_code)
            ->where('employee_competency_completion_hr2.status', 'completed')

            ->select(
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',

                // training schedule
                'training_schedule_hr3.training_date',
                'training_schedule_hr3.training_time',
                'training_schedule_hr3.venue',

                // evaluation
                'employee_training_scores_hr2.total_score as training_score'
            )
            ->get();

        return response()->json($employees);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX FILTERS
    |--------------------------------------------------------------------------
    */
    public function getSpecializations($dept)
    {
        return response()->json(
            DepartmentSpecialization::where('dept_code', $dept)
                ->where('is_active', 1)
                ->get()
        );
    }

    public function getCompetencies($dept, $spec)
    {
        return response()->json(
            Competency::where('department_id', $dept)
                ->where('specialization_name', $spec)
                ->where('is_active', 1)
                ->get()
        );
    }
}