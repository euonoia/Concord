<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr3\Shift;
use Illuminate\Support\Facades\Auth;

class AdminDirectCompensationController extends Controller
{
    /**
     * Ensure user is HR4 admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized access to HR4 Direct Compensation.');
        }
    }

    /**
     * Show all direct compensations for a given month
     */
    public function index(Request $request)
    {
        $this->authorizeHrAdmin(); // <-- Role check

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
        $this->authorizeHrAdmin(); 

        $month = $request->input('month', date('Y-m'));
        $employees = Employee::all();

        foreach ($employees as $emp) {
            $position = DepartmentPositionTitle::find($emp->position_id);

            $base_salary = $position->base_salary ?? 0;
            $shift_allowance = Shift::calculateMonthlyShiftAllowance($emp->employee_id, $month);
            $bonus = 0;

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