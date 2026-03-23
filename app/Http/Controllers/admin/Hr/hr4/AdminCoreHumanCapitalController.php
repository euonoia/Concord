<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\User;
use App\Models\admin\Hr\hr4\HiredUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\admin\Hr\hr4\AvailableJob;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr4\PromotedEmployee;

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
            $current_count = $employees->where('position_id', $pos->id)
                                      ->where('status', 'active')
                                      ->count();
            $needed = max(0, ($pos->required_count ?? 0) - $current_count);
            $needed_positions[] = [
                'department' => $pos->department->name ?? $pos->department_id ?? 'Unknown Department',
                'position' => $pos->position_title,
                'required' => $pos->required_count ?? 0,
                'current' => $current_count,
                'needed' => $needed
            ];
        }

        // HR2 succession pipeline (candidate table for HR4 to promote from)
        $successionPipeline = SuccessorCandidate::with(['position', 'position.department', 'employee'])
            ->where('is_active', 1)
            ->orderByRaw("FIELD(readiness,'Ready Now','1-2 Years','3+ Years','Emergency')")
            ->orderBy('updated_at', 'desc')
            ->get();

        // Fetch promoted employees from dedicated HR4 promotion history
        $promotedEmployees = PromotedEmployee::with(['employee', 'oldPosition', 'newPosition'])
            ->orderBy('promoted_at', 'desc')
            ->get()
            ->map(function($promotion) {
                return [
                    'id' => $promotion->id,
                    'employee_id' => $promotion->employee_id,
                    'first_name' => optional($promotion->employee)->first_name ?? 'N/A',
                    'last_name' => optional($promotion->employee)->last_name ?? 'N/A',
                    'previous_position' => optional($promotion->oldPosition)->position_title ?? 'N/A',
                    'new_position' => optional($promotion->newPosition)->position_title ?? 'N/A',
                    'department' => optional($promotion->newPosition->department)->name ?? 'N/A',
                    'promoted_at' => $promotion->promoted_at instanceof \Carbon\Carbon ? $promotion->promoted_at->format('Y-m-d') : 'N/A',
                    'readiness' => 'Promoted', // Since it's from promotion history
                    'performance_score' => 'N/A', // Not applicable here
                ];
            });

        // Return view (compact fully closed)
        return view('admin.hr4.core_human_capital', compact(
            'employees',
            'departments',
            'positions',
            'hr1_employees',
            'users',
            'jobPostings',
            'availableJobsCount',
            'needed_positions',
            'successionPipeline',
            'promotedEmployees'
        ));
    }

    /**
     * Promote a succession candidate from HR4 core, grade-based using rank levels
     */
    public function promoteSuccessionCandidate($id)
    {
        $this->authorizeHrAdmin();

        try {
            DB::beginTransaction();

            $candidate = SuccessorCandidate::with(['position', 'employee'])->findOrFail($id);

            if ($candidate->readiness !== 'Ready Now') {
                return redirect()->back()->with('error', 'Only candidates with Ready Now can be promoted.');
            }

            $employee = Employee::where('employee_id', $candidate->employee_id)->first();
            if (!$employee) {
                return redirect()->back()->with('error', 'Employee profile not found for this candidate.');
            }

            $currentPosition = $employee->position;

            // Define rank hierarchy (lowest to highest)
            $rankHierarchy = ['Resident', 'Specialist', 'Consultant', 'Chief'];
            
            // Determine the true promotion target based on rank level
            $targetPosition = null;

            if ($currentPosition && $currentPosition->rank_level !== null) {
                $currentRankIndex = array_search($currentPosition->rank_level, $rankHierarchy);
                
                if ($currentRankIndex !== false && $currentRankIndex < count($rankHierarchy) - 1) {
                    $nextRank = $rankHierarchy[$currentRankIndex + 1];
                    
                    $targetPosition = DepartmentPositionTitle::where('department_id', $currentPosition->department_id)
                        ->where('specialization_name', $currentPosition->specialization_name ?? $employee->specialization)
                        ->where('is_active', 1)
                        ->where('rank_level', $nextRank)
                        ->first();
                }
            }

            if (!$targetPosition && $candidate->position) {
                $targetPosition = $candidate->position;
            }

            if (!$targetPosition) {
                return redirect()->back()->with('error', 'No eligible promotion position found for this candidate.');
            }

            // Store old values for history
            $oldPositionId = $employee->position_id;
            $oldDepartmentId = $employee->department_id;
            $oldSpecialization = $employee->specialization;

            // Update employee to promoted position (based on shared HR2 logic)
            $employee->update([
                'position_id' => $targetPosition->id,
                'department_id' => $targetPosition->department_id,
                'specialization' => $targetPosition->specialization_name,
                'post_grad_status' => 'fellowship', // Based on shared code
            ]);

            // Update candidate status
            $candidate->is_active = 0;
            $candidate->position_id = $targetPosition->id;
            $candidate->department_id = $targetPosition->department_id;
            $candidate->specialization = $targetPosition->specialization_name;
            $candidate->save();

            // Get the logged-in HR4 admin's employee record
            $loggedInEmployee = Employee::where('user_id', Auth::id())->first();

            // Record promotion in HR4 history
            PromotedEmployee::create([
                'employee_id' => $employee->employee_id,
                'old_position_id' => $oldPositionId,
                'new_position_id' => $targetPosition->id,
                'old_department_id' => $oldDepartmentId,
                'new_department_id' => $targetPosition->department_id,
                'old_specialization' => $oldSpecialization,
                'new_specialization' => $targetPosition->specialization_name,
                'promoted_by' => $loggedInEmployee ? $loggedInEmployee->employee_id : 'HR4',
                'promoted_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Promotion successful: {$employee->first_name} {$employee->last_name} is now {$targetPosition->position_title} with Fellowship status.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Promotion failed: ' . $e->getMessage());
        }
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
            $department = null;
            $position = null;

            if ($job) {
                // Find department by name
                $department = Department::where('name', $job->department)->first();
                // Use position_id directly from the job posting
                $position = DepartmentPositionTitle::find($job->position_id);
            } else {
                // Fallback: Determine department and position from employee_id pattern
                $employeeId = $hired->employee_id;
                
                // Extract department code from employee ID (e.g., GEN-0001 -> GEN, NEU-0001 -> NEU)
                $deptCode = strtoupper(explode('-', $employeeId)[0] ?? '');
                
                // Map department codes to actual department IDs
                $deptMapping = [
                    'GEN' => 'MED-GEN',      // General Medicine
                    'NEU' => 'NEURO-01',     // Neurology
                    'PED' => 'PED-01',       // Pediatrics
                    'PSY' => 'PSY-01',       // Psychology/Psychiatry
                    'PATH' => 'PATH-01',     // Pathology
                    'RAD' => 'RAD-01',       // Radiology
                ];
                
                if (isset($deptMapping[$deptCode])) {
                    $department = Department::where('department_id', $deptMapping[$deptCode])->first();
                    
                    // Find a suitable position in this department (preferably the first one with base salary)
                    if ($department) {
                        $position = DepartmentPositionTitle::where('department_id', $department->department_id)
                            ->where('base_salary', '>', 0)
                            ->orderBy('rank_level')
                            ->first();
                    }
                }
            }

            // Only proceed if we have department and position
            if ($department && $position) {

                // Create employee record
                $employee = Employee::create([
                    'employee_id' => $hired->employee_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'department_id' => $department->department_id,
                    'position_id' => $position->id,
                    'hire_date' => $hired->hired_at->toDateString(),
                    'status' => 'active',
                ]);

                // Create DirectCompensation record with base salary from position
                // Always create a DirectCompensation record, even if base_salary might be adjusted later
                $baseSalary = $position->base_salary ?? 0;
                DirectCompensation::create([
                    'employee_id' => $hired->employee_id,
                    'month' => now()->format('Y-m'), // Current month
                    'base_salary' => $baseSalary,
                    'shift_allowance' => 0,
                    'overtime_pay' => 0,
                    'night_diff_pay' => 0,
                    'bonus' => 0,
                    'training_reward' => 0,
                    'worked_hours' => 0,
                    'overtime_hours' => 0,
                    'night_diff_hours' => 0,
                ]);

                // If this was from a job posting, decrement available positions
                if ($job && $job->positions_available > 0) {
                    $job->decrement('positions_available');
                    
                    // If no positions left, close the job
                    $job->refresh(); // Refresh to get updated value
                    if ($job->positions_available <= 0) {
                        $job->update(['status' => 'closed']);
                    }
                }

                $processed++;
            }
        }

        return redirect()->back()->with('success', "Processed $processed new hires and created employee records.");
    }

    /**
     * Show edit employee form
     */
    public function editEmployee(Employee $employee)
    {
        $this->authorizeHrAdmin();

        $departments = Department::all();
        $positions = DepartmentPositionTitle::with('department')->get();

        return view('admin.hr4.edit_employee', compact('employee', 'departments', 'positions'));
    }

    /**
     * Update employee
     */
    public function updateEmployee(Request $request, Employee $employee)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'employee_id' => 'required|string|max:50|unique:employees,employee_id,' . $employee->id,
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|string|max:50',
            'position_id' => 'nullable|integer',
            'specialization' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
            'is_on_duty' => 'boolean',
            'status' => 'required|in:active,inactive,resigned,terminated',
        ]);

        $employee->update($request->all());

        return redirect()->route('hr4.core')->with('success', 'Employee updated successfully.');
    }

    /**
     * Delete employee
     */
    public function deleteEmployee(Employee $employee)
    {
        $this->authorizeHrAdmin();

        // Check if employee has related records that prevent deletion
        if ($employee->payrolls()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete employee with existing payroll records.');
        }

        $employee->delete();

        return redirect()->route('hr4.core')->with('success', 'Employee deleted successfully.');
    }

    /**
     * Update employee status
     */
    public function updateEmployeeStatus(Request $request, Employee $employee)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'status' => 'required|in:active,inactive,resigned,terminated',
        ]);

        $employee->update(['status' => $request->status]);

        $statusMessages = [
            'active' => 'Employee activated successfully.',
            'inactive' => 'Employee deactivated successfully.',
            'resigned' => 'Employee marked as resigned.',
            'terminated' => 'Employee terminated successfully.',
        ];

        return redirect()->back()->with('success', $statusMessages[$request->status] ?? 'Employee status updated.');
    }
}