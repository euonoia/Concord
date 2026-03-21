<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetencyController extends Controller
{
   public function index(Request $request)
{
    $departments = DB::table('departments_hr2')
        ->where('is_active', 1)
        ->get();

    $deptCode = $request->query('dept_code', null);
    $specialization = $request->query('specialization', null);

    $competenciesQuery = DB::table('competency_hr2 as c')

        // ✅ JOIN departments (FIXED COLUMN)
        ->leftJoin('departments_hr2 as d', 'c.department_id', '=', 'd.department_id')

        // ✅ JOIN specialization mapping
        ->join('department_specializations_hr2 as s', function($join){
            $join->on('c.department_id', '=', 's.dept_code')
                 ->on('c.specialization_name', '=', 's.specialization_name');
        })

        // ✅ JOIN new hires (FIXED COLUMN)
        ->leftJoin('new_hires_hr1 as n', function($join){
            $join->on('c.department_id', '=', 'n.department_id')
                 ->on('c.specialization_name', '=', 'n.specialization');
        })

        ->select(
            'c.*',
            'd.name as department_name',
            'c.specialization_name',
            DB::raw('COUNT(n.id) as new_hire_count')
        )
        ->groupBy(
            'c.id',
            'd.name',
            'c.specialization_name'
        );

    // ✅ FILTERS
    if($deptCode){
        $competenciesQuery->where('c.department_id', $deptCode);
    }

    if($specialization){
        $competenciesQuery->where('c.specialization_name', $specialization);
    }

    $competencies = $competenciesQuery
        ->orderBy('c.created_at', 'desc')
        ->get();

    return view('admin.hr2.competencies', compact(
        'competencies',
        'departments',
        'deptCode',
        'specialization'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'dept_code' => 'required|string|max:50',
            'specialization_name' => 'nullable|string|max:255',
            'competency_group' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $dept = strtoupper($request->dept_code);
        $spec = $request->specialization_name ? strtoupper(substr($request->specialization_name,0,3)) : 'GEN';
        $prefix = $dept . '-' . $spec;

        $last = DB::table('competency_hr2')
            ->where('competency_code', 'like', $prefix.'%')
            ->orderBy('competency_code', 'desc')
            ->first();

        $nextNumber = $last ? str_pad(intval(substr($last->competency_code,-3))+1, 3, '0', STR_PAD_LEFT) : '001';
        $competencyCode = $prefix . '-' . $nextNumber;

        DB::table('competency_hr2')->insert([
            'competency_code' => $competencyCode,
            'name' => $request->title,
            'department_id' => $request->dept_code,
            'specialization_name' => $request->specialization_name,
            'competency_group' => $request->competency_group,
            'description' => $request->description,
            'rotation_order' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Competency created successfully. Code: {$competencyCode}");
    }

    public function destroy($id)
    {
        DB::table('competency_hr2')->where('id', $id)->delete();
        return back()->with('info','Competency removed.');
    }

    // AJAX: Load specializations by department
    public function getSpecializations($dept)
    {
        $specs = DB::table('department_specializations_hr2')
            ->where('dept_code', $dept)
            ->where('is_active', 1)
            ->pluck('specialization_name');

        return response()->json($specs);
    }
}