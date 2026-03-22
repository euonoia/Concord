<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAssessmentPerformanceController extends Controller
{
    /**
     * Display a list of all onboarding assessments.
     */
    public function index()
    {
        // We select from the assessments table directly as it contains the names/info
        $applicants = DB::table('onboarding_assessments_hr1')
            ->select(
                'id',
                'applicant_id',
                'application_id',
                'first_name',
                'last_name',
                'email',
                'specialization',
                'assessment_status',
                'is_validated'
            )
            ->paginate(15);

        return view('admin.hr1.assessment_performance.index', compact('applicants'));
    }

    /**
     * Show detailed competency scores for a specific assessment record.
     */
    public function show($id)
    {
        // Get the assessment master record
        $applicant = DB::table('onboarding_assessments_hr1')->where('id', $id)->first();
        
        if (!$applicant) {
            return redirect()->route('hr1.assessment.performance.index')->with('error', 'Assessment record not found.');
        }

        // Get the individual competency scores using the applicant_id or application_id
        $scores = DB::table('onboarding_assessment_scores_hr1')
            ->where('application_id', $applicant->application_id)
            ->get();

        // Calculate Average
        $average = $scores->count() > 0 ? $scores->avg('rating') : 0;

        return view('admin.hr1.assessment_performance.show', compact('applicant', 'scores', 'average'));
    }

    /**
     * Finalize and validate the assessment.
     */
    public function validateAssessment(Request $request, $id)
    {
        // Update validation status in the onboarding_assessments_hr1 table
        DB::table('onboarding_assessments_hr1')
            ->where('id', $id)
            ->update([
                'is_validated' => 1,
                'validated_by' => Auth::id(), // ID of the HR Admin currently logged in
                'updated_at'   => now(),
            ]);

        return redirect()->back()->with('success', 'Assessment scores have been officially validated.');
    }
}