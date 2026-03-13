<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\Employee;

class CareersController extends Controller
{
    /**
     * Show residency & fellowship vacancies (public)
     */
    public function residency()
    {
        $departments = Department::all();

        // Available specializations per department
        $vacantSpecializations = [];
        foreach ($departments as $d) {
            $deptSpecs = DepartmentSpecialization::where('dept_code', $d->department_id)
                ->where('is_active', 1)
                ->get();

            $assignedSpecs = Employee::where('department_id', $d->department_id)
                ->whereNotNull('specialization')
                ->pluck('specialization')
                ->map(fn($s) => trim((string) $s))
                ->unique()
                ->toArray();

            $available = $deptSpecs->filter(function ($s) use ($assignedSpecs) {
                return !in_array($s->specialization_name, $assignedSpecs);
            })->values();

            if ($available->isNotEmpty()) {
                $vacantSpecializations[] = [
                    'department' => $d,
                    'specializations' => $available,
                ];
            }
        }

        // Vacant positions (available slots)
        $positions = DepartmentPositionTitle::with('department')->get();
        $vacantPositions = $positions->map(function ($p) {
            $assigned = Employee::where('position_id', $p->id)->count();
            // Enforce sensible capacity bounds: default to 10 when unset, clamp to [10,30]
            $rawMax = isset($p->max_slots) && $p->max_slots ? (int) $p->max_slots : 10;
            $max = max(10, min(30, $rawMax));
            $available = max(0, $max - $assigned);
            $p->assigned_count = $assigned;
            $p->available_slots = $available;
            $p->max_slots = $max;
            return $p;
        })->filter(function ($p) {
            return $p->available_slots > 0;
        })->values();

        return view('hr.hr1.residency_fellowship', compact('vacantPositions', 'vacantSpecializations'));
    }
}
