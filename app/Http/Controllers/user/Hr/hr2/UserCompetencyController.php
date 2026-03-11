<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\Competency;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use App\Models\user\Hr\hr2\CompetencyEnroll;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
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

        $competencies = Competency::where('department_id', $employee->department_id)
            ->where('specialization_name', $employee->specialization)
            ->where('is_active', 1)
            ->orderBy('rotation_order')
            ->get();

        $completed = EmployeeCompetencyCompletion::where('employee_id', $employee->employee_id)
            ->pluck('competency_code')
            ->toArray();

        $enrolled = CompetencyEnroll::where('employee_id', $employee->employee_id)
            ->pluck('competency_code')
            ->toArray();

        $scores = EmployeeTrainingScore::where('employee_id', $employee->employee_id)
            ->get()
            ->keyBy('competency_code');

        return view('hr.hr2.competencies', [
            'competencies' => $competencies,
            'completed' => $completed,
            'enrolled' => $enrolled,
            'scores' => $scores
        ]);
    }

    public function enroll($competency_code)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('portal.login');
        }

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error', 'Employee not found');
        }

        $exists = CompetencyEnroll::where('employee_id', $employee->employee_id)
            ->where('competency_code', $competency_code)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Already enrolled in this competency.');
        }

        CompetencyEnroll::create([
            'employee_id' => $employee->employee_id,
            'competency_code' => $competency_code,
            'status' => 'enrolled',
            'enrolled_at' => Carbon::now()
        ]);

        return back()->with('success', 'Competency enrolled successfully.');
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