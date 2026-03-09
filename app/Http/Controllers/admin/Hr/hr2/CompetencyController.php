<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr2\Competency;
use App\Models\admin\Hr\hr2\Department;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    public function index()
    {
        $competencies = Competency::with('department')
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::where('is_active', 1)->get();

        return view('admin.hr2.competencies', compact('competencies', 'departments'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'dept_code' => 'required|string|max:50',
            'specialization_name' => 'nullable|string|max:255',
            'competency_group' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Generate competency_code automatically: DEPT-SPE-001
        $dept = strtoupper($request->dept_code);
        $spec = $request->specialization_name ? strtoupper(substr($request->specialization_name,0,3)) : 'GEN';
        $prefix = $dept . '-' . $spec;

        $last = Competency::where('competency_code', 'like', $prefix.'%')
            ->orderBy('competency_code', 'desc')
            ->first();

        $nextNumber = $last ? str_pad(intval(substr($last->competency_code,-3))+1, 3, '0', STR_PAD_LEFT) : '001';
        $competencyCode = $prefix . '-' . $nextNumber;

        // Save
        Competency::create([
            'competency_code' => $competencyCode,
            'name' => $request->input('title'),
            'department_id' => $request->input('dept_code'),
            'specialization_name' => $request->input('specialization_name'),
            'competency_group' => $request->input('competency_group'),
            'description' => $request->input('description'),
            'rotation_order' => 0,
            'is_active' => 1,
        ]);

        return redirect()->back()->with('success', "Competency created successfully. Code: {$competencyCode}");
    }

    public function destroy($id)
    {
        $competency = Competency::findOrFail($id);
        $competency->delete();

        return back()->with('info','Competency removed.');
    }

    // AJAX: load specializations by department
    public function getSpecializations($dept)
    {
        $department = Department::where('department_id', $dept)->first();
        $specs = $department && $department->specializations
            ? $department->specializations->pluck('specialization_name')
            : collect();

        return response()->json($specs);
    }
}