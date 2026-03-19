<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\User;
use App\Models\admin\Hr\hr4\HiredUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\admin\Hr\hr4\AvailableJob;

class AdminCoreHumanCapitalController extends Controller
{
    /**
     * Ensure user is HR admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized access to HR4 Core Human Capital.');
        }
    }

    /**
     * Display employees with departments and positions
     */
    public function index()
    {
        // Check role
        $this->authorizeHrAdmin();

        // Fetch employees data from Core Human Capital
        $employees = Employee::with('department', 'position')->get();
        $departments = Department::all();
        $positions = DepartmentPositionTitle::with('department')->get();
        $users = User::all();

        // Fetch job postings
        $jobPostings = AvailableJob::with('poster')
            ->leftJoin('departments_hr2', 'available_jobs_hr4.department', '=', 'departments_hr2.department_id')
            ->select('available_jobs_hr4.*', 'departments_hr2.name as department_name')
            ->orderBy('available_jobs_hr4.created_at', 'desc')
            ->get();

        // Get count of available (open) jobs
        $availableJobsCount = AvailableJob::where('status', 'open')->count();

        // Fetch HR1 employees dynamically
        $hr1_employees = DB::table('new_hires_hr1')->select('id', 'first_name', 'last_name')->get();

        // Needed positions logic
        $needed_positions = [];
        foreach ($positions as $pos) {
            $current_count = $employees->where('position_id', $pos->id)->count();
            $needed = max(0, ($pos->required_count ?? 0) - $current_count);
            $needed_positions[] = [
                'department' => $pos->department->name ?? 'N/A',
                'position' => $pos->position_title,
                'required' => $pos->required_count ?? 0,
                'current' => $current_count,
                'needed' => $needed
            ];
        }

        // Return view (compact fully closed)
        return view('admin.hr4.core_human_capital', compact(
            'employees',
            'departments',
            'positions',
            'hr1_employees',
            'users',
            'jobPostings',
            'availableJobsCount',
            'needed_positions'
        ));
    }

    /**
     * Process hired users from HR1 and create employee records
     */
    public function processHiredUsers()
    {
        $this->authorizeHrAdmin();

        // Get hired users that don't have employee records yet
        $hiredUsers = HiredUser::whereDoesntHave('employee')->get();

        $processed = 0;
        foreach ($hiredUsers as $hired) {
            // Parse full_name into first and last name (assuming "First Last" format)
            $nameParts = explode(' ', $hired->full_name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            // Get job details for department and position
            $job = $hired->job;
            if ($job) {
                // Find department by name
                $department = Department::where('name', $job->department)->first();
                // Find position by title
                $position = DepartmentPositionTitle::where('position_title', $job->title)->first();

                // Create employee record
                Employee::create([
                    'employee_id' => $hired->employee_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'department_id' => $department ? $department->department_id : null,
                    'position_id' => $position ? $position->id : null,
                    'hire_date' => $hired->hired_at->toDateString(),
                    'status' => 'active',
                ]);

                $processed++;
            }
        }

        return redirect()->back()->with('success', "Processed $processed new hires and created employee records.");
    }
}