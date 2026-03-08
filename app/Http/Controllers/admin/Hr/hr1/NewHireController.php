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

        $filters = $request->only(['department', 'position', 'status']);

        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $positions = DB::table('department_position_titles_hr2')
            ->where('is_active', 1)
            ->orderBy('position_title')
            ->get();

        $query = DB::table('new_hires_hr1')
            ->leftJoin('departments_hr2', 'new_hires_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('department_position_titles_hr2', 'new_hires_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'new_hires_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
            );

        if (!empty($filters['department'])) {
            $query->where('new_hires_hr1.department_id', $filters['department']);
        }

        if (!empty($filters['position'])) {
            $query->where('new_hires_hr1.position_id', $filters['position']);
        }

        if (!empty($filters['status'])) {
            $query->where('new_hires_hr1.status', $filters['status']);
        }

        $newHires = $query->orderByDesc('new_hires_hr1.id')->paginate(10);

        return view('admin.hr1.new_hires.index', compact(
            'newHires',
            'departments',
            'positions',
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
            ->leftJoin('department_position_titles_hr2', 'new_hires_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'new_hires_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
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

            DB::table('new_hires_hr1')
                ->where('id', $id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now(),
                ]);

            $message = "New hire status updated.";

            if ($request->status === 'active') {

                $newHire = DB::table('new_hires_hr1')
                    ->where('id', $id)
                    ->first();

                if (!$newHire) {
                    throw new \Exception("New hire not found.");
                }

                $existingUser = DB::table('users')
                    ->where('email', $newHire->email)
                    ->first();

                if (!$existingUser) {

                    /*
                    Generate Department Based Employee ID
                    */

                    $department = DB::table('departments_hr2')
                        ->where('department_id', $newHire->department_id)
                        ->first();

                    if (!$department) {
                        throw new \Exception("Department not found.");
                    }

                    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $department->name), 0, 3));

                    $lastEmployee = DB::table('employees')
                        ->where('employee_id', 'LIKE', $prefix . '-%')
                        ->orderByDesc('employee_id')
                        ->first();

                    if ($lastEmployee) {
                        $lastNumber = (int) substr($lastEmployee->employee_id, -4);
                        $nextNumber = $lastNumber + 1;
                    } else {
                        $nextNumber = 1;
                    }

                    $employeeId = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    /*
                    Create User Account
                    */

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

                    /*
                    Create Employee Record
                    */

                    DB::table('employees')->insert([
                        'user_id' => $userId,
                        'employee_id' => $employeeId,
                        'first_name' => $newHire->first_name,
                        'last_name' => $newHire->last_name,
                        'phone' => $newHire->phone ?? null,
                        'department_id' => $newHire->department_id,
                        'position_id' => $newHire->position_id,
                        'specialization' => $newHire->specialization ?? null,
                        'post_grad_status' => $newHire->post_grad_status ?? null,
                        'hire_date' => now(),
                        'is_on_duty' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $message = "Employee account created successfully. Username: {$employeeId} | Password: 123456789";
                }
                else {
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