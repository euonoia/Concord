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
        // Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        // Get employee linked to the logged in user
        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        // Correct column names from employees table
        $employeeDept = $employeeRecord->department_id;
        $employeeSpecialization = $employeeRecord->specialization;

        // Get learning modules matching employee department + specialization
        $modules = LearningModule::where('dept_code', $employeeDept)
            ->where('specialization_name', $employeeSpecialization)
            ->with(['enrolls' => function ($query) use ($employeeRecord) {
                $query->where('employee_id', $employeeRecord->employee_id);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.hr2.learning', compact('modules'));
    }

    public function enroll($id)
    {
        try {

            $employeeRecord = Employee::where('user_id', Auth::id())->first();

            if (!$employeeRecord) {
                return redirect()->back()->with('error', 'Employee profile not found.');
            }

            $module = LearningModule::findOrFail($id);

            CourseEnroll::updateOrCreate(
                [
                    'employee_id' => (string) $employeeRecord->employee_id,
                    'module_id'   => $module->id
                ],
                [
                    'assigned_date' => now(),
                    'status' => 'in_progress'
                ]
            );

            return redirect()
                ->route('user.learning.index')
                ->with('success', 'Enrolled in ' . $module->module_name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Enrollment Error: ' . $e->getMessage());
        }
    }
}