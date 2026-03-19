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
     * AJAX: Get latest compensation for employee, prorated based on attendance
     */
    public function getSalary($employeeId)
    {
        $comp = DirectCompensation::where('employee_id', $employeeId)
            ->orderByDesc('month')
            ->first();
        if ($comp) {
            // Get attendance for current month
            $attendances = AttendanceLog::where('employee_id', $employeeId)
                ->whereMonth('clock_in', now()->month)
                ->whereYear('clock_in', now()->year)
                ->get();

            $totalHours = 0;
            foreach ($attendances as $att) {
                if ($att->clock_in && $att->clock_out) {
                    $totalHours += $att->clock_in->diffInHours($att->clock_out);
                }
            }

            // Assume 160 hours per month (20 days * 8 hours)
            $expectedHours = 160;
            $proratedBase = $totalHours > 0 ? ($comp->base_salary / $expectedHours) * $totalHours : 0;

            $salary = $proratedBase + $comp->shift_allowance + $comp->overtime_pay + $comp->bonus;

            return response()->json([
                'salary' => round($salary, 2),
                'info' => 'Prorated Base: ₱' . number_format($proratedBase, 2) . ' (' . $totalHours . 'h), Allowance: ₱' . number_format($comp->shift_allowance, 2) . ', OT: ₱' . number_format($comp->overtime_pay, 2) . ', Bonus: ₱' . number_format($comp->bonus, 2)
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

        return redirect()->route('payroll.index')->with('success', 'Payroll created successfully.');
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
}
