<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\Competency;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use App\Models\user\Hr\hr2\CompetencyEnroll;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Carbon\Carbon;

class UserCompetencyController extends Controller
{
 public function index()
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('portal.login');
    }

    $employee = Employee::where('user_id', $user->id)->first();

    if (!$employee) {
        return view('hr.hr2.competencies', [
            'competencies' => collect(),
            'error' => 'Employee record not found.'
        ]);
    }

    $competencies = Competency::join('employee_training_scores_hr2 as s', function($join) use ($employee) {
            $join->on('competency_hr2.competency_code', '=', 's.competency_code')
                 ->where('s.employee_id', '=', $employee->employee_id);
        })
        // This Join finds the Evaluator's Name
        ->leftJoin('employees as evaluator', 's.evaluated_by', '=', 'evaluator.employee_id') 
        ->where('competency_hr2.department_id', $employee->department_id)
        ->where('competency_hr2.specialization_name', $employee->specialization)
        ->where('competency_hr2.is_active', 1)
        ->select(
            'competency_hr2.name',
            'competency_hr2.description',
            's.total_score',
            's.evaluated_by',
            // Concatenate the evaluator's name into a single field
            DB::raw("CONCAT(evaluator.first_name, ' ', evaluator.last_name) as evaluator_name")
        )
        ->orderBy('competency_hr2.rotation_order')
        ->get();

    return view('hr.hr2.competencies', compact('competencies'));
}
    public function complete($competency_code)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('portal.login');
        }

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee not found');
        }

        EmployeeCompetencyCompletion::create([
            'employee_id' => $employee->employee_id,
            'competency_code' => $competency_code,
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ]);

        return back()->with('success', 'Competency marked as completed.');
    }
}