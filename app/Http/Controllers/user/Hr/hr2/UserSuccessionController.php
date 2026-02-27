<?php

namespace App\Http\Controllers\user\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserSuccessionController extends Controller
{
    public function index()
    {
        // 1. Get the Employee profile linked to the logged-in User
        $employee = Employee::where('user_id', Auth::id())->first();

       

        if (!$employee) {
            return view('hr.hr2.succession', ['nominations' => collect([])]);
        }

        // 2. Fetch nominations using a more "forgiving" Left Join
        $nominations = SuccessorCandidate::query()
          
            ->leftJoin('department_position_titles_hr2 as target_pos', 'target_pos.id', '=', 'successor_candidates_hr2.position_id')
            
            ->leftJoin('employees as emp', 'emp.employee_id', '=', 'successor_candidates_hr2.employee_id')
            
            ->leftJoin('department_position_titles_hr2 as current_pos', 'current_pos.id', '=', 'emp.position_id')
            
            ->where('successor_candidates_hr2.employee_id', trim($employee->employee_id)) 
            ->where('successor_candidates_hr2.is_active', 1)
            ->select([
                'successor_candidates_hr2.*',
                'target_pos.position_title as target_position_title',
                'current_pos.position_title as current_position_title',
            ])
            ->get();

        return view('hr.hr2.succession', compact('nominations'));
    }
}