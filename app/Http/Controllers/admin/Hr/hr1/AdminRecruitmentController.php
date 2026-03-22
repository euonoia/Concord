<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRecruitmentController extends Controller
{
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * List all job postings with a summary card of total needed applicants.
     */
    public function index()
    {
        $this->authorizeHr1Admin();

        $postings = DB::table('job_postings_hr1')
            ->orderByDesc('created_at')
            ->get();

        $hr4Jobs = DB::table('available_jobs_hr4')
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->get();

        $totalNeeded    = $postings->sum('needed_applicants');
        $activeCount    = $postings->where('is_active', 1)->count();
        $inactiveCount  = $postings->where('is_active', 0)->count();

        return view('admin.hr1.recruitment.index', compact(
            'postings',
            'hr4Jobs',
            'totalNeeded',
            'activeCount',
            'inactiveCount'
        ));
    }

    /**
     * Show a single job posting detail.
     */
    public function show($id)
    {
        $this->authorizeHr1Admin();

        $posting = DB::table('job_postings_hr1')->where('id', $id)->first();

        if (!$posting) abort(404);

        return view('admin.hr1.recruitment.show', compact('posting'));
    }

    /**
     * Toggle is_active between 0 and 1.
     */
    public function toggle($id)
    {
        $this->authorizeHr1Admin();

        $posting = DB::table('job_postings_hr1')->where('id', $id)->first();

        if (!$posting) abort(404);

        /** @var object $posting */
        DB::table('job_postings_hr1')
            ->where('id', $id)
            ->update([
                'is_active'  => $posting->is_active ? 0 : 1,
                'updated_at' => now(),
            ]);

        $label = $posting->is_active ? 'deactivated' : 'activated';

        return redirect()->back()->with('success', "Job posting \"{$posting->title}\" has been {$label}.");
    }

    /**
     * Publish a job from an HR4 request.
     */
    public function publishFromHr4($id)
    {
        $this->authorizeHr1Admin();

        /** @var object $hr4Job */
        $hr4Job = DB::table('available_jobs_hr4')->where('id', $id)->first();

        if (!$hr4Job) abort(404, 'HR4 Job Request not found.');

        // Check if already published
        $alreadyPublished = DB::table('job_postings_hr1')->where('hr4_job_id', $id)->exists();
        if ($alreadyPublished) {
            return redirect()->back()->with('error', 'This HR4 job request has already been published.');
        }

        DB::table('job_postings_hr1')->insert([
            'hr4_job_id'        => $hr4Job->id,
            'title'             => $hr4Job->title,
            'track_type'        => 'residency', // Added default track type
            'dept_code'         => $hr4Job->department, // Using dept_code as column name from schema listing
            'description'       => $hr4Job->description,
            'needed_applicants' => $hr4Job->positions_available ?? 0,
            'is_active'         => 1,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return redirect()->back()->with('success', "Job \"{$hr4Job->title}\" published successfully from HR4 request.");
    }
}
