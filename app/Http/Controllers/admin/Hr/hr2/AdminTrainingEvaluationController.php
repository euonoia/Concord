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

class AdminTrainingEvaluationController extends Controller
{
    public function index() 
    {
        $departments = Department::where('is_active', 1)->get();
        return view('admin.hr2.training_evaluation', compact('departments'));
    }

    public function showEvaluation(Request $request) 
    {
        $employee_id = $request->query('employee_id');
        $competency_code = $request->query('competency_code');

        if (!$employee_id || !$competency_code) {
            return redirect()->back()->with('error', 'Missing parameters.');
        }

        $exists = EmployeeTrainingScore::where('employee_id', $employee_id)
            ->where('competency_code', $competency_code)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Employee already evaluated.');
        }

        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        $competency = Competency::where('competency_code', $competency_code)->firstOrFail();
        
        return view('admin.hr2.training_evaluation_matrix', compact('employee', 'competency'));
    }
   public function storeEvaluation(Request $request) 
{
    $request->validate([
        'employee_id' => 'required',
        'competency_code' => 'required',
        'scores' => 'required|array'
    ]);

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

    /*
    |--------------------------------------------------------------------------
    | Update competency_enroll_hr2 status
    |--------------------------------------------------------------------------
    */

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

    public function getEligibleEmployees(Request $request)
    {
        $employees = EmployeeCompetencyCompletion::join('employees', 'employees.employee_id', '=', 'employee_competency_completion_hr2.employee_id')
            ->join('competency_hr2', 'competency_hr2.competency_code', '=', 'employee_competency_completion_hr2.competency_code')
            
            // Join with scores table
            ->leftJoin('employee_training_scores_hr2', function($join) {
                $join->on('employees.employee_id', '=', 'employee_training_scores_hr2.employee_id')
                     ->on('competency_hr2.competency_code', '=', 'employee_training_scores_hr2.competency_code');
            })
            
            // Join again with employees to get Evaluator's Name via their Employee ID
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
                'employee_training_scores_hr2.total_score as training_score',
                'employee_training_scores_hr2.evaluated_by',
                'evaluator.first_name as eval_fname', 
                'evaluator.last_name as eval_lname'
            )
            ->orderBy('employees.last_name')
            ->get();

        return response()->json($employees);
    }

    public function getSpecializations($dept) {
        return response()->json(DepartmentSpecialization::where('dept_code', $dept)->where('is_active', 1)->get());
    }

    public function getCompetencies($dept, $spec) {
        return response()->json(Competency::where('department_id', $dept)->where('specialization_name', $spec)->where('is_active', 1)->get());
    }
}