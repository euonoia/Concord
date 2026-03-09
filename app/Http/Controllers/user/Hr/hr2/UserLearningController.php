<?php
namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\user\Hr\hr2\CourseEnroll;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserLearningController extends Controller
{
    // Show available courses for enrollment
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return redirect()->back()->with('error', 'Employee profile not found.');

        $modules = LearningModule::where('dept_code', $employee->department_id)
            ->where('specialization_name', $employee->specialization)
            ->orderBy('created_at', 'desc')
            ->get();

        $enrolledModuleCodes = CourseEnroll::where('employee_id', $employee->employee_id)
            ->where('status', 'enrolled')
            ->pluck('module_code')
            ->toArray();

        return view('hr.hr2.learning', compact('modules', 'enrolledModuleCodes', 'employee'));
    }

    // Enroll in a course
    public function enroll($module_code)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return redirect()->back()->with('error', 'Employee profile not found.');

        $exists = CourseEnroll::where('employee_id', $employee->employee_id)
            ->where('module_code', $module_code)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        CourseEnroll::create([
            'employee_id' => $employee->employee_id,
            'module_code' => $module_code,
            'status'      => 'enrolled',
        ]);

        return redirect()->route('user.learning.index')->with('success', 'Successfully enrolled!');
    }
}