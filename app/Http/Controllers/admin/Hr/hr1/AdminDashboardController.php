<?php

namespace App\Http\Controllers\admin\Hr\hr1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr2\DepartmentSpecialization;
use App\Models\Employee;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $departmentSummaries = [];

        foreach ($departments as $d) {
            $assignedCount = Employee::where('department_id', $d->department_id)->count();

            $pos = DepartmentPositionTitle::where('department_id', $d->department_id)->get();
            $totalMax = 0;
            foreach ($pos as $pp) {
                $totalMax += isset($pp->max_slots) && $pp->max_slots ? (int) $pp->max_slots : 1;
            }

            $availableSlots = max(0, $totalMax - $assignedCount);

            $availableSpecsCount = DepartmentSpecialization::where('dept_code', $d->department_id)
                ->where('is_active', 1)
                ->whereNotIn('specialization_name', Employee::where('department_id', $d->department_id)
                    ->whereNotNull('specialization')
                    ->pluck('specialization')
                    ->map(fn($s) => trim($s))->toArray()
                )->count();

            $departmentSummaries[] = [
                'department' => $d,
                'assigned' => $assignedCount,
                'max' => $totalMax,
                'available' => $availableSlots,
                'available_specializations' => $availableSpecsCount,
            ];
        }

        return view('admin.hr1.dashboard', compact('departmentSummaries'));
    }
}
