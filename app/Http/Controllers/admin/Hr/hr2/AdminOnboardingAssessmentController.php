<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminOnboardingAssessmentController extends Controller
{
    /**
     * Allow only HR2
     */
    private function authorizeHr2()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * LIST OF APPLICANTS
     */
    public function index()
    {
        $this->authorizeHr2();

        $assessments = DB::table('onboarding_assessments_hr1')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $validatedAssessments = DB::table('onboarding_assessment_scores_hr1 as s')
            ->join('onboarding_assessments_hr1 as a', 's.applicant_id', '=', 'a.id')
            ->leftJoin('employees as e', 's.validated_by', '=', 'e.employee_id')
            ->whereNotNull('s.validated_by')
            ->select(
                'a.application_id',
                'a.first_name',
                'a.last_name',
                's.competency',
                's.rating',
                's.remarks',
                DB::raw('CONCAT(e.first_name, " ", e.last_name) as validator_name'),
                's.updated_at'
            )
            ->orderBy('s.updated_at', 'desc')
            ->get();

        return view('admin.hr2.onboarding_assessment.index', compact('assessments', 'validatedAssessments'));
    }

    /**
     * CHECK REFERENCE ID
     */
    public function checkReference(Request $request)
    {
        $this->authorizeHr2();

        $request->validate([
            'reference_id' => 'required|string|max:50'
        ]);

        $applicant = DB::table('onboarding_assessments_hr1')
            ->where('application_id', $request->reference_id)
            ->first();

        if (!$applicant) {
            return redirect()->back()->with('error','Reference ID not found.');
        }

        if ($applicant->assessment_status === 'assessed') {
            return redirect()->back()->with('info','This applicant has already been assessed.');
        }

        return redirect()->route('onboarding.assessment.matrix', $applicant->id);
    }

    /**
     * SHOW ASSESSMENT MATRIX
     */
    public function matrix($id)
    {
        $this->authorizeHr2();

        $applicant = DB::table('onboarding_assessments_hr1')
            ->where('id', $id)
            ->first();

        if (!$applicant) {
            abort(404);
        }

        $competencies = [
            'Technical Knowledge',
            'Communication Skills',
            'Problem Solving',
        ];

        return view('admin.hr2.onboarding_assessment.matrix', compact('applicant', 'competencies'));
    }

    /**
     * SUBMIT ASSESSMENT
     */
    public function submitAssessment(Request $request, $id)
    {
        $this->authorizeHr2();

        $applicant = DB::table('onboarding_assessments_hr1')
            ->where('id', $id)
            ->first();

        if (!$applicant) {
            return redirect()->back()->with('error', 'Applicant not found.');
        }

        $admin = DB::table('employees')->where('user_id', Auth::id())->first();
        $adminId = $admin ? $admin->employee_id : null;

        $ratings = $request->input('ratings', []);

        $weights = [
            'Technical Knowledge'   => 0.40,
            'Communication Skills'  => 0.30,
            'Problem Solving'       => 0.30,
        ];

        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($ratings as $competency => $score) {
            if (isset($weights[$competency])) {
                $totalWeightedScore += $score * $weights[$competency];
                $totalWeight += $weights[$competency];
            }
        }

        $finalAverage = $totalWeight > 0 ? $totalWeightedScore / $totalWeight : 0;

        // Determine level + remarks
        if ($finalAverage >= 90) {
            $level = 'Advanced';
            $autoRemarks = 'Excellent performance — highly competent.';
        } elseif ($finalAverage >= 75) {
            $level = 'Intermediate';
            $autoRemarks = 'Good performance — meets expectations.';
        } elseif ($finalAverage >= 60) {
            $level = 'Basic';
            $autoRemarks = 'Satisfactory performance — needs improvement in some areas.';
        } else {
            $level = 'Beginner';
            $autoRemarks = 'Insufficient performance — significant improvement required.';
        }

        // Save each competency
        foreach ($ratings as $competency => $score) {
            DB::table('onboarding_assessment_scores_hr1')->updateOrInsert(
                [
                    'applicant_id' => $applicant->id,
                    'competency'   => $competency
                ],
                [
                    'application_id' => $applicant->application_id,
                    'rating'         => $score,
                    'remarks'        => $autoRemarks,
                    'assessed_by'    => $adminId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            );
        }

        // Update assessment status
        DB::table('onboarding_assessments_hr1')
            ->where('id', $applicant->id)
            ->update([
                'assessment_status' => 'assessed',
                'assessed_by'       => $adminId,
                'updated_at'        => now(),
            ]);

        return redirect()
            ->route('onboarding.assessment.index')
            ->with('success', 'Assessment submitted successfully.');
    }
}