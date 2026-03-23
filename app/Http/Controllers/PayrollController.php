<?php
namespace App\Http\Controllers;

use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr3\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $salary = $comp->base_salary + $comp->shift_allowance + $comp->overtime_pay + $comp->night_diff_pay + $comp->bonus;
            return response()->json([
                'salary' => $salary,
                'info' => 'Base: ₱' . number_format($comp->base_salary,2) . ', Allowance: ₱' . number_format($comp->shift_allowance,2) . ', OT: ₱' . number_format($comp->overtime_pay,2) . ', Night Diff: ₱' . number_format($comp->night_diff_pay,2) . ', Bonus: ₱' . number_format($comp->bonus,2)
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

        // Get compensation data with hours summary
        $comp = \App\Models\admin\Hr\hr4\DirectCompensation::where('employee_id', $employeeId)
            ->where('month', now()->format('Y-m'))
            ->first();

        $totalDays = $attendances->count();
        $totalHours = 0;

        foreach ($attendances as $att) {
            if ($att->clock_in && $att->clock_out) {
                $hours = $att->clock_in->diffInMinutes($att->clock_out) / 60;
                $totalHours += $hours;
            }
        }

        $hoursSummary = \App\Helpers\AttendanceHelper::getMonthlyHoursSummary($employeeId, now()->format('Y-m'));

        return response()->json([
            'attendances' => $attendances->map(function ($att) {
                $attWorked = $att->worked_hours ?? ($att->clock_in && $att->clock_out ? $att->clock_in->diffInMinutes($att->clock_out) / 60 : 0);
                $attOvertime = $att->overtime_hours ?? \App\Helpers\AttendanceHelper::calculateOvertimeHours($attWorked);
                $attNightDiff = $att->night_diff_hours ?? ($att->clock_in && $att->clock_out ? \App\Helpers\AttendanceHelper::calculateNightDiffHours($att->clock_in, $att->clock_out) : 0);
                return [
                    'date' => $att->clock_in ? $att->clock_in->format('Y-m-d') : null,
                    'clock_in' => $att->clock_in ? $att->clock_in->format('H:i') : null,
                    'clock_out' => $att->clock_out ? $att->clock_out->format('H:i') : null,
                    'hours' => $att->clock_in && $att->clock_out ? round($att->clock_in->diffInMinutes($att->clock_out) / 60, 2) : 0,
                    'worked_hours' => round($attWorked, 2),
                    'overtime_hours' => round($attOvertime, 2),
                    'night_diff_hours' => round($attNightDiff, 2),
                ];
            }),
            'total_days' => $totalDays,
            'total_hours' => round($totalHours, 2),
            'worked_hours' => $comp->worked_hours ?? $hoursSummary['worked_hours'],
            'overtime_hours' => $comp->overtime_hours ?? $hoursSummary['overtime_hours'],
            'night_diff_hours' => $comp->night_diff_hours ?? $hoursSummary['night_diff_hours'],
            'base_salary' => $comp->base_salary ?? 0,
            'shift_allowance' => $comp->shift_allowance ?? 0,
            'overtime_pay' => $comp->overtime_pay ?? 0,
            'night_diff_pay' => $comp->night_diff_pay ?? 0,
            'bonus' => $comp->bonus ?? 0,
            'training_reward' => $comp->training_reward ?? 0,
            'total_compensation' => $comp ? $comp->total_compensation : 0,
        ]);
    }

    /**
     * AJAX: Get employee's position
     */
    public function getEmployeePosition($employeeId)
    {
        // Find employee by employee_id (custom ID like "EMP001"), not database id
        $employee = Employee::where('employee_id', $employeeId)->first();
        if (!$employee) {
            return response()->json([
                'position_id' => null,
                'position_title' => 'N/A',
                'salary' => 0
            ]);
        }

        $position = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::find($employee->position_id);
        
        return response()->json([
            'position_id' => $employee->position_id,
            'position_title' => $position ? $position->position_title : 'N/A',
            'salary' => $position ? $position->base_salary : 0
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
            'employee_id' => 'required|exists:employees,employee_id',
            'salary' => 'required|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
            'worked_hours' => 'nullable|numeric',
            'overtime_hours' => 'nullable|numeric',
            'night_diff_hours' => 'nullable|numeric',
        ]);

        // Find employee by custom employee_id to get database id
        $employee = Employee::where('employee_id', $validated['employee_id'])->first();
        if (!$employee) {
            return redirect()->back()->withError('Employee not found');
        }

        $deductions = $validated['deductions'] ?? 0;
        $net_pay = $validated['salary'] - $deductions;

        // Create Payroll record using employee database id
        Payroll::create([
            'employee_id' => $employee->id,
            'salary' => $validated['salary'],
            'deductions' => $deductions,
            'net_pay' => $net_pay,
            'pay_date' => $validated['pay_date'],
        ]);

        // Update or create DirectCompensation record for this month
        // Use the custom employee_id for the FK constraint
        $month = Carbon::parse($validated['pay_date'])->format('Y-m');
        \App\Models\admin\Hr\hr4\DirectCompensation::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'month' => $month,
            ],
            [
                'base_salary' => $validated['salary'],
                'worked_hours' => $validated['worked_hours'] ?? 0,
                'overtime_hours' => $validated['overtime_hours'] ?? 0,
                'night_diff_hours' => $validated['night_diff_hours'] ?? 0,
            ]
        );

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
        // Default to current month/year if not specified
        $month = $request->get('month', date('Y-m'));
        $year = $request->get('year', date('Y'));

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

        // YTD total
        $ytdTotal = Payroll::whereYear('pay_date', $year)->sum('salary');

        // Employee count
        $employeeCount = Employee::where('status', 'active')->count();

        // Department breakdown
        $departmentBreakdown = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->join('departments_hr2', 'employees.department_id', '=', 'departments_hr2.id')
            ->select(
                'departments_hr2.name as department',
                DB::raw('COUNT(DISTINCT employees.id) as employees'),
                DB::raw('SUM(payrolls.salary) as total_salary'),
                DB::raw('SUM(payrolls.deductions) as total_deductions'),
                DB::raw('SUM(payrolls.net_pay) as total_net_pay')
            )
            ->groupBy('departments_hr2.name', 'departments_hr2.id')
            ->orderBy('total_salary', 'desc')
            ->get();

        $employees = Employee::all();
        $positions = \App\Models\admin\Hr\hr2\DepartmentPositionTitle::all();
        $departments = Department::all();

        return view('payroll.reports', compact(
            'payrolls', 'employees', 'positions', 'departments',
            'totalSalary', 'totalDeductions', 'totalNetPay',
            'month', 'year', 'employeeCount', 'ytdTotal', 'departmentBreakdown'
        ));
    }
}
