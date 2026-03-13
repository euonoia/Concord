<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle as Position;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        // Keep the landing page focused and light-weight. Vacancy and recruitment
        // details are presented on the dedicated Careers pages where they belong.
        $departments = Department::all();
        return view('landing.landing', compact('departments'));
    }
}
