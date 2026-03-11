<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\admin\Hr\hr2\Competency;
use App\Models\Employee;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;

class AdminTrainingController extends Controller
{
    public function index()
    {
        $departments = Department::where('is_active',1)->get();
        return view('admin.hr2.training', compact('departments'));
    }

    public function getSpecializations($dept)
    {
        $specs = DepartmentSpecialization::where('dept_code', $dept)
            ->where('is_active', 1)
            ->orderBy('specialization_name')
            ->get();

        return response()->json($specs);
    }

    public function getCompetencies($dept,$spec)
    {
        $competencies = Competency::where('department_id', $dept)
            ->where('specialization_name', $spec)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return response()->json($competencies);
    }

    public function getEligibleEmployees(Request $request)
    {
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
            ->where('competency_hr2.department_id', $request->department_id)
            ->where('competency_hr2.specialization_name', $request->specialization)
            ->where('competency_hr2.competency_code', $request->competency_code)
            ->where('employee_competency_completion_hr2.status', 'completed')
            ->whereNotNull('employee_competency_completion_hr2.verified_by')
            ->select(
                'employees.employee_id',
                'employees.first_name',
                'employees.last_name',
                'employee_competency_completion_hr2.completed_at'
            )
            ->orderBy('employees.last_name')
            ->get();

        return response()->json($employees);
    }
}