<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\SuccessionPosition;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\Employee; 
use Illuminate\Support\Facades\Auth;

class AdminSuccessionController extends Controller
{
    private function checkAccess()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized access to HR2 Succession Planning.');
        }
    }

    /**
     * Display Succession Positions & Candidates
     */
    public function index()
    {
        $this->checkAccess();

        $departments = Department::where('is_active', 1)
            ->with('specializations')
            ->get();

        $positions = SuccessionPosition::withCount('candidates')
            ->orderBy('position_title')
            ->get();

        $candidates = SuccessorCandidate::with(['position', 'employee'])
            ->get()
            ->sortBy(function($candidate) {
                $order = ['Ready Now' => 1, '1-2 Years' => 2, '3+ Years' => 3, 'Emergency' => 4];
                return $order[$candidate->readiness] ?? 5;
            });

        $employees = Employee::orderBy('first_name')->get();

        return view('admin.hr2.succession', compact('positions', 'candidates', 'employees', 'departments'));
    }

    /**
     * Return specializations based on department code
     */
    public function getSpecializations($dept_code)
    {
        $specializations = DepartmentSpecialization::where('dept_code', $dept_code)
            ->where('is_active', 1)
            ->get(['specialization_name']);

        return response()->json($specializations);
    }

    /**
     * Store a new Succession Position
     */
    public function storePosition(Request $request)
{
    $this->checkAccess();

    $validated = $request->validate([
        'position_title' => 'required|string|max:255',
        'criticality'    => 'required|in:low,medium,high',
        'department_id'  => 'required|exists:departments_hr2,department_id',
        'specialization' => 'nullable|string|max:255',
    ]);

    
    $department = Department::where('department_id', $validated['department_id'])->first();

    if (!$department) {
        return redirect()->back()->with('error', 'Selected department not found.');
    }

    SuccessionPosition::create([
        'position_id'      => $department->department_id,  // store the dept code as position_id if needed
        'position_title'   => $validated['position_title'],
        'department_id'    => $department->department_id,
        'department_name'  => $department->name,          // store department name
        'specialization'   => $validated['specialization'] ?? null,
        'criticality'      => $validated['criticality'],
        'is_active'        => 1,
    ]);

    return redirect()->back()->with('success', 'Succession position added successfully.');
}
    /**
     * Store a new Candidate for a Position
     */
    public function storeCandidate(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'position_id'      => 'required|exists:succession_positions_hr2,id',
            'employee_id'      => 'required|exists:employees,id',
            'readiness'        => 'required|in:Ready Now,1-2 Years,3+ Years,Emergency',
            'perf_score'       => 'required|numeric|min:1|max:10',
            'pot_score'        => 'required|numeric|min:1|max:10',
            'retention_risk'   => 'required|in:High,Medium,Low',
            'effective_at'     => 'required|date',
            'development_plan' => 'nullable|string|max:500',
        ]);

        SuccessorCandidate::create([
            'position_id'       => $validated['position_id'],
            'employee_id'       => $validated['employee_id'],
            'readiness'         => $validated['readiness'],
            'performance_score' => $validated['perf_score'],
            'potential_score'   => $validated['pot_score'],
            'retention_risk'    => $validated['retention_risk'],
            'effective_at'      => $validated['effective_at'],
            'development_plan'  => $validated['development_plan'] ?? null,
            'is_active'         => 1,
        ]);

        return redirect()->back()->with('success', 'Candidate added successfully.');
    }

    /**
     * Archive / Delete a Succession Position
     */
    public function destroyPosition($id)
    {
        $this->checkAccess();

        $position = SuccessionPosition::findOrFail($id);
        $position->is_active = 0;
        $position->save();

        return redirect()->back()->with('success', 'Position archived successfully.');
    }

    /**
     * Remove Candidate from pipeline
     */
    public function destroyCandidate($id)
    {
        $this->checkAccess();

        $candidate = SuccessorCandidate::findOrFail($id);
        $candidate->is_active = 0;
        $candidate->save();

        return redirect()->back()->with('success', 'Candidate removed successfully.');
    }
}