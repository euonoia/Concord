<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class AdminCoreHumanCapitalController extends Controller
{

    private function checkAccess()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized access to HR4 Core Human Capital.');
        }
    }

        public function index()
        {
            $employees = Employee::with(['position','position.department'])->get();
            $departments = \App\Models\admin\Hr\hr2\Department::all();
            $positions = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::with('department')->get();

            return view('admin.hr4.core_human_capital', compact(
                'employees',
                'departments',
                'positions'
            ));
        }
}