<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewHireController extends Controller
{
    /**
     * Ensure only HR/admin can access
     */
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display list of new hires
     */
    public function index(Request $request)
    {
        $this->authorizeHr1Admin();

        $filters = $request->only(['department', 'specialization', 'status']);

        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Get all distinct specializations from new hires for filter dropdown
        $specializations = DB::table('new_hires_hr1')
            ->select('specialization as specialization_name')
            ->distinct()
            ->whereNotNull('specialization')
            ->orderBy('specialization')
            ->get();

        $query = DB::table('new_hires_hr1')
            ->leftJoin('departments_hr2', 'new_hires_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('onboarding_assessments_hr1', 'new_hires_hr1.applicant_id', '=', 'onboarding_assessments_hr1.applicant_id')
            ->select(
                'new_hires_hr1.*',
                'departments_hr2.name as department_name',
                'onboarding_assessments_hr1.assessment_status',
                'onboarding_assessments_hr1.is_validated',
                'onboarding_assessments_hr1.validated_by'
            );

        if (!empty($filters['department'])) {
            $query->where('new_hires_hr1.department_id', $filters['department']);
        }

        if (!empty($filters['specialization'])) {
            $query->where('new_hires_hr1.specialization', $filters['specialization']);
        }

        if (!empty($filters['status'])) {
            $query->where('new_hires_hr1.status', $filters['status']);
        }

        $newHires = $query->orderByDesc('new_hires_hr1.id')->paginate(10);

        // Fetch recent syncs to HR4
        $recentSyncs = DB::table('hired_users_hr4')
            ->orderByDesc('hired_at')
            ->limit(5)
            ->get();

        return view('admin.hr1.new_hires.index', compact(
            'newHires',
            'departments',
            'specializations',
            'filters',
            'recentSyncs'
        ));
    }

    /**
     * Show new hire details
     */
    public function show($id)
    {
        $this->authorizeHr1Admin();

        $newHire = DB::table('new_hires_hr1')
            ->leftJoin('departments_hr2', 'new_hires_hr1.department_id', '=', 'departments_hr2.department_id')
            ->select(
                'new_hires_hr1.*',
                'departments_hr2.name as department_name'
            )
            ->where('new_hires_hr1.id', $id)
            ->first();

        if (!$newHire) {
            abort(404);
        }

        return view('admin.hr1.new_hires.show', compact('newHire'));
    }

    /**
     * Download resume
     */
    public function downloadResume($id)
    {
        $this->authorizeHr1Admin();

        $newHire = DB::table('new_hires_hr1')->where('id', $id)->first();

        if (!$newHire || !$newHire->resume_path) {
            abort(404, 'Resume not found.');
        }

        $compressed = Storage::disk('public')->get($newHire->resume_path);
        $pdfContent = gzdecode($compressed);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="resume.pdf"');
    }

    /**
     * Validate HR2 assessment result
     */
    public function validateAssessment(Request $request, $applicant_id)
    {
        $this->authorizeHr1Admin();

        $assessment = DB::table('onboarding_assessments_hr1')
            ->where('applicant_id', $applicant_id)
            ->first();

        if (!$assessment) {
            return back()->with('error', 'Assessment record not found.');
        }

        if ($assessment->assessment_status !== 'passed') {
            return back()->with('error', 'Cannot validate: Assessment status must be PASSED first (Current: ' . strtoupper($assessment->assessment_status) . ').');
        }

        DB::table('onboarding_assessments_hr1')
            ->where('applicant_id', $applicant_id)
            ->update([
                'is_validated' => true,
                'validated_by' => Auth::user()->name,
                'updated_at' => now()
            ]);

        return back()->with('success', 'Onboarding assessment validated successfully.');
    }

    /**
     * Update onboarding status
     * Creates employee + user account when status = active
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeHr1Admin();

        $request->validate([
            'status' => 'required|in:onboarding,active,inactive',
        ]);

        // Check if activation is allowed
        if ($request->status === 'active') {
            $hire = DB::table('new_hires_hr1 as n')
                ->leftJoin('onboarding_assessments_hr1 as a', 'n.applicant_id', '=', 'a.applicant_id')
                ->where('n.id', $id)
                ->select('a.is_validated', 'a.assessment_status')
                ->first();

            if (!$hire || !$hire->is_validated) {
                $reason = ($hire && $hire->assessment_status !== 'passed') 
                    ? "Mandatory HR2 Onboarding Assessment must be PASSED first." 
                    : "HR1 must VALIDATE the assessment results before activation.";
                return back()->with('error', "Activation failed: $reason");
            }
        }

        DB::beginTransaction();

        try {
            // Update status
            DB::table('new_hires_hr1')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);

            $message = "New hire status updated.";

            // Create employee account if status becomes active
            if ($request->status === 'active') {

                // CHECK HR2 ASSESSMENT FIRST
                /** @var object $newHire */
                $newHire = DB::table('new_hires_hr1')->where('id', $id)->first();
                if (!$newHire) throw new \Exception("New hire not found.");

                $assessment = DB::table('onboarding_assessments_hr1')
                    ->where('applicant_id', $newHire->applicant_id)
                    ->first();

                if (!$assessment || $assessment->assessment_status !== 'passed') {
                    throw new \Exception("Activation failed: Mandatory HR2 Onboarding Assessment must be PASSED first.");
                }

                $existingUser = DB::table('users')->where('email', $newHire->email)->first();

                if (!$existingUser) {
                    // Generate department-based Employee ID
                    /** @var object $department */
                    $department = DB::table('departments_hr2')
                        ->where('department_id', $newHire->department_id)
                        ->first();

                    if (!$department) throw new \Exception("Department not found.");

                    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $department->name), 0, 3));

                    /** @var object $lastEmployee */
                    $lastEmployee = DB::table('employees')
                        ->where('employee_id', 'LIKE', $prefix . '-%')
                        ->orderByDesc('employee_id')
                        ->first();

                    $nextNumber = $lastEmployee ? ((int) substr($lastEmployee->employee_id, -4) + 1) : 1;
                    $employeeId = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    // Create user account
                    $userId = DB::table('users')->insertGetId([
                        'uuid' => Str::uuid(),
                        'username' => $employeeId,
                        'email' => $newHire->email,
                        'password' => Hash::make('123456789'),
                        'user_type' => 'staff',
                        'role_slug' => 'employee',
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create employee record
                    DB::table('employees')->insert([
                        'user_id' => $userId,
                        'employee_id' => $employeeId,
                        'first_name' => $newHire->first_name,
                        'last_name' => $newHire->last_name,
                        'phone' => $newHire->phone ?? null,
                        'department_id' => $newHire->department_id,
                        'specialization' => $newHire->specialization ?? null,
                        'post_grad_status' => $newHire->post_grad_status ?? null,
                        'hire_date' => now(),
                        'is_on_duty' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $message = "Employee account created successfully. Username: {$employeeId} | Password: 123456789";

                    // Decrement HR4 positions if linked
                    /** @var object $applicant */
                    $applicant = DB::table('applicants_hr1')->where('id', $newHire->applicant_id)->first();
                    if ($applicant && $applicant->job_posting_id) {
                        /** @var object $jobPosting */
                        $jobPosting = DB::table('job_postings_hr1')->where('id', $applicant->job_posting_id)->first();
                        // Reworked HR4 Sync: Insert into hired_users_hr4
                        if ($jobPosting && $jobPosting->hr4_job_id) {
                            $hr4_job_id = $jobPosting->hr4_job_id;
                            $emp_data = [
                                'first_name' => $newHire->first_name,
                                'last_name' => $newHire->last_name,
                            ];

                            DB::table('hired_users_hr4')->insert([
                                'hr4_job_id' => $hr4_job_id,
                                'employee_id' => $employeeId,
                                'full_name' => $emp_data['first_name'] . ' ' . $emp_data['last_name'],
                                'hired_at' => now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Decrement positions in HR4
                            DB::table('available_jobs_hr4')
                                ->where('id', $hr4_job_id)
                                ->decrement('positions_available');

                            // Auto-close job if no positions remaining
                            /** @var object $job */
                            $job = DB::table('available_jobs_hr4')->where('id', $hr4_job_id)->first();
                            if ($job && $job->positions_available <= 0) {

                                DB::table('available_jobs_hr4')
                                    ->where('id', $hr4_job_id)
                                    ->update(['status' => 'closed']);
                            }
                        }
                    }

                    // Connection 4: Payroll/Compensation Initialization
                    DB::table('direct_compensations_hr4')->insert([
                        'employee_id' => $employeeId,
                        'month' => date('Y-m'),
                        'base_salary' => 0, // Should be updated by HR4
                        'shift_allowance' => 0,
                        'bonus' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $message = "Status updated. Account already exists.";
                }
            }

            DB::commit();

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Manual Sync to HR4 (Handover)
     */
    public function syncToHr4(Request $request)
    {
        $this->authorizeHr1Admin();

        // Find active employees from HR1 who are not yet in hired_users_hr4
        $activeEmployees = DB::table('employees')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->join('new_hires_hr1', 'users.email', '=', 'new_hires_hr1.email')
            ->where('new_hires_hr1.status', 'active')
            ->whereNotIn('employees.employee_id', function ($query) {
                $query->select('employee_id')->from('hired_users_hr4');
            })
            ->select('employees.*', 'users.email')
            ->get();



        if ($activeEmployees->isEmpty()) {
            return redirect()->back()->with('info', 'All active employees are already synchronized with HR4.');
        }

        $syncedCount = 0;
        foreach ($activeEmployees as $emp) {
            /** @var object $emp */
            // Find the corresponding new hire to get the hr4_job_id if possible
            $newHire = DB::table('new_hires_hr1')
                ->where('email', $emp->email) // Assuming email is unique
                ->first();

            $hr4_job_id = null;
            if ($newHire) {
                /** @var object $newHire */
                $applicant = DB::table('applicants_hr1')->where('id', $newHire->applicant_id)->first();
                if ($applicant) {
                    /** @var object $applicant */
                    if ($applicant->job_posting_id) {
                        $jobPosting = DB::table('job_postings_hr1')->where('id', $applicant->job_posting_id)->first();
                        if ($jobPosting) {
                            /** @var object $jobPosting */
                            $hr4_job_id = $jobPosting->hr4_job_id;
                        }
                    }
                }
            }

            DB::table('hired_users_hr4')->insert([
                'hr4_job_id' => $hr4_job_id,
                'employee_id' => $emp->employee_id,
                'full_name' => $emp->first_name . ' ' . $emp->last_name,
                'hired_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $syncedCount++;
        }


        return redirect()->back()->with('success', "Successfully handed over {$syncedCount} employees to Core Human Capital (HR4).");
    }
}