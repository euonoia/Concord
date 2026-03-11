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
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403, 'Unauthorized access to HR2 Succession Planning.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        $departments = Department::where('is_active', 1)->get();
        $positions = DepartmentPositionTitle::where('is_active', 1)->orderBy('position_title')->get();

        // Join with HR1 Validated Grade and Evaluator Info
        $candidates = SuccessorCandidate::with(['position', 'employee'])
            ->leftJoin('validated_training_performance_hr1 as vtp', 'successor_candidates_hr2.employee_id', '=', 'vtp.employee_id')
            ->leftJoin('employees as evaluator', 'vtp.evaluated_by', '=', 'evaluator.employee_id')
            ->select(
                'successor_candidates_hr2.*',
                'vtp.weighted_average as training_grade',
                'evaluator.first_name as eval_fname',
                'evaluator.last_name as eval_lname'
            )
            ->where('successor_candidates_hr2.is_active', 1)
            ->get()
            ->sortBy(fn($c) => ['Ready Now'=>1,'1-2 Years'=>2,'3+ Years'=>3,'Emergency'=>4][$c->readiness] ?? 5);

        return view('admin.hr2.succession', compact('departments', 'positions', 'candidates'));
    }

    public function storeCandidate(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'position_id'      => 'required|exists:department_position_titles_hr2,id',
            'employee_id'      => 'required|exists:employees,employee_id',
            'readiness'        => 'required|in:Ready Now,1-2 Years,3+ Years,Emergency',
            'retention_risk'   => 'required|in:High,Medium,Low',
            'effective_at'     => 'required|date',
            'development_plan' => 'nullable|string|max:1000',
        ]);

        // Auto-fetch the Validated Grade from HR1
        $training = DB::table('validated_training_performance_hr1')
            ->where('employee_id', $validated['employee_id'])
            ->first();

        $position = DepartmentPositionTitle::findOrFail($validated['position_id']);

        SuccessorCandidate::create([
            'position_id'       => $position->id,
            'employee_id'       => $validated['employee_id'],
            'department_id'     => $position->department_id,
            'specialization'    => $position->specialization_name,
            'readiness'         => $validated['readiness'],
            'performance_score' => $training ? $training->weighted_average : 0, // Swapped to Grade
            'potential_score'   => 0, // Removed per request
            'retention_risk'    => $validated['retention_risk'],
            'effective_at'      => $validated['effective_at'],
            'development_plan'  => $validated['development_plan'], // Now text description
            'is_active'         => 1,
        ]);

        return redirect()->back()->with('success', 'Candidate successfully added to pipeline using HR1 Training Validation.');
    }

    public function promoteCandidate($id)
    {
        $this->checkAccess();
        try {
            DB::beginTransaction();
            $candidate = SuccessorCandidate::findOrFail($id);
            $employee = Employee::where('employee_id', $candidate->employee_id)->firstOrFail();

            $employee->update([
                'position_id'    => $candidate->position_id,
                'department_id'  => $candidate->department_id,
                'specialization' => $candidate->specialization
            ]);

            $candidate->is_active = 0;
            $candidate->save();

            DB::commit();
            return redirect()->back()->with('success', "Promotion Successful! {$employee->first_name} is now the official {$candidate->position->position_title}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function getEmployeesByDeptAndSpec(Request $request, $dept_id)
    {
        // AJAX: Returns Grade and Evaluator for the selection preview
        $employees = Employee::query()
            ->leftJoin('validated_training_performance_hr1 as vtp', 'employees.employee_id', '=', 'vtp.employee_id')
            ->leftJoin('employees as evaluator', 'vtp.evaluated_by', '=', 'evaluator.employee_id')
            ->where('employees.department_id', $dept_id)
            ->select(
                'employees.employee_id', 
                'employees.first_name', 
                'employees.last_name', 
                'employees.specialization',
                'vtp.weighted_average',
                DB::raw("CONCAT(evaluator.first_name, ' ', evaluator.last_name) as evaluator_name")
            )
            ->orderBy('employees.first_name')
            ->get();

        return response()->json($employees);
    }

    public function getSpecializations($dept_code)
    {
        return response()->json(DepartmentSpecialization::where('dept_code', $dept_code)->get(['specialization_name']));
    }

    public function getPositions(Request $request, $dept_code)
    {
        $spec = $request->query('specialization');
        return response()->json(DepartmentPositionTitle::where('department_id', $dept_code)
            ->when($spec, fn($q) => $q->where('specialization_name', $spec))
            ->get(['id','position_title']));
    }

    public function destroyCandidate($id)
    {
        $this->checkAccess();
        SuccessorCandidate::where('id', $id)->update(['is_active' => 0]);
        return redirect()->back()->with('success', 'Nomination removed.');
    }
}