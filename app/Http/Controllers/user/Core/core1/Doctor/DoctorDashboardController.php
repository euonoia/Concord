<?php

namespace App\Http\Controllers\user\Core\core1\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Appointment;
use App\Models\core1\Patient;
use App\Models\core1\MedicalRecord;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
          $activeStatuses = ['scheduled','accepted','waiting','in_consultation','consulted'];

        // Only include patients who are NOT completed
        $activePatientQuery = Patient::where('status', '!=', 'completed');

        // Statistics
        $stats = [
            'today_appointments' => Appointment::where('doctor_id', $user->id)
                ->whereDate('appointment_date', today())
                ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
                ->count(),

            'upcoming_appointments' => Appointment::where('doctor_id', $user->id)
                ->where('appointment_date', '>=', today())
                ->where('status', 'scheduled')
                ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
                ->count(),


'total_patients' => Patient::whereHas('appointments', function($q) use ($user, $activeStatuses) {
    $q->where('doctor_id', $user->id)
      ->whereIn('status', $activeStatuses);
})->distinct()->count(), 

            'recent_records' => MedicalRecord::where('doctor_id', $user->id)
                ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        // Today's appointments
        $todayAppointments = Appointment::with(['patient'])
            ->where('doctor_id', $user->id)
            ->whereDate('appointment_date', today())
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->orderBy('appointment_time')
            ->get();

        // Upcoming appointments (next 5)
        $upcomingAppointments = Appointment::with(['patient'])
            ->where('doctor_id', $user->id)
            ->where('appointment_date', '>=', today())
            ->where('status', 'scheduled')
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Recent medical records
        $recentRecords = MedicalRecord::with(['patient'])
            ->where('doctor_id', $user->id)
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->latest()
            ->take(5)
            ->get();

        // Inpatients under care
        $inpatients = Patient::where('care_type', 'inpatient')
            ->where('status', '!=', 'completed')
            ->where(function($q) use ($user){
                $q->where('doctor_id', $user->id)
                  ->orWhereHas('appointments', fn($a) => $a->where('doctor_id', $user->id));
            })
            ->get();

        return view('core.core1.doctor.dashboard', compact(
            'stats',
            'todayAppointments',
            'upcomingAppointments',
            'recentRecords',
            'inpatients'
        ));
    }


    public function overview()
{
    $user = auth()->user();
           $activeStatuses = ['scheduled','accepted','waiting','in_consultation','consulted'];


    // Only include patients who are NOT completed
    $activePatientQuery = Patient::where('status', '!=', 'completed');

    // Statistics
    $stats = [
        'today_appointments' => Appointment::where('doctor_id', $user->id)
            ->whereDate('appointment_date', today())
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->count(),

        'upcoming_appointments' => Appointment::where('doctor_id', $user->id)
            ->where('appointment_date', '>=', today())
            ->where('status', 'scheduled')
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->count(),


'total_patients' => Patient::whereHas('appointments', function($q) use ($user, $activeStatuses) {
    $q->where('doctor_id', $user->id)
      ->whereIn('status', $activeStatuses);
})->distinct()->count(),

        'recent_records' => MedicalRecord::where('doctor_id', $user->id)
            ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count(),
    ];

    $todayAppointments = Appointment::with(['patient'])
        ->where('doctor_id', $user->id)
        ->whereDate('appointment_date', today())
        ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
        ->orderBy('appointment_time')
        ->get();

    $upcomingAppointments = Appointment::with(['patient'])
        ->where('doctor_id', $user->id)
        ->where('appointment_date', '>=', today())
        ->where('status', 'scheduled')
        ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->take(5)
        ->get();

    $recentRecords = MedicalRecord::with(['patient'])
        ->where('doctor_id', $user->id)
        ->whereHas('patient', fn($q) => $q->where('status', '!=', 'completed'))
        ->latest()
        ->take(5)
        ->get();

    return view('core.core1.doctor.overview', compact(
        'stats',
        'todayAppointments',
        'upcomingAppointments',
        'recentRecords'
    ));
}}
