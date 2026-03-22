<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminHr1DashboardController extends Controller
{
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHr1Admin();

        // Count metrics for cards
        $totalApplicants = DB::table('applicants_hr1')->count();
        $acceptedCount  = DB::table('applicants_hr1')->whereIn('application_status', ['accepted', 'onboarded'])->count();
        $rejectedCount  = DB::table('applicants_hr1')->where('application_status', 'rejected')->count();
        $activeJobs     = DB::table('job_postings_hr1')->where('is_active', 1)->count();

        // Data for Status Chart
        $statusCounts = DB::table('applicants_hr1')
            ->select('application_status', DB::raw('count(*) as total'))
            ->groupBy('application_status')
            ->get();

        // Data for Department Chart
        $deptCounts = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->select('departments_hr2.name as dept_name', DB::raw('count(*) as total'))
            ->groupBy('departments_hr2.name')
            ->get();

        // Recent Applications
        $recentApplicants = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->select('applicants_hr1.*', 'departments_hr2.name as department_name')
            ->orderByDesc('applicants_hr1.created_at')
            ->limit(5)
            ->get();

        return view('admin.hr1.dashboard', compact(
            'totalApplicants',
            'acceptedCount',
            'rejectedCount',
            'activeJobs',
            'statusCounts',
            'deptCounts',
            'recentApplicants'
        ));
    }
}
