<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\MedicalRecord;
use App\Models\user\Core\core1\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();

     if ($user->role_slug === 'doctor') {
    $records = Patient::where('doctor_id', $user->id)
        ->orWhereHas('appointments', function ($q) use ($user) {
            $q->where('doctor_id', $user->id);
        })
        ->with([
            'medicalRecords' => function ($q) {
                $q->orderByDesc('record_date');
            },
            'appointments' => function ($q) use ($user) {
                // Only load appointments belonging to this doctor
                $q->where('doctor_id', $user->id)
                  ->orderByDesc('appointment_date')
                  ->orderByDesc('appointment_time');
            },
        ])
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate(10);
} else {
    // Admin, Head Nurse, Nurse → show all patients
    $records = Patient::with([
        'medicalRecords' => function ($q) {
            $q->orderByDesc('record_date');
        },
        'appointments' => function ($q) {
            $q->orderByDesc('appointment_date')->orderByDesc('appointment_time');
        }
    ])->orderBy('last_name')->orderBy('first_name')->paginate(10);
}

        return view('core.core1.medical-records.index', compact('records'));
    }

  public function show(Patient $patient)
{
    $user = auth()->user();

    // Only allow doctor to see assigned patients or patients with appointments
    if ($user->role_slug === 'doctor') {
        $ownsPatient = $patient->doctor_id === $user->id
            || $patient->appointments()->where('doctor_id', $user->id)->exists();
        if (!$ownsPatient) {
            abort(403);
        }
    }

    // Fetch Encounters (IPD, OPD, OR)
    $encounters = $patient->encounters()
        ->with(['doctor', 'admission', 'admission.bed.room.ward'])
        ->orderByDesc('created_at')
        ->get();

    $patient->load([
        'appointments',
        'bills',
        'assignedNurse',
        'doctor'
    ]);

    return view('core.core1.medical-records.show', compact('patient', 'encounters'));
}
}
