<?php

namespace App\Http\Controllers\user\Core\core1\Nurse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Patient;
use App\Models\core1\Appointment;
use App\Models\core1\MedicalRecord;

class NurseDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'head_nurse') {
             $stats = [
                'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
                'active_patients' => Patient::where('status', 'active')->count(),
                'total_nurses' => \App\Models\core1\User::where('role', 'nurse')->count(),
                'pending_tasks' => Appointment::where('status', 'scheduled')->count(),
            ];
            
            $todayAppointments = Appointment::with(['patient.assignedNurse', 'doctor'])
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time')
                ->take(10)
                ->get();
            
            $recentPatients = Patient::latest()
                ->take(10)
                ->get();

            return view('core.core1.head-nurse.dashboard', compact('stats', 'todayAppointments', 'recentPatients'));
        }

        // Statistics for nurse dashboard
        $assignedPatientIds = Patient::where('assigned_nurse_id', $user->id)->pluck('id');

        $stats = [
            'today_appointments' => Appointment::whereIn('patient_id', $assignedPatientIds)
                ->whereDate('appointment_date', today())
                ->count(),
            'assigned_patients' => $assignedPatientIds->count(),
            'today_registrations' => Patient::whereIn('id', $assignedPatientIds)
                ->whereDate('created_at', today())
                ->count(),
            'recent_records' => MedicalRecord::whereIn('patient_id', $assignedPatientIds)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
        
        // Recent appointments for today (only assigned patients)
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereIn('patient_id', $assignedPatientIds)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();
        
        // Assigned patients
        $assignedPatients = Patient::where('assigned_nurse_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Recent patients (fallback/global)
        $recentPatients = Patient::latest()
            ->take(5)
            ->get();
        
        return view('core.core1.nurse.dashboard', compact('stats', 'todayAppointments', 'assignedPatients', 'recentPatients'));
    }

    public function overview()
    {
        $user = auth()->user();
        
        if ($user->role === 'head_nurse') {
            $stats = [
                'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
                'active_patients' => Patient::where('status', 'active')->count(),
                'total_nurses' => \App\Models\core1\User::where('role', 'nurse')->count(),
                'pending_tasks' => Appointment::where('status', 'scheduled')->count(),
            ];
            
            $todayAppointments = Appointment::with(['patient.assignedNurse', 'doctor'])
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time')
                ->take(10)
                ->get();
            
            $recentPatients = Patient::latest()
                ->take(10)
                ->get();

            return view('core.core1.head-nurse.overview', compact('stats', 'todayAppointments', 'recentPatients'));
        }

        // Statistics for nurse dashboard
        $assignedPatientIds = Patient::where('assigned_nurse_id', $user->id)->pluck('id');

        $stats = [
            'today_appointments' => Appointment::whereIn('patient_id', $assignedPatientIds)
                ->whereDate('appointment_date', today())
                ->count(),
            'assigned_patients' => $assignedPatientIds->count(),
            'today_registrations' => Patient::whereIn('id', $assignedPatientIds)
                ->whereDate('created_at', today())
                ->count(),
            'recent_records' => MedicalRecord::whereIn('patient_id', $assignedPatientIds)
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
        
        // Recent appointments for today (only assigned patients)
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereIn('patient_id', $assignedPatientIds)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();
        
        // Assigned patients
        $assignedPatients = Patient::where('assigned_nurse_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Recent patients (fallback/global)
        $recentPatients = Patient::latest()
            ->take(5)
            ->get();
        
        return view('core.core1.nurse.overview', compact('stats', 'todayAppointments', 'assignedPatients', 'recentPatients'));
    }
}
