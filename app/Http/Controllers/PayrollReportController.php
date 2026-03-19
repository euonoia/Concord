<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\admin\Hr\hr4\DirectCompensation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollReportController extends Controller
{
    /**
     * Show payroll reports dashboard
     */
    public function index(Request $request)
    {
        $month = $request->get('month', date('Y-m'));
        $year = $request->get('year', date('Y'));

        // Monthly payroll summary
        $monthlyPayrolls = Payroll::with('employee')
            ->whereYear('pay_date', $year)
            ->whereMonth('pay_date', date('m', strtotime($month)))
            ->get();

        $totalSalary = $monthlyPayrolls->sum('salary');
        $totalDeductions = $monthlyPayrolls->sum('deductions');
        $totalNetPay = $monthlyPayrolls->sum('net_pay');
        $employeeCount = $monthlyPayrolls->count();

        // Department-wise breakdown
        $departmentBreakdown = DB::table('payrolls')
            ->leftJoin('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->leftJoin('departments_hr2', 'employees.department_id', '=', 'departments_hr2.department_id')
            ->whereYear('payrolls.pay_date', $year)
            ->whereMonth('payrolls.pay_date', date('m', strtotime($month)))
            ->selectRaw('
                COALESCE(departments_hr2.name, "No Department") as department,
                COUNT(*) as employees,
                SUM(payrolls.salary) as total_salary,
                SUM(payrolls.deductions) as total_deductions,
                SUM(payrolls.net_pay) as total_net_pay
            ')
            ->groupBy('departments_hr2.name')
            ->get();

        // Year-to-date summary
        $ytdPayrolls = Payroll::whereYear('pay_date', $year)->get();
        $ytdTotal = $ytdPayrolls->sum('net_pay');

        return view('payroll.reports', compact(
            'monthlyPayrolls',
            'totalSalary',
            'totalDeductions',
            'totalNetPay',
            'employeeCount',
            'departmentBreakdown',
            'ytdTotal',
            'month',
            'year'
        ));
    }

    /**
     * Generate detailed payroll report for a specific period
     */
    public function detailed(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));
        $department = $request->get('department');

        $query = Payroll::with(['employee.department', 'employee.position'])
            ->whereBetween('pay_date', [$startDate, $endDate]);

        if ($department) {
            $query->whereHas('employee', function($q) use ($department) {
                $q->where('department_id', $department);
            });
        }

        $payrolls = $query->orderBy('pay_date', 'desc')->get();

        // Calculate totals
        $totals = [
            'salary' => $payrolls->sum('salary'),
            'deductions' => $payrolls->sum('deductions'),
            'net_pay' => $payrolls->sum('net_pay'),
            'count' => $payrolls->count()
        ];

        $departments = \App\Models\admin\Hr\hr2\Department::all();

        return view('payroll.detailed_report', compact('payrolls', 'totals', 'startDate', 'endDate', 'department', 'departments'));
    }

    /**
     * Export payroll data to CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $payrolls = Payroll::with(['employee.department', 'employee.position'])
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->orderBy('pay_date')
            ->get();

        $filename = "payroll_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Employee ID',
            'Employee Name',
            'Department',
            'Position',
            'Pay Date',
            'Salary',
            'Deductions',
            'Net Pay'
        ];

        $callback = function() use ($payrolls, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->employee->employee_id ?? 'N/A',
                    $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
                    $payroll->employee->department->name ?? 'N/A',
                    $payroll->employee->position->position_title ?? 'N/A',
                    $payroll->pay_date->format('Y-m-d'),
                    $payroll->salary,
                    $payroll->deductions,
                    $payroll->net_pay
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Show employee payroll history
     */
    public function employeeHistory($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $payrolls = Payroll::where('employee_id', $employeeId)
            ->orderBy('pay_date', 'desc')
            ->paginate(12);

        // Calculate statistics
        $stats = [
            'total_payrolls' => $payrolls->total(),
            'average_salary' => $payrolls->avg('salary'),
            'average_deductions' => $payrolls->avg('deductions'),
            'average_net_pay' => $payrolls->avg('net_pay'),
            'total_earned' => $payrolls->sum('net_pay')
        ];

        return view('payroll.employee_history', compact('employee', 'payrolls', 'stats'));
    }
}
