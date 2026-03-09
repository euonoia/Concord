<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr2\Competency;
use App\Models\Employee;

class UserCompetencyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('portal.login');
        }

        // Get employee record linked to this user
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return view('hr.hr2.competencies', [
                'competencies' => collect(),
                'error' => 'Employee record not found.'
            ]);
        }

        // Fetch competencies based on employee department and specialization
        $competencies = Competency::where('department_id', $employee->department_id)
            ->where('specialization_name', $employee->specialization)
            ->where('is_active', 1)
            ->orderBy('rotation_order', 'asc')
            ->get();

        return view('hr.hr2.competencies', compact('competencies'));
    }
}