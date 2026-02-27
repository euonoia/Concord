<?php

namespace App\Http\Controllers\user\Core\core1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Patient;
use App\Models\core1\Appointment;
use App\Models\core1\Bill;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'bed_occupancy' => [
                'occupied' => 42, // TODO: Replace with actual bed occupancy data when beds table is created
                'total' => 58,
                'percentage' => 72,
            ],
            'monthly_revenue' => Bill::whereMonth('bill_date', now()->month)
                ->whereYear('bill_date', now()->year)
                ->where('status', 'paid')
                ->sum('total') ?? 0,
        ];

        $admissionData = $this->getAdmissionData();
        $revenueData = $this->getRevenueData();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get alerts
        $alerts = $this->getAlerts();

        return view('core.core1.admin.dashboard', compact('stats', 'admissionData', 'revenueData', 'recentActivities', 'alerts'));
    }

    public function overview()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'bed_occupancy' => [
                'occupied' => 42, // TODO: Replace with actual bed occupancy data when beds table is created
                'total' => 58,
                'percentage' => 72,
            ],
            'monthly_revenue' => Bill::whereMonth('bill_date', now()->month)
                ->whereYear('bill_date', now()->year)
                ->where('status', 'paid')
                ->sum('total') ?? 0,
        ];

        $admissionData = $this->getAdmissionData();
        $revenueData = $this->getRevenueData();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get alerts
        $alerts = $this->getAlerts();

        return view('core.core1.admin.overview', compact('stats', 'admissionData', 'revenueData', 'recentActivities', 'alerts'));
    }

    private function getAdmissionData()
    {
        // Mock data - in production, get from database
        return [
            ['month' => 'Jan', 'admissions' => 45],
            ['month' => 'Feb', 'admissions' => 52],
            ['month' => 'Mar', 'admissions' => 48],
            ['month' => 'Apr', 'admissions' => 61],
            ['month' => 'May', 'admissions' => 55],
            ['month' => 'Jun', 'admissions' => 67],
        ];
    }

    private function getRevenueData()
    {
        // Mock data - in production, get from database
        return [
            ['month' => 'Jan', 'revenue' => 125000],
            ['month' => 'Feb', 'revenue' => 142000],
            ['month' => 'Mar', 'revenue' => 138000],
            ['month' => 'Apr', 'revenue' => 156000],
            ['month' => 'May', 'revenue' => 148000],
            ['month' => 'Jun', 'revenue' => 172000],
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent patient registrations
        $recentPatients = Patient::latest()->take(3)->get();
        foreach ($recentPatients as $patient) {
            $activities[] = [
                'action' => 'New patient registered',
                'patient' => $patient->name,
                'time' => $patient->created_at->diffForHumans(),
                'timestamp' => $patient->created_at->timestamp,
            ];
        }
        
        // Recent appointments
        $recentAppointments = Appointment::with('patient')
            ->latest()
            ->take(2)
            ->get();
        foreach ($recentAppointments as $appointment) {
            $activities[] = [
                'action' => 'Appointment scheduled',
                'patient' => $appointment->patient->name,
                'time' => $appointment->created_at->diffForHumans(),
                'timestamp' => $appointment->created_at->timestamp,
            ];
        }
        
        // Sort by timestamp (most recent first) and take most recent 4
        usort($activities, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Remove timestamp before returning
        return array_map(function($activity) {
            unset($activity['timestamp']);
            return $activity;
        }, array_slice($activities, 0, 4));
    }

    private function getAlerts()
    {
        $alerts = [];
        
        // Pending bills
        $pendingBills = Bill::where('status', 'pending')->count();
        if ($pendingBills > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingBills} pending bill" . ($pendingBills > 1 ? 's' : '') . " requiring attention",
                'priority' => $pendingBills > 10 ? 'high' : 'medium',
            ];
        }
        
        // Overdue bills
        $overdueBills = Bill::where('status', 'overdue')->count();
        if ($overdueBills > 0) {
            $alerts[] = [
                'type' => 'critical',
                'message' => "{$overdueBills} overdue bill" . ($overdueBills > 1 ? 's' : ''),
                'priority' => 'high',
            ];
        }
        
        // Today's appointments count
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        if ($todayAppointments > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$todayAppointments} appointment" . ($todayAppointments > 1 ? 's' : '') . " scheduled for today",
                'priority' => 'low',
            ];
        }
        
        // If no alerts, add a default info message
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'All systems operational',
                'priority' => 'low',
            ];
        }
        
        return $alerts;
    }
}

