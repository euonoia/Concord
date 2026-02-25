<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
// Note: Ensure this path matches where you saved the Admin model
use App\Models\admin\Hr\hr2\LearningModule; 
use App\Models\user\Hr\hr2\CourseEnroll;
use App\Models\Employee; // Better to use Employee record for ID consistency
use Illuminate\Support\Facades\Auth;

class UserLearningController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        // Get the specific employee record (to match your training logic)
        $employeeRecord = Employee::where('user_id', Auth::id())->first();

        if (!$employeeRecord) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        // Fetch Learning Modules and check if the user is already enrolled
        $modules = LearningModule::with(['enrolls' => function ($query) use ($employeeRecord) {
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
            $module = LearningModule::findOrFail($id);

            // Using the same string-safe logic we used for training
            CourseEnroll::updateOrCreate(
                [
                    'employee_id' => (string) $employeeRecord->employee_id,
                    'course_id'   => (string) $module->id, // This matches the 'course_id' in enrolls table
                ],
                [
                    'status' => 'enrolled'
                ]
            );

            return redirect()->route('user.learning.index')->with('success', 'Enrolled in ' . $module->title);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Enrollment Error: ' . $e->getMessage());
        }
    }
}