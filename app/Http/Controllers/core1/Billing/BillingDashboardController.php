<?php

namespace App\Http\Controllers\core1\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Bill;
use App\Models\core1\Patient;
use Illuminate\Support\Facades\DB;

class BillingDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'today_revenue' => Bill::whereDate('bill_date', today())
                ->where('status', 'paid')
                ->sum('total') ?? 0,
            'monthly_revenue' => Bill::whereMonth('bill_date', now()->month)
                ->whereYear('bill_date', now()->year)
                ->where('status', 'paid')
                ->sum('total') ?? 0,
            'pending_bills' => Bill::where('status', 'pending')->count(),
            'overdue_bills' => Bill::where('status', 'overdue')->count(),
            'total_bills' => Bill::count(),
            'paid_bills' => Bill::where('status', 'paid')->count(),
        ];
        
        // Recent bills
        $recentBills = Bill::with(['patient'])
            ->latest()
            ->take(10)
            ->get();
        
        // Pending bills
        $pendingBills = Bill::with(['patient'])
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();
        
        // Overdue bills
        $overdueBills = Bill::with(['patient'])
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'pending')
                    ->where('due_date', '<', today());
            })
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();
        
        // Revenue by status
        $revenueByStatus = Bill::select('status', DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return view('core.core1.billing.dashboard', compact('stats', 'recentBills', 'pendingBills', 'overdueBills', 'revenueByStatus'));
    }

    public function overview()
    {
        $stats = [
            'today_revenue' => Bill::whereDate('bill_date', today())
                ->where('status', 'paid')
                ->sum('total') ?? 0,
            'monthly_revenue' => Bill::whereMonth('bill_date', now()->month)
                ->whereYear('bill_date', now()->year)
                ->where('status', 'paid')
                ->sum('total') ?? 0,
            'pending_bills' => Bill::where('status', 'pending')->count(),
            'overdue_bills' => Bill::where('status', 'overdue')->count(),
            'total_bills' => Bill::count(),
            'paid_bills' => Bill::where('status', 'paid')->count(),
        ];
        
        $recentBills = Bill::with(['patient'])
            ->latest()
            ->take(10)
            ->get();
        
        $pendingBills = Bill::with(['patient'])
            ->where('status', 'pending')
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();
        
        $overdueBills = Bill::with(['patient'])
            ->where('status', 'overdue')
            ->orWhere(function($query) {
                $query->where('status', 'pending')
                    ->where('due_date', '<', today());
            })
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();
        
        $revenueByStatus = Bill::select('status', DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return view('core.core1.billing.overview', compact('stats', 'recentBills', 'pendingBills', 'overdueBills', 'revenueByStatus'));
    }
}

