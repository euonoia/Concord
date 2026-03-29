<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr2\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollAnalyticsController extends Controller
{
    /**
     * Payroll & Labor Cost Analytics Dashboard
     */
    public function dashboard(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized');
        }

        $summaryData = $this->getPayrollSummary();
        $costByDepartment = $this->getCostByDepartment();
        $costByPosition = $this->getCostByPosition();
        $payrollTrends = $this->getPayrollTrends();
        $deductionBreakdown = $this->getDeductionBreakdown();
        $costAnalysis = $this->getCostAnalysis();
        $salaryDistribution = $this->getSalaryDistribution();

        return view('admin.hr4.analytics.payroll_analytics', compact(
            'summaryData',
            'costByDepartment',
            'costByPosition',
            'payrollTrends',
            'deductionBreakdown',
            'costAnalysis',
            'salaryDistribution'
        ));
    }

    /**
     * Get Payroll Summary (Monthly & YTD)
     */
    private function getPayrollSummary()
    {
        $now = Carbon::now();
        $startOfMonth = $now->clone()->startOfMonth();
        $endOfMonth = $now->clone()->endOfMonth();
        $startOfYear = $now->clone()->startOfYear();

        // Current Month Data
        $monthlyPayroll = Payroll::whereBetween('pay_date', [$startOfMonth, $endOfMonth])->get();
        $monthlyTotal = $monthlyPayroll->sum('salary');
        $monthlyDeductions = $monthlyPayroll->sum('deductions');
        $monthlyNetPay = $monthlyPayroll->sum('net_pay');

        // YTD Data
        $ytdPayroll = Payroll::whereBetween('pay_date', [$startOfYear, $now])->get();
        $ytdTotal = $ytdPayroll->sum('salary');
        $ytdDeductions = $ytdPayroll->sum('deductions');
        $ytdNetPay = $ytdPayroll->sum('net_pay');

        // Cost Per Employee
        $activeEmployees = Employee::where('status', 'active')->count();
        $costPerEmployee = $activeEmployees > 0 ? $monthlyTotal / $activeEmployees : 0;

        // DirectCompensation data for more accurate calculations
        $compensation = DirectCompensation::whereMonth('month', $now->month)
            ->whereYear('month', $now->year)
            ->get();

        $compensationTotal = $compensation->sum(function ($comp) {
            return $comp->base_salary + $comp->shift_allowance + $comp->overtime_pay + 
                   $comp->bonus + $comp->training_reward;
        });

        // Budget comparison (estimated budget as 2.4M for demo)
        $budget = 2400000;
        $budgetPercentage = $budget > 0 ? round(($compensationTotal / $budget) * 100, 2) : 0;

        return [
            'monthlyTotal' => $monthlyTotal,
            'monthlyDeductions' => $monthlyDeductions,
            'monthlyNetPay' => $monthlyNetPay,
            'ytdTotal' => $ytdTotal,
            'ytdDeductions' => $ytdDeductions,
            'ytdNetPay' => $ytdNetPay,
            'costPerEmployee' => $costPerEmployee,
            'activeEmployees' => $activeEmployees,
            'compensationTotal' => $compensationTotal,
            'budget' => $budget,
            'budgetPercentage' => $budgetPercentage,
            'budgetStatus' => $budgetPercentage > 110 ? 'over' : ($budgetPercentage > 95 ? 'warning' : 'on-track'),
        ];
    }

    /**
     * Get Cost by Department
     */
    private function getCostByDepartment()
    {
        $now = Carbon::now();

        return DB::table('employees')
            ->select(
                'departments_hr2.name as department',
                DB::raw('COUNT(employees.id) as headcount'),
                DB::raw('SUM(department_position_titles_hr2.base_salary) as total_base_salary'),
                DB::raw('AVG(department_position_titles_hr2.base_salary) as avg_salary')
            )
            ->join('departments_hr2', 'employees.department_id', '=', 'departments_hr2.id')
            ->leftJoin('department_position_titles_hr2', 'employees.position_id', '=', 'department_position_titles_hr2.id')
            ->where('employees.status', 'active')
            ->groupBy('departments_hr2.name', 'departments_hr2.id')
            ->orderByDesc('total_base_salary')
            ->get()
            ->map(function ($item) {
                // Add percentage of total
                return (array) $item;
            });
    }

    /**
     * Get Cost by Position
     */
    private function getCostByPosition()
    {
        return DB::table('employees')
            ->select(
                'department_position_titles_hr2.position_title as position',
                DB::raw('COUNT(employees.id) as count'),
                'department_position_titles_hr2.base_salary',
                DB::raw('COUNT(employees.id) * department_position_titles_hr2.base_salary as total_cost')
            )
            ->join('department_position_titles_hr2', 'employees.position_id', '=', 'department_position_titles_hr2.id')
            ->where('employees.status', 'active')
            ->groupBy('department_position_titles_hr2.position_title', 'department_position_titles_hr2.base_salary', 'department_position_titles_hr2.id')
            ->orderByDesc('total_cost')
            ->get()
            ->map(function ($item) {
                return [
                    'position' => $item->position,
                    'count' => $item->count,
                    'baseSalary' => $item->base_salary,
                    'totalCost' => $item->total_cost,
                    'avgSalary' => $item->base_salary,
                ];
            });
    }

    /**
     * Get Payroll Trends (Last 12 months)
     */
    private function getPayrollTrends()
    {
        $trends = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->clone()->subMonths($i);
            $month = $date->format('M Y');

            $total = Payroll::whereMonth('pay_date', $date->month)
                ->whereYear('pay_date', $date->year)
                ->sum('salary');

            $trends[$month] = $total;
        }

        return $trends;
    }

    /**
     * Get Deduction Breakdown
     */
    private function getDeductionBreakdown()
    {
        $now = Carbon::now();

        // Get deduction details from compensation records
        $deductions = [
            'SSS' => 0,
            'PhilHealth' => 0,
            'PAG-IBIG' => 0,
            'Income Tax' => 0,
            'Other' => 0,
        ];

        // Estimate based on total deductions
        $totalDeductions = Payroll::whereMonth('pay_date', $now->month)
            ->whereYear('pay_date', $now->year)
            ->sum('deductions');

        // Standard deductions as percentage
        $deductions['SSS'] = $totalDeductions * 0.35;
        $deductions['PhilHealth'] = $totalDeductions * 0.25;
        $deductions['PAG-IBIG'] = $totalDeductions * 0.15;
        $deductions['Income Tax'] = $totalDeductions * 0.20;
        $deductions['Other'] = $totalDeductions * 0.05;

        return collect($deductions)->map(function ($amount, $type) {
            return [
                'type' => $type,
                'amount' => $amount,
            ];
        })->values();
    }

    /**
     * Get Cost Analysis (Breakdown: Salaries, Allowances, OT, etc)
     */
    private function getCostAnalysis()
    {
        $compensation = DirectCompensation::whereMonth('month', Carbon::now()->month)
            ->whereYear('month', Carbon::now()->year)
            ->get();

        $analysis = [
            'Salaries' => $compensation->sum('base_salary'),
            'Allowances' => $compensation->sum('shift_allowance'),
            'Overtime' => $compensation->sum('overtime_pay'),
            'Bonus' => $compensation->sum('bonus'),
            'Training Reward' => $compensation->sum('training_reward'),
        ];

        $total = array_sum($analysis);

        return collect($analysis)->map(function ($amount, $category) use ($total) {
            return [
                'category' => $category,
                'amount' => $amount,
                'percentage' => $total > 0 ? round(($amount / $total) * 100, 1) : 0,
            ];
        })->values();
    }

    /**
     * Get Salary Distribution
     */
    private function getSalaryDistribution()
    {
        // Get count first
        $count = DB::table('employees')
            ->join('department_position_titles_hr2', 'employees.position_id', '=', 'department_position_titles_hr2.id')
            ->where('employees.status', 'active')
            ->count();

        if ($count == 0) {
            return (object) [
                'count' => 0,
                'min_salary' => 0,
                'max_salary' => 0,
                'avg_salary' => 0,
                'median_salary' => 0
            ];
        }

        // Get min, max, avg
        $stats = DB::table('employees')
            ->select(
                DB::raw('MIN(department_position_titles_hr2.base_salary) as min_salary'),
                DB::raw('MAX(department_position_titles_hr2.base_salary) as max_salary'),
                DB::raw('AVG(department_position_titles_hr2.base_salary) as avg_salary')
            )
            ->join('department_position_titles_hr2', 'employees.position_id', '=', 'department_position_titles_hr2.id')
            ->where('employees.status', 'active')
            ->first();

        // Calculate median using PHP (simpler and more reliable)
        $salaries = DB::table('employees')
            ->join('department_position_titles_hr2', 'employees.position_id', '=', 'department_position_titles_hr2.id')
            ->where('employees.status', 'active')
            ->pluck('department_position_titles_hr2.base_salary')
            ->sort()
            ->values();

        $median = 0;
        if ($salaries->count() > 0) {
            $middle = floor($salaries->count() / 2);
            if ($salaries->count() % 2 == 0) {
                // Even number of elements, average the two middle ones
                $median = ($salaries[$middle - 1] + $salaries[$middle]) / 2;
            } else {
                // Odd number, take the middle one
                $median = $salaries[$middle];
            }
        }

        return (object) [
            'count' => $count,
            'min_salary' => $stats->min_salary ?? 0,
            'max_salary' => $stats->max_salary ?? 0,
            'avg_salary' => $stats->avg_salary ?? 0,
            'median_salary' => $median
        ];
    }

    /**
     * API Endpoint: Get Payroll Summary as JSON
     */
    public function getSummaryJson()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized');
        }

        $summary = $this->getPayrollSummary();
        $costByDept = $this->getCostByDepartment();
        $costByPos = $this->getCostByPosition();

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'costByDepartment' => $costByDept,
            'costByPosition' => $costByPos,
        ]);
    }

    /**
     * Export Payroll Report
     */
    public function exportReport(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized');
        }

        $summary = $this->getPayrollSummary();
        $costByDept = $this->getCostByDepartment();

        // Create CSV
        $filename = 'payroll-analytics-' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($summary, $costByDept) {
            $file = fopen('php://output', 'w');

            // Summary Section
            fputcsv($file, ['PAYROLL SUMMARY']);
            fputcsv($file, ['Metric', 'Amount']);
            fputcsv($file, ['Monthly Total', number_format($summary['monthlyTotal'] ?? 0, 2)]);
            fputcsv($file, ['Monthly Deductions', number_format($summary['monthlyDeductions'] ?? 0, 2)]);
            fputcsv($file, ['Monthly Net Pay', number_format($summary['monthlyNetPay'] ?? 0, 2)]);
            fputcsv($file, ['YTD Total', number_format($summary['ytdTotal'] ?? 0, 2)]);
            fputcsv($file, ['Cost Per Employee', number_format($summary['costPerEmployee'] ?? 0, 2)]);
            fputcsv($file, ['Active Employees', $summary['activeEmployees'] ?? 0]);
            fputcsv($file, []);

            // Department Cost Section
            fputcsv($file, ['COST BY DEPARTMENT']);
            fputcsv($file, ['Department', 'Headcount', 'Total Base Salary', 'Avg Salary']);
            if (count($costByDept) > 0) {
                foreach ($costByDept as $dept) {
                    fputcsv($file, [
                        $dept['department'] ?? 'Unknown',
                        $dept['headcount'] ?? 0,
                        number_format($dept['total_base_salary'] ?? 0, 2),
                        number_format($dept['avg_salary'] ?? 0, 2),
                    ]);
                }
            } else {
                fputcsv($file, ['No department data available', '', '', '']);
            }

            fputcsv($file, []);
            fputcsv($file, ['Generated on', Carbon::now()->format('Y-m-d H:i:s')]);

            fclose($file);
        };
    }

    /**
     * Get Revenue Comparison (Payroll as % of Revenue)
     */
    public function getRevenueComparison()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized');
        }

        $now = Carbon::now();
        $monthlyPayroll = Payroll::whereMonth('pay_date', $now->month)
            ->whereYear('pay_date', $now->year)
            ->sum('salary');

        // Estimated revenue (you should integrate with actual revenue data)
        $estimatedRevenue = 7000000; // Example: 7M

        $payrollPercentage = $estimatedRevenue > 0 ? round(($monthlyPayroll / $estimatedRevenue) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'monthlyPayroll' => $monthlyPayroll,
                'estimatedRevenue' => $estimatedRevenue,
                'payrollPercentage' => $payrollPercentage,
                'healthStatus' => $payrollPercentage < 40 ? 'healthy' : 'warning',
            ],
        ]);
    }
}
