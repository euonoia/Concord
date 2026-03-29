<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminLearningEnrollController extends Controller
{
    /**
     * Authorize only HR2 Admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403);
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        $employees = DB::table('employees as e')
            ->leftJoin('departments_hr2 as d', 'e.department_id', '=', 'd.department_id')
            ->select('e.*', 'd.name as department_name') 
            ->whereNotNull('e.department_id')
            ->get();

        return view('admin.hr2.learning.enroll_table', compact('employees'));
    }

    public function showEnrollment($id)
    {
        $this->authorizeHrAdmin();

        $employee = DB::table('employees as e')
            ->leftJoin('departments_hr2 as d', 'e.department_id', '=', 'd.department_id')
            ->select('e.*', 'd.name as department_name') 
            ->where('e.id', $id)
            ->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        // 1. Get modules based on specialization
        $modules = DB::table('learning_modules_hr2')
            ->where('specialization_name', $employee->specialization)
            ->get();

        // 2. Fetch already enrolled module codes for this specific employee
        $existingEnrollments = DB::table('course_enrolls_hr2')
            ->where('employee_id', $employee->employee_id)
            ->pluck('module_code')
            ->toArray();

        // 3. Attach Materials
        foreach ($modules as $module) {
            $module->materials = DB::table('learning_materials_hr2')
                ->where('module_code', $module->module_code)
                ->get();
            
            // Tag module as already enrolled for the view
            $module->is_enrolled = in_array($module->module_code, $existingEnrollments);
        }

        return view('admin.hr2.learning.show_enroll_table', compact('employee', 'modules'));
    }

    public function assignModules(Request $request)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'employee_id' => 'required',
            'module_codes' => 'required|array'
        ]);

        $employee = DB::table('employees')->where('id', $request->employee_id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $count = 0;

        foreach ($request->module_codes as $m_code) {

            $module = DB::table('learning_modules_hr2')
                ->where('module_code', $m_code)
                ->first();

            if (!$module) continue;

            DB::table('course_enrolls_hr2')->updateOrInsert(
                [
                    'employee_id' => $employee->employee_id,
                    'module_code' => $m_code
                ],
                [
                    'status' => 'completed',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            DB::table('competency_enroll_hr2')->updateOrInsert(
                [
                    'employee_id' => $employee->employee_id,
                    'competency_code' => $module->competency_code
                ],
                [
                    'status' => 'completed',
                    'enrolled_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            DB::table('employee_competency_completion_hr2')->updateOrInsert(
                [
                    'employee_id' => $employee->employee_id,
                    'competency_code' => $module->competency_code
                ],
                [
                    'status' => 'completed',
                    'verified_by' => null,
                    'verification_notes' => null,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            $count++;
        }

        return redirect()
            ->route('hr2.learning.enroll')
            ->with('success', "Successfully enrolled user in {$count} modules. Competencies are now pending verification.");
    }
}