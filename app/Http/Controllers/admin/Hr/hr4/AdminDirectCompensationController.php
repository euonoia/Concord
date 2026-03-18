<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr4\AvailableJob;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\admin\Hr\hr4\TrainingPerformance;
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

        $compensations = DirectCompensation::with(['employee.department', 'employee.position', 'user'])
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

            // Training reward is now calculated dynamically in the model
            // based on latest HR1 training performance data

            DirectCompensation::updateOrCreate(
                ['employee_id' => $emp->employee_id, 'month' => $month],
                [
                    'base_salary' => $base_salary,
                    'shift_allowance' => $shift_allowance,
                    'bonus' => $bonus,
                ]
            );
        }

        return redirect()->back()->with('success', "Monthly compensation generated for {$month}.");
    }

    /**
     * Calculate training reward based on HR1 performance data
     * Note: Training rewards are now calculated dynamically in the DirectCompensation model
     */
    private function calculateTrainingReward($employee_id, $month)
    {
        // This method is kept for backward compatibility but training rewards
        // are now calculated dynamically in the DirectCompensation model
        // based on the latest HR1 training performance data
        return 0;
    }

    /**
     * Show training rewards management page
     */
    public function trainingRewardsIndex()
    {
        $this->authorizeHrAdmin();

        // Get all training performances from HR1
        $trainingPerformances = TrainingPerformance::with('employee')
            ->where('status', 'completed')
            ->orderBy('evaluated_at', 'desc')
            ->paginate(20);

        return view('admin.hr4.training_rewards', compact('trainingPerformances'));
    }

    /**
     * Show training rewards for a specific employee
     */
    public function showEmployeeTrainingRewards(Employee $employee)
    {
        $this->authorizeHrAdmin();

        // Get all training performances for this employee
        $trainingPerformances = TrainingPerformance::where('employee_id', $employee->employee_id)
            ->where('status', 'completed')
            ->orderBy('evaluated_at', 'desc')
            ->get();

        // Calculate rewards for each training
        $trainingRewards = $trainingPerformances->map(function ($training) {
            $grade = $training->weighted_average ?? 0;

            if ($grade >= 95) {
                $reward_amount = 5000;
                $performance_level = 'Excellent';
            } elseif ($grade >= 90) {
                $reward_amount = 3000;
                $performance_level = 'Very Good';
            } elseif ($grade >= 85) {
                $reward_amount = 2000;
                $performance_level = 'Good';
            } elseif ($grade >= 80) {
                $reward_amount = 1000;
                $performance_level = 'Satisfactory';
            } else {
                $reward_amount = 0;
                $performance_level = 'Below Satisfactory';
            }

            return [
                'training' => $training,
                'grade' => $grade,
                'reward_amount' => $reward_amount,
                'performance_level' => $performance_level,
                'month' => date('Y-m', strtotime($training->training_date))
            ];
        });

        // Fetch needed positions (active, required_count > 0)
        $neededPositions = \DB::table('department_position_titles_hr2 as dpt')
            ->join('departments_hr2 as d', 'dpt.department_id', '=', 'd.department_id')
            ->leftJoin('department_specializations_hr2 as ds', function($join) {
                $join->on('dpt.specialization_name', '=', 'ds.specialization_name')
                    ->on('dpt.department_id', '=', 'ds.dept_code');
            })
            ->select(
                'd.name as department_name',
                'ds.specialization_name',
                'dpt.position_title',
                'dpt.rank_level',
                'dpt.employee_type',
                'dpt.base_salary',
                'dpt.required_count'
            )
            ->where('dpt.is_active', 1)
            ->where('dpt.required_count', '>', 0)
            ->orderBy('d.name')
            ->orderBy('ds.specialization_name')
            ->orderBy('dpt.position_title')
            ->get();

        return view('admin.hr4.employee_training_rewards', compact('employee', 'trainingRewards', 'trainingPerformances', 'neededPositions'));
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
            'positions_available' => 'required|integer|min:1',
        ]);

        AvailableJob::create([
            'title' => $request->title,
            'department' => $request->department,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'positions_available' => $request->positions_available,
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
            'positions_available' => 'required|integer|min:1',
            'status' => 'required|in:open,closed',
        ]);

        $jobPosting->update([
            'title' => $request->title,
            'department' => $request->department,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'salary_range' => $request->salary_range,
            'positions_available' => $request->positions_available,
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