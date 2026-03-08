<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicantManagementController extends Controller
{
    /**
     * Check if user is authorized for admin_hr1 role.
     */
    private function authorizeHr1Admin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr1') {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a listing of applicants with optional filters.
     */
    public function index(Request $request)
    {
        $this->authorizeHr1Admin(); // <-- Authorization

        $department = $request->input('department');
        $position = $request->input('position');

        // Get all departments for filter dropdown
        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Get all positions for filter dropdown
        $positions = DB::table('department_position_titles_hr2')
            ->where('is_active', 1)
            ->orderBy('position_title')
            ->get();

        // Base query for applicants
        $query = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('department_position_titles_hr2', 'applicants_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
            );

        // Apply filters if present
        if ($department) {
            $query->where('applicants_hr1.department_id', $department);
        }

        if ($position) {
            $query->where('applicants_hr1.position_id', $position);
        }

        // Paginate results
        $applicants = $query->orderByDesc('applicants_hr1.id')->paginate(10);

        return view('admin.hr1.applicants.index', compact(
            'applicants',
            'departments',
            'positions',
            'department',
            'position'
        ));
    }

    /**
     * Display a single applicant.
     */
    public function show($id)
    {
        $this->authorizeHr1Admin(); // <-- Authorization

        $applicant = DB::table('applicants_hr1')
            ->leftJoin('departments_hr2', 'applicants_hr1.department_id', '=', 'departments_hr2.department_id')
            ->leftJoin('department_position_titles_hr2', 'applicants_hr1.position_id', '=', 'department_position_titles_hr2.id')
            ->select(
                'applicants_hr1.*',
                'departments_hr2.name as department_name',
                'department_position_titles_hr2.position_title'
            )
            ->where('applicants_hr1.id', $id)
            ->first();

        if (!$applicant) abort(404);

        return view('admin.hr1.applicants.show', compact('applicant'));
    }
}