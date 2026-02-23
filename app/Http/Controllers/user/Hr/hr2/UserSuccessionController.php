<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\user\Hr\hr2\SuccessorCandidate;
use Illuminate\Support\Facades\Auth;

class UserSuccessionController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('portal.login');
        }

        $employee = Auth::user();

        // Using Eloquent Join to fetch position details for the nominated candidate
        $nominations = SuccessorCandidate::query()
            ->join('succession_positions_hr2 as p', 'p.branch_id', '=', 'successor_candidates_hr2.branch_id')
            ->where('successor_candidates_hr2.employee_id', $employee->id) // Using id to match your Auth structure
            ->select([
                'successor_candidates_hr2.*',
                'p.position_title',
                'p.criticality',
                'p.branch_id as pos_branch_id',
            ])
            ->orderBy('p.position_title')
            ->get();

        return view('hr.hr2.succession', compact('nominations'));
    }
}