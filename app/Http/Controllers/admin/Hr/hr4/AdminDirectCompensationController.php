<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr4\AvailableJob;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr3\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDirectCompensationController extends Controller
{
    /**
     * Ensure user is HR4 admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized access to HR4 Direct Compensation.');
        }
    }

    /**
     * Show all direct compensations for a given month
     */
    public function index(Request $request)
    {
        $this->authorizeHrAdmin(); // <-- Role check

        $month = $request->query('month', date('Y-m'));

        $compensations = DirectCompensation::with('employee')
            ->where('month', $month)
            ->orderBy('employee_id')
            ->get();

        return view('admin.hr4.compensations', compact('compensations', 'month'));
    }

    /**
     * Generate monthly compensation for all employees
     */
    public function generate(Request $request)
    {
        $this->authorizeHrAdmin(); 

        $month = $request->input('month', date('Y-m'));
        $employees = Employee::all();

        foreach ($employees as $emp) {
            $position = DepartmentPositionTitle::find($emp->position_id);

            $base_salary = $position->base_salary ?? 0;
            $shift_allowance = Shift::calculateMonthlyShiftAllowance($emp->employee_id, $month);
            $bonus = 0;

            DirectCompensation::updateOrCreate(
                ['employee_id' => $emp->employee_id, 'month' => $month],
                [
                    'base_salary' => $base_salary,
                    'shift_allowance' => $shift_allowance,
                    'bonus' => $bonus
                ]
            );
        }

        return redirect()->back()->with('success', "Monthly compensation generated for {$month}.");
    }

    /**
     * Show job postings
     */
    public function jobPostingsIndex()
    {
        $this->authorizeHrAdmin();

        $jobPostings = AvailableJob::with('poster')
            ->leftJoin('departments_hr2', 'available_jobs_hr4.department', '=', 'departments_hr2.department_id')
            ->select('available_jobs_hr4.*', 'departments_hr2.name as department_name')
            ->orderBy('available_jobs_hr4.created_at', 'desc')
            ->get();

        return view('admin.hr4.job_postings', compact('jobPostings'));
    }

    /**
     * Show create job posting form
     */
    public function createJobPosting()
    {
        $this->authorizeHrAdmin();

        $departments = DB::table('departments_hr2')->where('is_active', 1)->orderBy('name')->get();
        $positions = DB::table('department_position_titles_hr2')->where('is_active', 1)->orderBy('position_title')->get();

        return view('admin.hr4.create_job_posting', compact('departments', 'positions'));
    }

    /**
     * Store job posting
     */
    public function storeJobPosting(Request $request)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:255',
        ]);

        AvailableJob::create([
            'title' => $request->title,
            'department' => $request->department,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'posted_by' => Auth::id(),
            'posted_at' => now(),
        ]);

        return redirect()->route('hr4.job_postings.index')->with('success', 'Available job added successfully.');
    }

    /**
     * Show job posting details
     */
    public function showJobPosting(AvailableJob $jobPosting)
    {
        $this->authorizeHrAdmin();

        $jobPosting->load('poster');

        return view('admin.hr4.show_job_posting', compact('jobPosting'));
    }

    /**
     * Show edit job posting form
     */
    public function editJobPosting(AvailableJob $jobPosting)
    {
        $this->authorizeHrAdmin();

        $departments = DB::table('departments_hr2')->where('is_active', 1)->orderBy('name')->get();
        $positions = DB::table('department_position_titles_hr2')->where('is_active', 1)->orderBy('position_title')->get();

        return view('admin.hr4.edit_job_posting', compact('jobPosting', 'departments', 'positions'));
    }

    /**
     * Update job posting
     */
    public function updateJobPosting(Request $request, AvailableJob $jobPosting)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'salary_range' => 'nullable|string|max:255',
            'status' => 'required|in:open,closed',
        ]);

        $jobPosting->update([
            'title' => $request->title,
            'department' => $request->department,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'status' => $request->status,
        ]);

        return redirect()->route('hr4.job_postings.index')->with('success', 'Available job updated successfully.');
    }

    /**
     * Archive job posting (soft delete by setting status to closed)
     */
    public function archiveJobPosting(AvailableJob $jobPosting)
    {
        $this->authorizeHrAdmin();

        $jobPosting->update(['status' => 'closed']);

        return redirect()->route('hr4.job_postings.index')->with('success', 'Available job archived successfully.');
    }
}