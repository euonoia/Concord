<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class UserSuccessionController extends Controller
{
    public function index()
    {
        // 1. Get the Employee profile linked to the logged-in User
        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return view('hr.hr2.succession', ['nominations' => collect([])]);
        }

        // 2. Fetch nominations using the Employee ID
        $nominations = SuccessorCandidate::query()
            ->join('succession_positions_hr2 as p', 'p.branch_id', '=', 'successor_candidates_hr2.branch_id')
            ->where('successor_candidates_hr2.employee_id', $employee->id) 
            ->where('successor_candidates_hr2.is_active', 1)
            ->select([
                'successor_candidates_hr2.*',
                'p.position_title',
                'p.criticality',
            ])
            ->orderBy('successor_candidates_hr2.effective_at', 'asc')
            ->get();

        return view('hr.hr2.succession', compact('nominations'));
    }
}