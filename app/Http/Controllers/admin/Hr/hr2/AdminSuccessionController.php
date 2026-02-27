<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSuccessionController extends Controller
{
    private function checkAccess()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized access to HR2 Succession Planning.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        $departments = Department::where('is_active', 1)->get();

        $positions = DepartmentPositionTitle::where('is_active', 1)
            ->with('department')
            ->orderBy('position_title')
            ->get();

        // Get active candidates sorted by readiness priority
        $candidates = SuccessorCandidate::with(['position', 'employee'])
            ->where('is_active', 1)
            ->get()
            ->sortBy(fn($c) => ['Ready Now'=>1,'1-2 Years'=>2,'3+ Years'=>3,'Emergency'=>4][$c->readiness] ?? 5);

        return view('admin.hr2.succession', compact('departments','positions','candidates'));
    }

    /** * NEW: Promote Candidate Logic
     * Grants the target position to the successor and updates their employee file.
     */
    public function promoteCandidate($id)
    {
        $this->checkAccess();

        try {
            DB::beginTransaction();

            $candidate = SuccessorCandidate::findOrFail($id);
            $employee = Employee::where('employee_id', $candidate->employee_id)->firstOrFail();

            // Update Employee Record
            $employee->update([
                'position_id' => $candidate->position_id,
                'department_id' => $candidate->department_id,
                'specialization' => $candidate->specialization
            ]);

            // Deactivate Succession record (Task Complete)
            $candidate->is_active = 0;
            $candidate->save();

            DB::commit();
            return redirect()->back()->with('success', "Promotion Successful! {$employee->first_name} is now the official {$candidate->position->position_title}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process promotion: ' . $e->getMessage());
        }
    }

    // --- AJAX Helper Methods ---

    public function getSpecializations($dept_code)
    {
        $specializations = DepartmentSpecialization::where('dept_code', $dept_code)
            ->where('is_active', 1)
            ->get(['specialization_name']);
        return response()->json($specializations);
    }

    public function getPositions(Request $request, $dept_code)
    {
        $specialization = $request->query('specialization');
        $positions = DepartmentPositionTitle::where('department_id', $dept_code)
            ->where('is_active', 1)
            ->when($specialization, fn($q) => $q->where('specialization_name', $specialization))
            ->get(['id','position_title','rank_level']);
        return response()->json($positions);
    }

    public function getEmployeesByDeptAndSpec(Request $request, $dept_id)
    {
        $employees = Employee::where('department_id', $dept_id)
            ->orderBy('first_name')
            ->get(['employee_id', 'first_name', 'last_name', 'specialization']);
        return response()->json($employees);
    }

    public function storeCandidate(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'position_id'=>'required|exists:department_position_titles_hr2,id',
            'employee_id'=>'required|exists:employees,employee_id',
            'readiness'=>'required|in:Ready Now,1-2 Years,3+ Years,Emergency',
            'perf_score'=>'required|numeric|min:1|max:10',
            'pot_score'=>'required|numeric|min:1|max:10',
            'retention_risk'=>'required|in:High,Medium,Low',
            'effective_at'=>'required|date',
            'development_plan'=>'nullable|string|max:500',
        ]);

        $position = DepartmentPositionTitle::findOrFail($validated['position_id']);

        SuccessorCandidate::create([
            'position_id'=>$position->id,
            'employee_id'=>$validated['employee_id'],
            'department_id'=>$position->department_id,
            'specialization'=>$position->specialization_name,
            'readiness'=>$validated['readiness'],
            'performance_score'=>$validated['perf_score'],
            'potential_score'=>$validated['pot_score'],
            'retention_risk'=>$validated['retention_risk'],
            'effective_at'=>$validated['effective_at'],
            'development_plan'=>$validated['development_plan'] ?? null,
            'is_active'=>1,
        ]);

        return redirect()->back()->with('success','Candidate added to pipeline.');
    }

    public function destroyCandidate($id)
    {
        $this->checkAccess();
        $candidate = SuccessorCandidate::findOrFail($id);
        $candidate->is_active = 0;
        $candidate->save();
        return redirect()->back()->with('success','Nomination removed.');
    }
}