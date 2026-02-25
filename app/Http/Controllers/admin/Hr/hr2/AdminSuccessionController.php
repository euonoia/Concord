<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\SuccessionPosition;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\Employee; 
use Illuminate\Support\Facades\Auth;

class AdminSuccessionController extends Controller
{
    /**
     * Standard Authorization check used by all methods
     */
    private function checkAccess()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized access to HR2 Succession Planning.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        $positions = SuccessionPosition::withCount('candidates')->orderBy('position_title')->get();
        
        // Ordered by readiness: Ready Now first
        $candidates = SuccessorCandidate::with(['position', 'employee'])
            ->get()
            ->sortBy(function($candidate) {
                $order = ['Ready Now' => 1, '1-2 Years' => 2, '3+ Years' => 3, 'Emergency' => 4];
                return $order[$candidate->readiness] ?? 5;
            });
            
        $employees = Employee::orderBy('first_name')->get();

        return view('admin.hr2.succession', compact('positions', 'candidates', 'employees'));
    }

      public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'criticality'    => 'required|in:low,medium,high',
        ]);

        $branch_id = 'BR' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        SuccessionPosition::create([
            'position_title' => $validated['position_title'],
            'branch_id'      => $branch_id,
            'criticality'    => $validated['criticality'],
        ]);

        return redirect()->back()->with('success', 'Succession position added successfully.');
    }

    public function storeCandidate(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'position_id'      => 'required|exists:succession_positions_hr2,branch_id',
            'employee_id'      => 'required|exists:employees,id',
            'readiness'        => 'required|in:Ready Now,1-2 Years,3+ Years,Emergency',
            'perf_score'       => 'required|numeric|min:1|max:10',
            'pot_score'        => 'required|numeric|min:1|max:10',
            'retention_risk'   => 'required|in:High,Medium,Low',
            'effective_at'     => 'required|date',
            'development_plan' => 'nullable|string',
        ]);

        SuccessorCandidate::create([
            'branch_id'         => $validated['position_id'],
            'employee_id'       => $validated['employee_id'],
            'readiness'         => $validated['readiness'],
            'performance_score' => $validated['perf_score'],
            'potential_score'   => $validated['pot_score'],
            'retention_risk'    => $validated['retention_risk'],
            'effective_at'      => $validated['effective_at'],
            'development_plan'  => $validated['development_plan'],
            'is_active'         => 1,
        ]);

        return redirect()->back()->with('success', 'Candidate added successfully.');
    }
    
   
}