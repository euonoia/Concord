<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\LearningModule; 
use Illuminate\Support\Facades\Auth;

class AdminLearningController extends Controller
{
    /**
     * Reusing your private authorization helper
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        // Fetch courses with counts. 
        // Note: Ensure your model has the enrolls() relationship defined.
        $courses = LearningModule::withCount('enrolls')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.hr2.learning', compact('courses'));
    }

    public function store(Request $request)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'competency_id' => 'nullable|integer',
            'learning_type' => 'nullable|in:Online,Workshop,Seminar,Coaching',
            'duration' => 'nullable|string|max:50',
        ]);

        LearningModule::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'competency_id' => $request->competency_id,
            'learning_type' => $request->learning_type ?? 'Online',
            'duration'      => $request->duration,
        ]);

       
        return redirect()->back()->with('success', 'Learning module added successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeHrAdmin();

        $course = LearningModule::findOrFail($id);
        $course->delete();

        return redirect()->back()->with('success', 'Learning module deleted successfully.');
    }
}