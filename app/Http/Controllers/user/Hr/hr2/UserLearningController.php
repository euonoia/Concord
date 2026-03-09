<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\user\Hr\hr2\CourseEnroll;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserLearningController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        $employeeDept = $employeeRecord->department_id;
        $employeeSpecialization = $employeeRecord->specialization;

        // Get all modules for this employee's dept + specialization
        $modules = LearningModule::where('dept_code', $employeeDept)
            ->where('specialization_name', $employeeSpecialization)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all enrolled module_codes for this employee
        $enrolledModuleCodes = CourseEnroll::where('employee_id', $employeeRecord->employee_id)
            ->where('status', 'enrolled')
            ->pluck('module_code')
            ->toArray();

        return view('hr.hr2.learning', compact('modules', 'employeeRecord', 'enrolledModuleCodes'));
    }

    public function enroll($module_code)
    {
        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        // Only create enrollment if not already enrolled
        CourseEnroll::updateOrCreate(
            [
                'employee_id' => $employeeRecord->employee_id,
                'module_code' => $module_code
            ],
            [
                'status' => 'enrolled',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('user.learning.index')
                         ->with('success', 'Successfully enrolled!');
    }
}