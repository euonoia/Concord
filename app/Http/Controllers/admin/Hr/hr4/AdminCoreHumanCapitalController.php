<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminCoreHumanCapitalController extends Controller
{
    /**
     * Ensure user is HR admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized access to HR4 Core Human Capital.');
        }
    }

    /**
     * Display employees with departments and positions
     */
    public function index()
    {
        // Check role
        $this->authorizeHrAdmin();

        // Fetch employees data from Core Human Capital
        $employees = Employee::with('department', 'position')->get();
        $departments = Department::all();
        $positions = DepartmentPositionTitle::with('department')->get();
        $users = User::all();

        // Fetch HR1 employees dynamically
        $hr1_employees = DB::table('new_hires_hr1')->select('id', 'first_name', 'last_name')->get();

        // Return view (compact fully closed)
        return view('admin.hr4.core_human_capital', compact(
            'employees',
            'departments',
            'positions',
            'hr1_employees',
            'users'
        ));
    }
}