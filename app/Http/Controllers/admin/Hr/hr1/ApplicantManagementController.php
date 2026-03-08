<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantManagementController extends Controller
{
    // Ensure only HR/admin can access
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    // List applicants with optional filtering by department/specialization/status
    public function index(Request $request)
    {
        $this->authorizeHr1Admin();

        $filters = $request->only(['department', 'specialization', 'status']);

        $departments = DB::table('departments_hr2')->where('is_active', 1)->orderBy('name')->get();

        // Fetch specializations for the selected department (if any)
        $specializations = collect();
        if (!empty($filters['department'])) {
            $specializations = DB::table('department_specializations_hr2')
                ->where('dept_code', $filters['department'])
                ->where('is_active', 1)
                ->orderBy('specialization_name')
                ->get();
        }

        $query = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name'
            );

        if (!empty($filters['department'])) $query->where('applicants_hr1.department_id', $filters['department']);
        if (!empty($filters['specialization'])) $query->where('applicants_hr1.specialization', $filters['specialization']);
        if (!empty($filters['status'])) $query->where('applicants_hr1.application_status', $filters['status']);

        $applicants = $query->orderByDesc('applicants_hr1.id')->paginate(10);

        return view('admin.hr1.applicants.index', compact('applicants', 'departments', 'specializations', 'filters'));
    }

    // Show individual applicant details
    public function show($id)
    {
        $this->authorizeHr1Admin();

        $applicant = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name'
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
                    'applicant_id'     => $id,
                    'first_name'       => $applicant->first_name,
                    'last_name'        => $applicant->last_name,
                    'email'            => $applicant->email,
                    'phone'            => $applicant->phone,
                    'department_id'    => $applicant->department_id,
                    'specialization'   => $applicant->specialization,
                    'post_grad_status' => $applicant->post_grad_status,
                    'status'           => 'onboarding',
                    'resume_path'      => $applicant->resume_path,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Applicant status updated successfully.');
    }
}