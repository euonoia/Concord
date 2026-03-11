<?php
namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use App\Models\Employee;

class AdminCompetencyVerificationController extends Controller
{
    public function index(Request $request)
    {
        // Start the query with the Join
        $query = EmployeeCompetencyCompletion::leftJoin(
                'employees',
                'employees.employee_id',
                '=',
                'employee_competency_completion_hr2.employee_id'
            )
            ->select(
                'employee_competency_completion_hr2.*',
                'employees.first_name',
                'employees.last_name',
                'employees.department_id',
                'employees.specialization'
            );

        // --- SCALING FEATURE: SEARCH ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employees.first_name', 'like', "%{$search}%")
                  ->orWhere('employees.last_name', 'like', "%{$search}%")
                  ->orWhere('employees.employee_id', 'like', "%{$search}%");
            });
        }

        // --- SCALING FEATURE: STATUS FILTER ---
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('employee_competency_completion_hr2.verified_by');
            } elseif ($request->status === 'pending') {
                $query->whereNull('employee_competency_completion_hr2.verified_by');
            }
        }

        // Use Paginate instead of Get (15 records per page)
        $completions = $query->orderBy('employee_competency_completion_hr2.created_at', 'desc')
                             ->paginate(15)
                             ->withQueryString(); // Keeps search filters when clicking "Next Page"

        return view('admin.hr2.competency_verification.index', compact('completions'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000'
        ]);

        $completion = EmployeeCompetencyCompletion::findOrFail($id);
        
        // Get the logged-in user's employee record to act as the "Verifier"
        $verifier = Employee::where('user_id', Auth::id())->first();

        if (!$verifier) {
            return back()->with('error', 'Verifier record not found.');
        }

        $completion->update([
            'verified_by' => $verifier->employee_id,
            'verification_notes' => $request->verification_notes,
            'status' => 'completed',
            'verified_at' => now(), // Good practice to track WHEN it was verified
        ]);

        return redirect()->back()->with('success', 'Competency verified successfully.');
    }
}