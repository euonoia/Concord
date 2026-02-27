<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\MedicalRecord;
use App\Models\core1\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();

     if ($user->role === 'doctor') {
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
        ->orderBy('name')
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
    ])->orderBy('name')->paginate(10);
}

        return view('core.core1.medical-records.index', compact('records'));
    }

  public function show(Patient $patient)
{
    $user = auth()->user();

    // Only allow doctor to see assigned patients or patients with appointments
    if ($user->role === 'doctor') {
        $ownsPatient = $patient->doctor_id === $user->id
            || $patient->appointments()->where('doctor_id', $user->id)->exists();
        if (!$ownsPatient) {
            abort(403);
        }
    }

    // Get latest medical record if exists
    $record = $patient->medicalRecords()->latest('record_date')->first();

    // If no record, create dummy object
    if (!$record) {
        $record = new \App\Models\core1\MedicalRecord();
        $record->patient = $patient;
        $record->doctor = $patient->doctor ?? null;
        $record->record_type = null;
        $record->record_date = null;
        $record->diagnosis = null;
        $record->treatment = null;
        $record->prescription = null;
        $record->notes = null;
    } else {
        $record->load([
            'patient',
            'doctor',
            'patient.appointments',
            'patient.bills',
            'patient.assignedNurse'
        ]);
    }

    return view('core.core1.medical-records.show', compact('record'));
}
}
