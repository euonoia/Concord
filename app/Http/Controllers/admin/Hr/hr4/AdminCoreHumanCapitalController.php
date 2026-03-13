<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;

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
    $this->authorizeHrAdmin();

    $employees = Employee::with(['position', 'position.department'])->get()->unique('id')->values();
    $departments = \App\Models\admin\Hr\hr2\Department::all();
    $positions = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::with('department')->get()->unique('id')->values();

    // Available specializations per department
    $availableSpecializations = [];
    foreach ($departments as $d) {
        $deptSpecs = DepartmentSpecialization::where('dept_code', $d->department_id)
            ->where('is_active', 1)->get();

        $assignedSpecs = Employee::where('department_id', $d->department_id)
            ->whereNotNull('specialization')
            ->pluck('specialization')
            ->map(fn($s) => trim($s))
            ->unique()
            ->toArray();

        $available = $deptSpecs->filter(fn($s) => !in_array($s->specialization_name, $assignedSpecs))->values();
        $availableSpecializations[$d->id] = $available;
    }

    // Vacant positions with slot computation
    $vacantPositions = $positions->map(function ($p) {
        $assigned = Employee::where('position_id', $p->id)->count();
        $rawMax = isset($p->max_slots) && $p->max_slots ? (int) $p->max_slots : 10;
        $max = max(10, min(30, $rawMax));
        $p->assigned_count = $assigned;
        $p->available_slots = max(0, $max - $assigned);
        $p->max_slots = $max;
        return $p;
    })->filter(fn($p) => $p->available_slots > 0)->values();

    // Department summary with total slots
    $departmentSummary = $departments->map(function ($d) use ($positions, $employees, $availableSpecializations) {
        $deptPositions = $positions->where('department_id', $d->department_id);

        $totalMax = $deptPositions->sum(function ($p) {
            $raw = isset($p->max_slots) && $p->max_slots ? (int) $p->max_slots : 10;
            return max(10, min(30, $raw));
        });

        $totalAssigned = $employees->where('department_id', $d->department_id)->count();
        $totalAvailable = max(0, $totalMax - $totalAssigned);
        $openSpecs = isset($availableSpecializations[$d->id])
            ? $availableSpecializations[$d->id]->count()
            : 0;

        return (object) [
            'department_id'             => $d->department_id,
            'department_name'           => $d->name,
            'assigned'                  => $totalAssigned,
            'max'                       => $totalMax,
            'available'                 => $totalAvailable,
            'available_specializations' => $openSpecs,
        ];
    });

    return view('admin.hr4.core_human_capital', compact(
        'employees',
        'departments',
        'positions',
        'availableSpecializations',
        'vacantPositions',
        'departmentSummary'
    ));
}
}