<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\MedicalRecord;
use App\Models\user\Core\core1\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Patient::query();

        // Role-based scoping
        if ($user->role_slug === 'doctor') {
            $query->where(function ($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->orWhereHas('appointments', function ($q2) use ($user) {
                      $q2->where('doctor_id', $user->id);
                  });
            });
            
            $query->with([
                'encounters' => function ($q) {
                    $q->orderByDesc('created_at');
                },
                'appointments' => function ($q) use ($user) {
                    $q->where('doctor_id', $user->id)
                      ->orderByDesc('appointment_date')
                      ->orderByDesc('appointment_time');
                },
            ]);
        } else {
            // Admin, Head Nurse, Nurse → show all patients
            $query->with([
                'encounters' => function ($q) {
                    $q->orderByDesc('created_at');
                },
                'appointments' => function ($q) {
                    $q->orderByDesc('appointment_date')->orderByDesc('appointment_time');
                }
            ]);
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('last_name')
                         ->orderBy('first_name')
                         ->paginate(10)
                         ->appends(['search' => $request->search]);

        if ($request->ajax()) {
            return view('core.core1.medical-records.partials.table', compact('records'))->render();
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
        ->with([
            'doctor', 
            'admission', 
            'admission.bed.room.ward',
            'triage',
            'consultation',
            'labOrders',
            'prescriptions'
        ])
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
