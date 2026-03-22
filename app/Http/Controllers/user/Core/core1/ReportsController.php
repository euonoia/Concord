<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        // 1. Overview Stats
        $stats = [
            'total_patients' => \App\Models\user\Core\core1\Patient::count(),
            'monthly_appointments' => \App\Models\user\Core\core1\Appointment::whereMonth('appointment_date', now()->month)
                                        ->whereYear('appointment_date', now()->year)
                                        ->count(),
            'pending_appointments' => \App\Models\user\Core\core1\Appointment::where('status', 'pending')->count(),
            'monthly_revenue' => \App\Models\user\Core\core1\Bill::where('status', 'paid')
                                    ->whereMonth('paid_at', now()->month)
                                    ->whereYear('paid_at', now()->year)
                                    ->sum('total'),
        ];

        // 2. Appointment Trends (Last 7 Days)
        $trends = \App\Models\user\Core\core1\Appointment::selectRaw('DATE(appointment_date) as date, count(*) as count')
            ->where('appointment_date', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Patient Demographics (Gender)
        $demographics = \App\Models\user\Core\core1\Patient::selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->get();

        // 4. Appointment Status Distribution
        $statusDistribution = \App\Models\user\Core\core1\Appointment::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        // 5. Revenue Trends (Last 6 Months)
        $revenueTrends = \App\Models\user\Core\core1\Bill::selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, sum(total) as total')
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('core.core1.reports.index', compact('stats', 'trends', 'demographics', 'statusDistribution', 'revenueTrends'));
    }
}

