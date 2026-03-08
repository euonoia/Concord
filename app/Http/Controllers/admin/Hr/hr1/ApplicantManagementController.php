<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantManagementController extends Controller
{
    // Ensure only HR/admin can access
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    // List applicants with optional filtering by department/position/status
    public function index(Request $request)
    {
        $this->authorizeHr1Admin();

        $filters = $request->only(['department', 'position', 'status']);

        $departments = DB::table('departments_hr2')->where('is_active', 1)->orderBy('name')->get();
        $positions = DB::table('department_position_titles_hr2')->where('is_active', 1)->orderBy('position_title')->get();

        $query = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('department_position_titles_hr2', 'applicants_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
            );

        if (!empty($filters['department'])) $query->where('applicants_hr1.department_id', $filters['department']);
        if (!empty($filters['position'])) $query->where('applicants_hr1.position_id', $filters['position']);
        if (!empty($filters['status'])) $query->where('applicants_hr1.application_status', $filters['status']);

        $applicants = $query->orderByDesc('applicants_hr1.id')->paginate(10);

        return view('admin.hr1.applicants.index', compact('applicants', 'departments', 'positions', 'filters'));
    }

    // Show individual applicant details
    public function show($id)
    {
        $this->authorizeHr1Admin();

        $applicant = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('department_position_titles_hr2', 'applicants_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
            )
            ->where('applicants_hr1.id', $id)
            ->first();

        if (!$applicant) abort(404);

        return view('admin.hr1.applicants.show', compact('applicant'));
    }

    // Download CV
    public function downloadResume($id)
    {
        $this->authorizeHr1Admin();

        $applicant = DB::table('applicants_hr1')->where('id', $id)->first();
        if (!$applicant || !$applicant->resume_path) abort(404, 'Resume not found.');

        $compressed = Storage::disk('public')->get($applicant->resume_path);
        $pdfContent = gzdecode($compressed);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="resume.pdf"');
    }

    // Update status and optionally create new hire record
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeHr1Admin();

        $request->validate([
            'application_status' => 'required|in:pending,under_review,interview,accepted,rejected,onboarded',
        ]);

        DB::table('applicants_hr1')
            ->where('id', $id)
            ->update([
                'application_status' => $request->application_status,
                'updated_at' => now(),
            ]);

        // If status is accepted, create a new hire record if it doesn't exist
        if ($request->application_status === 'accepted') {
            $applicant = DB::table('applicants_hr1')->where('id', $id)->first();
            $existingHire = DB::table('new_hires_hr1')->where('applicant_id', $id)->first();

            if (!$existingHire && $applicant) {
                DB::table('new_hires_hr1')->insert([
                    'applicant_id'   => $id,
                    'first_name'     => $applicant->first_name,
                    'last_name'      => $applicant->last_name,
                    'email'          => $applicant->email,
                    'phone'          => $applicant->phone,
                    'department_id'  => $applicant->department_id,
                    'position_id'    => $applicant->position_id,
                    'specialization' => $applicant->specialization,
                    'post_grad_status' => $applicant->post_grad_status,
                    'status'         => 'onboarding',
                    'resume_path'    => $applicant->resume_path,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Applicant status updated successfully.');
    }
}