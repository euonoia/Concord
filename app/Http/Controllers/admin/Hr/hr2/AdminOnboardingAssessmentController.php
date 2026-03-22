<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOnboardingAssessmentController extends Controller
{
    /**
     * Public page (user enters APP-XXXX reference)
     */
    public function index()
    {
        $assessments = DB::table('onboarding_assessments_hr1')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.hr2.onboarding_assessment.index', compact('assessments'));
    }

    /**
     * Check reference ID (APP-OUG9ABP6)
     */
    public function checkReference(Request $request)
    {
        $request->validate([
            'reference_id' => 'required|string|max:50'
        ]);

        $applicant = DB::table('onboarding_assessments_hr1')
            ->where('application_id', $request->reference_id)
            ->first();

        if (!$applicant) {
            return redirect()->back()->with('error','Reference ID not found.');
        }

        return redirect()->route('onboarding.assessment.matrix', $applicant->id);
    }

    /**
     * Show the assessment matrix
     */
    public function matrix($id)
    {
        $applicant = DB::table('onboarding_assessments_hr1')
            ->where('id', $id)
            ->first();

        if (!$applicant) {
            abort(404);
        }

        // Example: load competencies (static or from DB)
        $competencies = [
            'Technical Knowledge',
            'Communication Skills',
            'Problem Solving',
        ];

        return view('admin.hr2.onboarding_assessment.matrix', compact('applicant', 'competencies'));
    }

    /**
     * Submit the assessment
     */
  public function submitAssessment(Request $request, $id)
{
    $applicant = DB::table('onboarding_assessments_hr1')
        ->where('id', $id)
        ->first();

    if (!$applicant) {
        return redirect()->back()->with('error', 'Applicant not found.');
    }

    $ratings = $request->input('ratings', []);
    $remarks = $request->input('remarks', []);

    // Save each competency score into the scores table with application_id
    foreach ($ratings as $competency => $score) {
        DB::table('onboarding_assessment_scores_hr1')->updateOrInsert(
            [
                'applicant_id' => $applicant->id,
                'competency'   => $competency,
            ],
            [
                'application_id' => $applicant->application_id, 
                'rating'         => $score,
                'remarks'        => $remarks[$competency] ?? null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );
    }

    // Update the applicant status using application_id
    DB::table('onboarding_assessments_hr1')
        ->where('application_id', $applicant->application_id)
        ->update([
            'assessment_status' => 'passed',
            'updated_at' => now(),
        ]);

    return redirect()->route('onboarding.assessment.matrix', $id)
        ->with('success', 'Assessment submitted successfully.');
}
}