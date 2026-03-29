<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAssessmentPerformanceController extends Controller
{
    /**
     * Authorize only HR1 Admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403);
        }
    }

    /**
     * Display a list of all onboarding assessments.
     */
    public function index()
    {
        $this->authorizeHrAdmin();

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

        $totalPassed = DB::table('onboarding_assessments_hr1')
            ->where('assessment_status', 'passed')
            ->count();

        $totalFailed = DB::table('onboarding_assessments_hr1')
            ->where('assessment_status', 'failed')
            ->count();

        $totalAssessed = DB::table('onboarding_assessments_hr1')
            ->where('assessment_status', 'assessed')
            ->count();

        return view(
            'admin.hr1.assessment_performance.index',
            compact('applicants', 'totalPassed', 'totalFailed', 'totalAssessed')
        );
    }

    /**
     * Show detailed competency scores for a specific assessment record.
     */
    public function show($id)
    {
        $this->authorizeHrAdmin();

        $applicant = DB::table('onboarding_assessments_hr1')->where('id', $id)->first();

        if (!$applicant) {
            return redirect()
                ->route('hr1.assessment.performance.index')
                ->with('error', 'Assessment record not found.');
        }

        $scores = DB::table('onboarding_assessment_scores_hr1')
            ->leftJoin('employees as assessors', 'onboarding_assessment_scores_hr1.assessed_by', '=', 'assessors.employee_id')
            ->leftJoin('employees as validators', 'onboarding_assessment_scores_hr1.validated_by', '=', 'validators.employee_id')
            ->where('onboarding_assessment_scores_hr1.application_id', $applicant->application_id)
            ->select(
                'onboarding_assessment_scores_hr1.*',
                DB::raw("CONCAT(assessors.first_name, ' ', assessors.last_name) as assessor_name"),
                DB::raw("CONCAT(validators.first_name, ' ', validators.last_name) as validator_name")
            )
            ->get();

        // Calculate average
        $average = $scores->count() > 0 ? $scores->avg('rating') : 0;

        return view(
            'admin.hr1.assessment_performance.show',
            compact('applicant', 'scores', 'average')
        );
    }

    /**
     * Validate final assessment result
     */
    public function validateAssessment(Request $request, $id)
    {
        $this->authorizeHrAdmin();

        $assessment = DB::table('onboarding_assessments_hr1')->where('id', $id)->first();

        if (!$assessment) {
            return redirect()->back()->with('error', 'Assessment record not found.');
        }

        if ($assessment->assessment_status !== 'assessed') {
            return redirect()->back()->with(
                'error',
                'Cannot validate: HR2 Assessment must be COMPLETED first (Current: ' .
                strtoupper($assessment->assessment_status) .
                ').'
            );
        }

        // Fetch individual scores
        $scores = DB::table('onboarding_assessment_scores_hr1')
            ->where('application_id', $assessment->application_id)
            ->get();

        if ($scores->isEmpty()) {
            return redirect()->back()->with('error', 'No scores found for this applicant.');
        }

        $average = $scores->avg('rating');
        $finalStatus = ($average >= 75) ? 'passed' : 'failed';

        // Get admin employee info
        $admin = DB::table('employees')->where('user_id', Auth::id())->first();
        $adminId = $admin ? $admin->employee_id : 'ADMIN';
        $adminName = Auth::user()->name;

        DB::beginTransaction();

        try {

            // Update main assessment record
            DB::table('onboarding_assessments_hr1')
                ->where('id', $id)
                ->update([
                    'assessment_status' => $finalStatus,
                    'is_validated' => 1,
                    'validated_by' => $adminName,
                    'updated_at' => now(),
                ]);

            // Update individual scores (who validated them)
            DB::table('onboarding_assessment_scores_hr1')
                ->where('application_id', $assessment->application_id)
                ->update([
                    'validated_by' => $adminId,
                    'updated_at' => now()
                ]);

            DB::commit();

            $msg = $finalStatus === 'passed'
                ? 'Assessment scores validated: PASSED (' . number_format($average, 2) . '%).'
                : 'Assessment scores validated: FAILED (' . number_format($average, 2) . '%).';

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with('error', 'Validation failed: ' . $e->getMessage());
        }
    }
}