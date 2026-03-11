<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Competency;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;

class AdminTrainingEvaluationController extends Controller
{
    public function index() {
        $departments = Department::where('is_active', 1)->get();
        return view('admin.hr2.training_evaluation', compact('departments'));
    }

    public function showEvaluation(Request $request) {
        $employee_id = $request->query('employee_id');
        $competency_code = $request->query('competency_code');

        if (!$employee_id || !$competency_code) {
            return redirect()->back()->with('error', 'Missing parameters.');
        }

        $employee = Employee::where('employee_id', $employee_id)->firstOrFail();
        $competency = Competency::where('competency_code', $competency_code)->firstOrFail();
        
        $existingScores = EmployeeTrainingScore::where('employee_id', $employee_id)
            ->where('competency_code', $competency_code)
            ->first();

        return view('admin.hr2.training_evaluation_matrix', compact('employee', 'competency', 'existingScores'));
    }

    public function storeEvaluation(Request $request) {
        $request->validate([
            'employee_id' => 'required',
            'competency_code' => 'required',
            'scores' => 'required|array'
        ]);

        // Calculate total from the scores array
        $total = array_sum($request->scores);

        // Update or Create the evaluation
        $evaluation = EmployeeTrainingScore::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'competency_code' => $request->competency_code
            ],
            [
                'scores' => json_encode([
                    'ratings' => $request->scores,
                    'remarks' => $request->remarks ?? []
                ]),
                'total_score' => $total,
                'status' => 'completed',
                'evaluated_by' => auth()->user()->employee_id ?? 'ADMIN',
                'evaluated_at' => now()
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Training evaluation saved successfully',
            'total_score' => $total
        ]);
    }

    // Dropdown helpers (Ensure these match your working routes)
    public function getSpecializations($dept) {
        return response()->json(DepartmentSpecialization::where('dept_code', $dept)->get());
    }

    public function getCompetencies($dept, $spec) {
        return response()->json(Competency::where('department_id', $dept)->where('specialization_name', $spec)->get());
    }

    public function getEligibleEmployees(Request $request) {
        // ... paste your existing working getEligibleEmployees logic here ...
    }
}