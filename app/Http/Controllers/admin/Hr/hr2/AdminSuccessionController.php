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
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        $positions = SuccessionPosition::withCount('candidates')->orderBy('position_title')->get();
        $candidates = SuccessorCandidate::with(['position', 'employee'])
            ->orderBy('branch_id')
            ->get();
            
        // Assuming your employee data is in the User model or a specific Employee model
        $employees = Employee::orderBy('first_name')->get();

        return view('admin.hr2.succession', compact('positions', 'candidates', 'employees'));
    }

    public function storePosition(Request $request)
    {
        $this->authorizeHrAdmin();

        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'criticality'    => 'required|in:low,medium,high',
        ]);

        // Generate a random Branch ID
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
        $this->authorizeHrAdmin();

        $validated = $request->validate([
            'position_id'      => 'required|exists:succession_positions_hr2,branch_id',
            'employee_id'      => 'required|exists:users,id', // Update table name to your actual employee/user table
            'readiness'        => 'required|in:ready,not_ready',
            'effective_at'     => 'required|date',
            'development_plan' => 'nullable|string',
        ]);

        SuccessorCandidate::create([
            'branch_id'        => $validated['position_id'],
            'employee_id'      => $validated['employee_id'],
            'readiness'        => $validated['readiness'],
            'effective_at'     => $validated['effective_at'],
            'development_plan' => $validated['development_plan'],
        ]);

        return redirect()->back()->with('success', 'Candidate added to succession plan.');
    }

    public function destroyPosition($id)
    {
        $this->authorizeHrAdmin();
        SuccessionPosition::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Position removed.');
    }

    public function destroyCandidate($id)
    {
        $this->authorizeHrAdmin();
        SuccessorCandidate::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Candidate removed.');
    }
}