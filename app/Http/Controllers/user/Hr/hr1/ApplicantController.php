<?php

namespace App\Http\Controllers\user\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ApplicantController extends Controller
{
    public function showApplicationForm(Request $request)
    {
        // Get department from query parameter or null
        $dept = $request->query('dept', null);

        // Fetch all active departments
        $departments = DB::table('departments_hr2')
            ->where('is_active', 1)
            ->get();

        // Initialize positions and specializations as empty collections
        $positions = collect();
        $specializations = collect();

        // Fetch positions and specializations only if a department is selected
        if ($dept) {
            // Positions table still uses department_id
            $positions = DB::table('department_position_titles_hr2')
                ->where('department_id', $dept)
                ->where('is_active', 1)
                ->get();

            // Specializations table uses dept_code
            $specializations = DB::table('department_specializations_hr2')
                ->where('dept_code', $dept)
                ->where('is_active', 1)
                ->get();
        }

        // Pass all data to view
        return view('hr.hr1.apply', compact('departments', 'positions', 'specializations', 'dept'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'department_id'  => 'required',
            'position_id'    => 'required',
            'specialization' => 'nullable|string|max:255',
        ]);

        DB::table('applicants_hr1')->insert([
            'application_id'   => 'APP-' . Str::upper(Str::random(8)),
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'department_id'    => $request->department_id,
            'position_id'      => $request->position_id,
            'specialization'   => $request->specialization,
            'post_grad_status' => 'residency',
            'application_status'=> 'pending',
            'applied_at'       => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }
}