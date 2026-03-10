<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\EmployeeCompetencyCompletion;
use App\Models\Employee;

class AdminCompetencyVerificationController extends Controller
{

    public function index()
    {

        $completions = EmployeeCompetencyCompletion::leftJoin(
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
            )
            ->orderBy('employee_competency_completion_hr2.created_at', 'desc')
            ->get();

        return view(
            'admin.hr2.competency_verification.index',
            compact('completions')
        );
    }


    public function verify(Request $request, $id)
    {

        $request->validate([
            'verification_notes' => 'nullable|string|max:1000'
        ]);

        $completion = EmployeeCompetencyCompletion::findOrFail($id);

        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return back()->with('error','Verifier employee record not found.');
        }

        $completion->verified_by = $employee->employee_id;

        $completion->verification_notes = $request->verification_notes;

        $completion->status = 'completed';

        $completion->save();

        return redirect()
            ->route('admin.hr2.competency.verification.index')
            ->with('success','Competency verified successfully.');
    }
}