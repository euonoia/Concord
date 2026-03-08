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

        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->get();

        $positions = collect();
        $specializations = collect();

        if ($dept) {
            $positions = DB::table('department_position_titles_hr2')
                ->where('department_id', $dept)
                ->where('is_active', 1)
                ->get();

            $specializations = DB::table('department_specializations_hr2')
                ->where('dept_code', $dept)
                ->where('is_active', 1)
                ->get();
        }

        return view('hr.hr1.apply', compact('departments', 'positions', 'specializations', 'dept'));
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
            'position_id'    => 'required',
            'specialization' => 'nullable|string|max:255',
            'resume'         => 'required|mimes:pdf|max:5120',
        ]);

        $resumePath = null;

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');

            $contents = file_get_contents($file->getRealPath());
            $compressed = gzencode($contents, 9);
            $filename = 'resumes/' . Str::uuid() . '.pdf.gz';

            Storage::disk('public')->put($filename, $compressed);
            $resumePath = $filename;
        }

        try {
            DB::table('applicants_hr1')->insert([
                'application_id'     => 'APP-' . Str::upper(Str::random(8)),
                'first_name'         => $request->first_name,
                'last_name'          => $request->last_name,
                'email'              => $request->email,
                'phone'              => $request->phone,
                'department_id'      => $request->department_id,
                'position_id'        => $request->position_id,
                'specialization'     => $request->specialization,
                'post_grad_status'   => 'residency',
                'resume_path'        => $resumePath,
                'application_status' => 'pending',
                'applied_at'         => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Log for debugging
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