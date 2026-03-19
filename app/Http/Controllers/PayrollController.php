<?php
namespace App\Http\Controllers;

use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\admin\Hr\hr3\AttendanceLog;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * AJAX: Get fixed compensation for employee
     */
    public function getSalary($employeeId)
    {
        $comp = DirectCompensation::where('employee_id', $employeeId)
            ->orderByDesc('month')
            ->first();
        if ($comp) {
            $salary = $comp->base_salary + $comp->shift_allowance + $comp->overtime_pay + $comp->bonus;
            return response()->json([
                'salary' => $salary,
                'info' => 'Base: ₱' . number_format($comp->base_salary,2) . ', Allowance: ₱' . number_format($comp->shift_allowance,2) . ', OT: ₱' . number_format($comp->overtime_pay,2) . ', Bonus: ₱' . number_format($comp->bonus,2)
            ]);
        }
        return response()->json(['salary' => null, 'info' => null]);
    }

    /**
     * AJAX: Get attendance logs for employee
     */
    public function getAttendance($employeeId)
    {
        $attendances = AttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('clock_in', now()->month)
            ->whereYear('clock_in', now()->year)
            ->orderBy('clock_in', 'desc')
            ->get();

        $totalDays = $attendances->count();
        $totalHours = 0;

        foreach ($attendances as $att) {
            if ($att->clock_in && $att->clock_out) {
                $hours = $att->clock_in->diffInHours($att->clock_out);
                $totalHours += $hours;
            }
        }

        return response()->json([
            'attendances' => $attendances->map(function ($att) {
                return [
                    'date' => $att->clock_in ? $att->clock_in->format('Y-m-d') : null,
                    'clock_in' => $att->clock_in ? $att->clock_in->format('H:i') : null,
                    'clock_out' => $att->clock_out ? $att->clock_out->format('H:i') : null,
                    'hours' => $att->clock_in && $att->clock_out ? $att->clock_in->diffInHours($att->clock_out) : 0,
                ];
            }),
            'total_days' => $totalDays,
            'total_hours' => $totalHours,
        ]);
    }

    /**
     * AJAX: Get employee's position
     */
    public function getEmployeePosition($employeeId)
    {
        $employee = Employee::find($employeeId);
        return response()->json([
            'position_id' => $employee ? $employee->position_id : null
        ]);
    }

    /**
     * AJAX: Get position salary
     */
    public function getPositionSalary($positionId)
    {
        $position = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::find($positionId);
        return response()->json([
            'salary' => $position ? $position->base_salary : null,
            'info' => $position ? $position->position_title . ' (₱' . number_format($position->base_salary,2) . ')' : null
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payrolls = Payroll::with('employee')->orderBy('pay_date', 'desc')->get();
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('payroll.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric',
            'deductions' => 'nullable|numeric',
            'pay_date' => 'required|date',
        ]);

        $deductions = $validated['deductions'] ?? 0;
        $net_pay = $validated['salary'] - $deductions;

        Payroll::create([
            'employee_id' => $validated['employee_id'],
            'salary' => $validated['salary'],
            'deductions' => $deductions,
            'net_pay' => $net_pay,
            'pay_date' => $validated['pay_date'],
        ]);

        // Update DirectCompensation for this employee
        $comp = \App\Models\admin\Hr\hr4\DirectCompensation::where('employee_id', $validated['employee_id'])
            ->orderByDesc('month')
            ->first();
        if ($comp) {
            $comp->base_salary = $validated['salary'];
            $comp->save();
        }

        return redirect()->route('hr4.payroll.index')->with('success', 'Payroll created and compensation updated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Payroll Reports
     */
    public function reports(Request $request)
    {
        $query = Payroll::with(['employee.position', 'employee.department']);

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('position_id', $request->position_id);
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('pay_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('pay_date', '<=', $request->end_date);
        }

        $payrolls = $query->orderBy('pay_date', 'desc')->get();

        // Calculate totals
        $totalSalary = $payrolls->sum('salary');
        $totalDeductions = $payrolls->sum('deductions');
        $totalNetPay = $payrolls->sum('net_pay');

        $employees = Employee::all();
        $positions = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::all();
        $departments = \App\Models\admin\Hr\hr2\Department::all();

        return view('payroll.reports', compact('payrolls', 'employees', 'positions', 'departments', 'totalSalary', 'totalDeductions', 'totalNetPay'));
    }
}
