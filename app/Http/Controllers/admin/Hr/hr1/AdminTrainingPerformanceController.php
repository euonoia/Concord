<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminTrainingPerformanceController extends Controller
{
    /**
     * Display the list of employees for HR1 to review.
     * This is the method the error says is missing.
     */
    public function index(Request $request)
    {
        $departments = \App\Models\admin\Hr\hr2\Department::all();

        $query = Employee::query();

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        $employees = $query->select('employee_id', 'first_name', 'last_name', 'department_id', 'specialization')
                           ->paginate(15)
                           ->withQueryString();

        $specializations = $request->filled('department')
            ? Employee::where('department_id', $request->department)
                      ->distinct()
                      ->pluck('specialization')
            : [];

        return view('admin.hr1.training_performance.index', compact('departments', 'specializations', 'employees'));
    }

    /**
     * AJAX route to get specializations when a department is selected.
     */
    public function getSpecializations($dept)
    {
        $specs = Employee::where('department_id', $dept)
            ->distinct()
            ->pluck('specialization');

        return response()->json($specs);
    }

    /**
     * Detailed view of one employee's training scores.
     */
    public function show($employee_id)
    {
        $scores = EmployeeTrainingScore::query()
            ->leftJoin('employees as evaluators', 'evaluators.employee_id', '=', 'employee_training_scores_hr2.evaluated_by')
            ->leftJoin('competency_hr2 as comp', 'comp.competency_code', '=', 'employee_training_scores_hr2.competency_code')
            ->select(
                'employee_training_scores_hr2.*',
                'comp.name as competency_name',
                'comp.specialization_name as specialization',
                'evaluators.first_name as evaluator_first_name',
                'evaluators.last_name as evaluator_last_name'
            )
            ->where('employee_training_scores_hr2.employee_id', $employee_id)
            ->get();

        // Check if validated
        $isValidated = DB::table('validated_training_performance_hr1')
            ->where('employee_id', $employee_id)
            ->where('status', 'completed')
            ->exists();

        $scores->transform(function ($item) {
            $item->decoded_scores = is_string($item->scores) ? json_decode($item->scores, true) : $item->scores;
            return $item;
        });

        // Normalized Grade Calculation
        $sumOfGrades = $scores->sum(fn($score) => $score->total_score / 4);
        $count = $scores->count();
        $weightedAverage = $count > 0 ? round($sumOfGrades / $count, 2) : 0;

        return view('admin.hr1.training_performance.show', compact('scores', 'weightedAverage', 'employee_id', 'isValidated'));
    }

    /**
     * Finalize and store the grade into the HR1 validation table.
     */
    public function validateAndStore(Request $request, $employee_id)
    {
        $scores = EmployeeTrainingScore::where('employee_id', $employee_id)->get();
        $count = $scores->count();

        if ($count === 0) return redirect()->back()->with('error', 'No scores to validate.');

        $totalPoints = $scores->sum('total_score');
        $maxPoints = $count * 400;
        $finalGrade = ($totalPoints / $maxPoints) * 100;

        $evaluator = Employee::where('user_id', Auth::id())->first();
        $evaluatorId = $evaluator ? $evaluator->employee_id : 'ADMIN';

        DB::table('validated_training_performance_hr1')->updateOrInsert(
            ['employee_id' => $employee_id],
            [
                'weighted_average' => round($finalGrade, 2), 
                'status'           => 'completed',
                'evaluated_by'     => $evaluatorId,
                'evaluated_at'     => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );

        return redirect()->back()->with('success', "Grade " . round($finalGrade, 2) . "% stored successfully.");
    }
}