<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\CourseEnroll;
use App\Models\admin\Hr\hr2\LearningMaterial;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserLearningMaterialsController extends Controller
{
    // Show list of materials for enrolled courses
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Completed competencies
        |--------------------------------------------------------------------------
        */
        $completedCompetencies = EmployeeCompetencyCompletion::where('employee_id', $employee->employee_id)
            ->pluck('competency_code')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Evaluated competencies
        |--------------------------------------------------------------------------
        */
        $evaluatedCompetencies = EmployeeTrainingScore::where('employee_id', $employee->employee_id)
            ->pluck('competency_code')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Fully finished competencies
        |--------------------------------------------------------------------------
        */
        $finishedCompetencies = array_intersect($completedCompetencies, $evaluatedCompetencies);

        /*
        |--------------------------------------------------------------------------
        | Get modules tied to those competencies
        |--------------------------------------------------------------------------
        */
        $finishedModules = LearningModule::whereIn('competency_code', $finishedCompetencies)
            ->pluck('module_code')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Get enrolled courses EXCEPT finished modules
        |--------------------------------------------------------------------------
        */
        $enrolledCourses = CourseEnroll::where('employee_id', $employee->employee_id)
            ->where('status', 'enrolled')
            ->whereNotIn('module_code', $finishedModules)
            ->pluck('module_code');

        /*
        |--------------------------------------------------------------------------
        | Fetch materials only for active modules
        |--------------------------------------------------------------------------
        */
        $materials = LearningMaterial::whereIn('module_code', $enrolledCourses)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('hr.hr2.user_learning.index', compact('materials', 'employee'));
    }

    // Show single module content
    public function showModule($module_code)
    {
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify enrollment
        |--------------------------------------------------------------------------
        */
        $enrolled = CourseEnroll::where('employee_id', $employee->employee_id)
            ->where('module_code', $module_code)
            ->exists();

        if (!$enrolled) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        /*
        |--------------------------------------------------------------------------
        | Get module competency
        |--------------------------------------------------------------------------
        */
        $module = LearningModule::where('module_code', $module_code)->first();

        if (!$module) {
            return redirect()->back()->with('error', 'Module not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Check if competency already finished
        |--------------------------------------------------------------------------
        */
        $completed = EmployeeCompetencyCompletion::where('employee_id', $employee->employee_id)
            ->where('competency_code', $module->competency_code)
            ->exists();

        $evaluated = EmployeeTrainingScore::where('employee_id', $employee->employee_id)
            ->where('competency_code', $module->competency_code)
            ->exists();

        if ($completed && $evaluated) {
            return redirect()->route('user.learning.index')
                ->with('info', 'This competency is already completed and evaluated.');
        }

        /*
        |--------------------------------------------------------------------------
        | Show materials
        |--------------------------------------------------------------------------
        */
        $materials = LearningMaterial::where('module_code', $module_code)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('hr.hr2.user_learning.index', compact('materials', 'employee', 'module_code'));
    }
}