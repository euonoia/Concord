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
    | ONLY GET COMPLETED MODULES
    |--------------------------------------------------------------------------
    */
    $completedModules = CourseEnroll::where('employee_id', $employee->employee_id)
        ->where('status', 'completed') 
        ->pluck('module_code');

    /*
    |--------------------------------------------------------------------------
    | GET MATERIALS
    |--------------------------------------------------------------------------
    */
    $materials = LearningMaterial::whereIn('module_code', $completedModules)
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
    | Check if module is COMPLETED
    |--------------------------------------------------------------------------
    */
    $completed = CourseEnroll::where('employee_id', $employee->employee_id)
        ->where('module_code', $module_code)
        ->where('status', 'completed')
        ->exists();

    if (!$completed) {
        return redirect()->back()->with('error', 'You can only access completed modules.');
    }

    /*
    |--------------------------------------------------------------------------
    | Get materials
    |--------------------------------------------------------------------------
    */
    $materials = LearningMaterial::where('module_code', $module_code)
        ->orderBy('created_at', 'asc')
        ->get();

    return view('hr.hr2.user_learning.index', compact('materials', 'employee', 'module_code'));
}
}