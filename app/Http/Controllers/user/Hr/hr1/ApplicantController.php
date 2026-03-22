<?php

namespace App\Http\Controllers\user\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ApplicantController extends Controller
{
    /**
     * Show residency application form
     */
    public function showApplicationForm(Request $request)
    {
        $dept = $request->query('dept', null);
        $jobId = $request->query('job_id', null);

        // Get all active departments
        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->get();

        $specializations = collect();

        if ($dept) {
            // Exclude specific specializations
            $excludedSpecializations = [
                'General Internal Medicine',
                'General Pediatrics',
                'General Psychiatry',
                'General Neurology',
                'General Pathology',
                'General Radiology',
            ];

            $specializations = DB::table('department_specializations_hr2')
                ->where('dept_code', $dept)
                ->where('is_active', 1)
                ->whereNotIn('specialization_name', $excludedSpecializations)
                ->get();
        }

        return view('hr.hr1.apply', compact('departments', 'specializations', 'dept', 'jobId'));
    }

    /**
     * Store submitted residency application
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'department_id'  => 'required',
            'specialization' => 'nullable|string|max:255',
            'resume'         => 'required|mimes:pdf|max:5120',
            'job_posting_id' => 'nullable|exists:job_postings_hr1,id',
        ]);

        $resumePath = null;

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');

            // Compress resume before storing
            $contents = file_get_contents($file->getRealPath());
            $compressed = gzencode($contents, 9);
            $filename = 'resumes/' . Str::uuid() . '.pdf.gz';

            Storage::disk('public')->put($filename, $compressed);
            $resumePath = $filename;
        }

        try {
            DB::table('applicants_hr1')->insert([
                'application_id'     => 'APP-' . Str::upper(Str::random(8)),
                'job_posting_id'     => $request->job_posting_id,
                'first_name'         => $request->first_name,
                'last_name'          => $request->last_name,
                'email'              => $request->email,
                'phone'              => $request->phone,
                'department_id'      => $request->department_id,
                'specialization'     => $request->specialization,
                'post_grad_status'   => 'residency',
                'resume_path'        => $resumePath,
                'application_status' => 'pending',
                'applied_at'         => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            Log::info('Applicant submitted: ' . $request->email);

            return redirect()
                ->back()
                ->with('success', 'Application submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Application failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to submit application. Please try again.')
                ->withInput();
        }
    }
}