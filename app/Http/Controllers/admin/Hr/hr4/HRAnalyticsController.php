<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\Payroll;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRAnalyticsController extends Controller
{
    /**
     * HR KPI Dashboard
     */
    public function dashboard(Request $request)
    {
        $dashboardData = $this->getKPIData();
        $trends = $this->getHeadcountTrends();
        $departmentBreakdown = $this->getDepartmentBreakdown();
        $newHires = $this->getNewHiresData();
        $attritionData = $this->getAttritionData();

        return view('admin.hr4.analytics.kpi_dashboard', compact(
            'dashboardData',
            'trends',
            'departmentBreakdown',
            'newHires',
            'attritionData'
        ));
    }

    /**
     * Get Core KPI Data
     */
    private function getKPIData()
    {
        $now = Carbon::now();
        $startOfMonth = $now->clone()->startOfMonth();
        $endOfMonth = $now->clone()->endOfMonth();
        $startOfYear = $now->clone()->startOfYear();

        // Total Headcount
        $totalHeadcount = Employee::where('status', 'active')->count();
        $inactiveCount = Employee::where('status', '!=', 'active')->count();

        // New Hires (MTD)
        $newHiresMTD = Employee::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $newHiresYTD = Employee::whereBetween('created_at', [$startOfYear, $now])
            ->count();

        // Vacant Positions (needed but not filled)
        $vacantPositions = 0;
        $positions = DepartmentPositionTitle::all();
        foreach ($positions as $pos) {
            $current_count = Employee::where('position_id', $pos->id)
                                    ->where('status', 'active')
                                    ->count();
            $needed = max(0, ($pos->required_count ?? 0) - $current_count);
            $vacantPositions += $needed;
        }

        $totalPositions = DepartmentPositionTitle::count();
        $vacancyRate = $totalPositions > 0 ? round(($vacantPositions / $totalPositions) * 100, 2) : 0;

        // Turnover Rate (employees left this month)
        $employeesLeft = Employee::whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'terminated')
            ->count();

        $turnoverRate = $totalHeadcount > 0 ? round(($employeesLeft / $totalHeadcount) * 100, 2) : 0;

        // Attrition Count
        $totalAttrition = Employee::where('status', 'terminated')
            ->count();

        // Average Tenure
        $avgTenure = Employee::where('status', 'active')
            ->selectRaw('AVG(YEAR(CURDATE()) - YEAR(created_at)) as avg_years')
            ->value('avg_years') ?? 0;

        // Promotion Rate (users with promoted status this month)
        $promotionsMTD = DB::table('employees')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->where('status', 'promoted')
            ->count();

        return [
            'totalHeadcount' => $totalHeadcount,
            'inactiveCount' => $inactiveCount,
            'activeCount' => $totalHeadcount,
            'newHiresMTD' => $newHiresMTD,
            'newHiresYTD' => $newHiresYTD,
            'vacantPositions' => $vacantPositions,
            'vacancyRate' => $vacancyRate,
            'turnoverRate' => $turnoverRate,
            'employeesLeftMTD' => $employeesLeft,
            'totalAttrition' => $totalAttrition,
            'avgTenure' => number_format($avgTenure, 1),
            'promotionsMTD' => $promotionsMTD,
        ];
    }

    /**
     * Get Headcount Trends (Last 12 months)
     */
    private function getHeadcountTrends()
    {
        $trends = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->clone()->subMonths($i);
            $month = $date->format('M Y');

            $count = Employee::where('status', 'active')
                ->whereDate('created_at', '<=', $date->endOfMonth())
                ->count();

            $trends[$month] = $count;
        }

        return $trends;
    }

    /**
     * Get Department Breakdown
     */
    private function getDepartmentBreakdown()
    {
        return DB::table('employees')
            ->select('departments_hr2.name as department', DB::raw('COUNT(employees.id) as count'))
            ->join('departments_hr2', 'employees.department_id', '=', 'departments_hr2.id')
            ->where('employees.status', 'active')
            ->groupBy('departments_hr2.name')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                $item->percentage = round(($item->count / Employee::where('status', 'active')->count()) * 100, 1);
                return $item;
            });
    }

    /**
     * Get New Hires Data (Last 30 days)
     */
    private function getNewHiresData()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        return Employee::where('created_at', '>=', $thirtyDaysAgo)
            ->with('department', 'position')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($emp) {
                return [
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'id' => $emp->employee_id,
                    'department' => $emp->department->name ?? 'N/A',
                    'position' => $emp->position->position_title ?? 'N/A',
                    'joinDate' => $emp->created_at->format('M d, Y'),
                    'daysEmployed' => $emp->created_at->diffInDays(now()),
                ];
            });
    }

    /**
     * Get Attrition Data
     */
    private function getAttritionData()
    {
        $attritionReasons = [
            'Resigned' => Employee::where('status', 'resigned')->count(),
            'Terminated' => Employee::where('status', 'terminated')->count(),
            'Retired' => Employee::where('status', 'retired')->count(),
            'Other' => Employee::where('status', 'inactive')->count(),
        ];

        $total = array_sum($attritionReasons);

        return collect($attritionReasons)->map(function ($count, $reason) use ($total) {
            return [
                'reason' => $reason,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->values();
    }

    /**
     * API Endpoint: Get KPI Data as JSON
     */
    public function getKPIDataJson()
    {
        $data = $this->getKPIData();
        $trends = $this->getHeadcountTrends();
        $departments = $this->getDepartmentBreakdown();

        return response()->json([
            'success' => true,
            'data' => $data,
            'trends' => $trends,
            'departments' => $departments,
        ]);
    }

    /**
     * Get Department Health Scores
     */
    public function getDepartmentHealthScores()
    {
        $departments = Department::where('status', 'active')->get();

        return $departments->map(function ($dept) {
            $totalEmps = Employee::where('department_id', $dept->id)->where('status', 'active')->count();

            if ($totalEmps === 0) {
                return [
                    'department' => $dept->name,
                    'headcount' => 0,
                    'healthScore' => 0,
                    'status' => 'inactive',
                ];
            }

            // Calculate health score based on:
            // - Filled positions (80%)
            // - Low turnover (20%)
            $filledPositions = $totalEmps;
            $neededPositions = 0;
            $deptPositions = DepartmentPositionTitle::where('department_id', $dept->id)->get();
            foreach ($deptPositions as $pos) {
                $current_count = Employee::where('position_id', $pos->id)
                                        ->where('status', 'active')
                                        ->count();
                $needed = max(0, ($pos->required_count ?? 0) - $current_count);
                $neededPositions += $needed;
            }

            $turnover = Employee::where('department_id', $dept->id)
                ->where('status', 'terminated')
                ->count();

            $turnoverRate = $totalEmps > 0 ? ($turnover / $totalEmps) * 100 : 0;
            $turnoverScore = max(0, 100 - $turnoverRate);

            // Calculate fill rate safely (avoids divide by zero)
            $fillRate = ($filledPositions + $neededPositions) > 0 ? ($filledPositions / ($filledPositions + $neededPositions)) * 100 : 0;
            $healthScore = ($fillRate * 0.8) + ($turnoverScore * 0.2);

            return [
                'department' => $dept->name,
                'headcount' => $totalEmps,
                'fillRate' => round($fillRate, 1),
                'turnoverRate' => round($turnoverRate, 1),
                'healthScore' => round($healthScore, 1),
                'status' => $healthScore >= 80 ? 'healthy' : ($healthScore >= 60 ? 'fair' : 'poor'),
            ];
        })->sortByDesc('healthScore');
    }
}
