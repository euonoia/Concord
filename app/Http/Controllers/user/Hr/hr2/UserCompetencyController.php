<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\Competency;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
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

        return view('hr.hr2.competencies', [
            'competencies' => $competencies,
            'completed' => $completed
        ]);
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