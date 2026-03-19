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
            ->select(
                'new_hires_hr1.*',
                'departments_hr2.name as department_name'
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

        return view('admin.hr1.new_hires.index', compact(
            'newHires',
            'departments',
            'specializations',
            'filters'
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
     * Update onboarding status
     * Creates employee + user account when status = active
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorizeHr1Admin();

        $request->validate([
            'status' => 'required|in:onboarding,active,inactive',
        ]);

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
                /** @var object $newHire */
                $newHire = DB::table('new_hires_hr1')->where('id', $id)->first();
                if (!$newHire) throw new \Exception("New hire not found.");

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
                        if ($jobPosting && $jobPosting->hr4_job_id) {
                            DB::table('available_jobs_hr4')
                                ->where('id', $jobPosting->hr4_job_id)
                                ->where('positions_available', '>', 0)
                                ->decrement('positions_available');
                        }
                    }
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
}