<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

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

        // Load data
        $employees = Employee::with(['position', 'position.department'])->get();
        $departments = \App\Models\admin\Hr\hr2\Department::all();
        $positions = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::with('department')->get();

        // Return view (compact fully closed)
        return view('admin.hr4.core_human_capital', compact(
            'employees',
            'departments',
            'positions'
        ));
    }
}