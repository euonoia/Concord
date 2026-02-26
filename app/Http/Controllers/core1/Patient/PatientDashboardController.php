<?php

namespace App\Http\Controllers\core1\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Patient;
use App\Models\core1\Appointment;
use App\Models\core1\MedicalRecord;
use App\Models\core1\Bill;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $patient = Patient::where('email', $user->email)->first();
        
        if (!$patient) {
            $stats = [
                'upcoming_appointments' => 0,
                'total_appointments' => 0,
                'medical_records' => 0,
                'pending_bills' => 0,
            ];
            return view('core.core1.patient.dashboard', compact('stats'));
        }
        
        // Statistics
        $stats = [
            'upcoming_appointments' => Appointment::where('patient_id', $patient->id)
                ->where('appointment_date', '>=', today())
                ->where('status', 'scheduled')
                ->count(),
            'total_appointments' => Appointment::where('patient_id', $patient->id)->count(),
            'medical_records' => MedicalRecord::where('patient_id', $patient->id)->count(),
            'pending_bills' => Bill::where('patient_id', $patient->id)
                ->where('status', 'pending')
                ->count(),
        ];
        
        // Upcoming appointments
        $upcomingAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();
        
        // Recent appointments
        $recentAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get();
        
        // Recent medical records
        $recentRecords = MedicalRecord::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Pending bills
        $pendingBills = Bill::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('core.core1.patient.dashboard', compact('stats', 'patient', 'upcomingAppointments', 'recentAppointments', 'recentRecords', 'pendingBills'));
    }

    public function overview()
    {
        $user = auth()->user();
        $patient = Patient::where('email', $user->email)->first();
        
        if (!$patient) {
            $stats = [
                'upcoming_appointments' => 0,
                'total_appointments' => 0,
                'medical_records' => 0,
                'pending_bills' => 0,
            ];
            return view('core.core1.patient.overview', compact('stats'));
        }
        
        $stats = [
            'upcoming_appointments' => Appointment::where('patient_id', $patient->id)
                ->where('appointment_date', '>=', today())
                ->where('status', 'scheduled')
                ->count(),
            'total_appointments' => Appointment::where('patient_id', $patient->id)->count(),
            'medical_records' => MedicalRecord::where('patient_id', $patient->id)->count(),
            'pending_bills' => Bill::where('patient_id', $patient->id)
                ->where('status', 'pending')
                ->count(),
        ];
        
        $upcomingAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();
        
        $recentAppointments = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get();
        
        $recentRecords = MedicalRecord::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();
        
        $pendingBills = Bill::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('core.core1.patient.overview', compact('stats', 'patient', 'upcomingAppointments', 'recentAppointments', 'recentRecords', 'pendingBills'));
    }
}

