<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\Hr\hr1\OnboardingAssessment;

class AdminOnboardingAssessmentController extends Controller
{
    private function authorizeHr2Admin()
    {
        if (!Auth::check() || (Auth::user()->role_slug !== 'admin_hr2' && Auth::user()->role_slug !== 'admin_hr1')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHr2Admin();

        $query = OnboardingAssessment::query();

        if ($request->filled('status')) {
            $query->where('assessment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $assessments = $query->orderByDesc('created_at')->paginate(10);

        return view('admin.hr2.onboarding_assessments.index', compact('assessments'));
    }

    public function show($id)
    {
        $this->authorizeHr2Admin();
        $assessment = OnboardingAssessment::findOrFail($id);
        return view('admin.hr2.onboarding_assessments.show', compact('assessment'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeHr2Admin();

        $request->validate([
            'assessment_status' => 'required|in:pending,scheduled,passed,failed',
            'interview_date' => 'nullable|date',
            'interviewer' => 'nullable|string|max:150',
            'remarks' => 'nullable|string',
        ]);

        $assessment = OnboardingAssessment::findOrFail($id);
        $assessment->update($request->all());

        return redirect()->route('admin.hr2.onboarding_assessments.index')->with('success', 'Assessment updated successfully.');
    }
}
