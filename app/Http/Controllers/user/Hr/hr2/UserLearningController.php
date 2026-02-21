<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\Course;
use App\Models\user\Hr\hr2\CourseEnroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserLearningController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $employee = Auth::user();

        // Eager load only the current user's enrollment record
        $courses = Course::with(['enrolls' => function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        }])->orderBy('created_at', 'desc')->get();

        return view('hr.hr2.learning', compact('courses'));
    }

    public function enroll($id)
    {
        $employee = Auth::user();
        
        $course = Course::findOrFail($id);

        // Prevent duplicate enrollment
        $exists = CourseEnroll::where('employee_id', $employee->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'You are already enrolled.');
        }

        CourseEnroll::create([
            'employee_id' => $employee->id,
            'course_id'   => $course->id,
            'status'      => 'enrolled'
        ]);

        return redirect()->route('user.learning.index')->with('success', 'Enrolled successfully!');
    }
}