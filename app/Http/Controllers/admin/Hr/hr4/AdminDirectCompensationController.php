<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr3\Shift;
use Carbon\Carbon;

class AdminDirectCompensationController extends Controller
{
    /**
     * Show all direct compensations for a given month
     */
    public function index(Request $request)
    {
        // Default to current year-month if not specified
        $month = $request->query('month', date('Y-m'));

        $compensations = DirectCompensation::with('employee')
            ->where('month', $month)
            ->orderBy('employee_id')
            ->get();

        return view('admin.hr4.compensations', compact('compensations', 'month'));
    }

    /**
     * Generate monthly compensation for all employees
     */
    public function generate(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $employees = Employee::all();

        foreach ($employees as $emp) {
            $position = DepartmentPositionTitle::find($emp->position_id);

            // Base salary from department-position table
            $base_salary = $position->base_salary ?? 0;

            // Use Shift model methods for allowance and overtime
            $shift_allowance = Shift::calculateMonthlyShiftAllowance($emp->employee_id, $month);

            $bonus = 0; // can extend later for bonuses

            // Insert or update compensation
            DirectCompensation::updateOrCreate(
                ['employee_id' => $emp->employee_id, 'month' => $month],
                [
                    'base_salary' => $base_salary,
                    'shift_allowance' => $shift_allowance,
                    'bonus' => $bonus
                ]
            );
        }

        return redirect()->back()->with('success', "Monthly compensation generated for {$month}.");
    }
}