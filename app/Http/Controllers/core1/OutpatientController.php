<?php

namespace App\Http\Controllers\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Appointment;
use App\Models\core1\MedicalRecord;
use Carbon\Carbon;

class OutpatientController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Base Query (Outpatient Only)
        |--------------------------------------------------------------------------
        */
        $query = Appointment::with(['patient', 'doctor'])
            ->whereHas('patient', function ($q) {
                $q->where('care_type', 'outpatient');
            });

        // Doctor sees only her patients
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        $appointmentsRaw = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Consultation Tracking Data
        |--------------------------------------------------------------------------
        */
        $appointments = $appointmentsRaw->map(function ($apt) {

            $status = $apt->status ?? 'scheduled';

            return [
                'id' => $apt->id,
                'time' => Carbon::parse($apt->appointment_time)->format('Y-m-d h:i A'),
                'patient' => $apt->patient->name ?? 'Unknown',
                'type' => $apt->type,
                'status' => ucfirst(str_replace('_', ' ', $status)),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Arrival Logs & Triage Data
        |--------------------------------------------------------------------------
        */
        $registrations = $appointmentsRaw->map(function ($apt) use ($user) {

            $isEmergency = $apt->type === 'emergency';

            // Triage Display
            $triageDisplay = '';
            if ($apt->triage_note && $apt->vital_signs) {
                $triageDisplay = $apt->triage_note . ' - BP ' . $apt->vital_signs;
            }

            // Status Logic
            $status = $isEmergency ? 'Emergency' : 'Checking';

            if ($apt->triage_note) {
                $status = 'Triaged';
            }

            /*
            |--------------------------------------------------------------------------
            | Permission Logic
            |--------------------------------------------------------------------------
            */
            $canAction = false;

            // Doctor can only triage her own patients
            if ($user->role === 'doctor' && $apt->doctor_id == $user->id) {
                $canAction = true;
            }

            // Admin cannot click
            if (in_array($user->role, ['admin'])) {
                $canAction = false;
            }

            /*
            |--------------------------------------------------------------------------
            | Priority Label
            |--------------------------------------------------------------------------
            */
            $priority = '';

            return [
                'id' => $apt->id,
                'date' => Carbon::parse($apt->appointment_time)->format('Y-m-d h:i A'),
                'patient' => $apt->patient->name . $priority,
                'triage' => $triageDisplay,
                'status' => $status,
                'isEmergency' => $isEmergency,
                'canAction' => $canAction
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */
        $stats = [
            'my_appointments' => $appointments->count(),
            'consulted' => Appointment::whereDate('appointment_date', today())
                ->where('status', 'consulted')
                ->count(),
            'pending_results' => 0,
            'avg_consultation_time' => '12 min',
        ];

   $prescriptions = \App\Models\core1\MedicalRecord::where('record_type','prescription')
    ->when($user->role === 'doctor', function($q) use ($appointmentsRaw) {
        $patientIds = $appointmentsRaw->pluck('patient_id')->unique();
        $q->whereIn('patient_id', $patientIds);
    })
    ->get();



        /*
|--------------------------------------------------------------------------
| Diagnostic Orders (Functional)
|--------------------------------------------------------------------------
*/

$labOrders = \App\Models\core1\MedicalRecord::where('record_type', 'lab_order')
    ->when($user->role === 'doctor', function($q) use ($appointmentsRaw) {
        $patientIds = $appointmentsRaw->pluck('patient_id')->unique();
        $q->whereIn('patient_id', $patientIds);
    })
    ->get();

$diagnosticOrders = $labOrders->map(function($order){

    $patient = \App\Models\core1\Patient::find($order->patient_id);
    $data = json_decode($order->prescription, true);

    return [
        'id' => $order->id,
        'patient' => $patient->name ?? 'Unknown',
        'patient_id' => $patient->id ?? null, // <-- add this
        'test' => $data['test'] ?? '',
        'clinical_note' => $data['clinical_note'] ?? '',
        'status' => ucfirst(str_replace('_',' ', $data['status'] ?? 'ordered'))
    ];
});

/*
|--------------------------------------------------------------------------
| Follow-Up Data
|--------------------------------------------------------------------------
*/
$followUps = \App\Models\core1\MedicalRecord::where('record_type', 'follow_up')
    ->when($user->role === 'doctor', function($q) use ($appointmentsRaw) {
        // Only follow-ups for patients of this doctor
        $patientIds = $appointmentsRaw->pluck('patient_id')->unique();
        $q->whereIn('patient_id', $patientIds);
    })

    ->get()
    ->map(function($record){
        $patient = \App\Models\core1\Patient::find($record->patient_id);
        $data = json_decode($record->prescription, true);

        return [
            'id' => $record->id,
            'patient' => $patient->name ?? 'Unknown',
             'patient_id' => $patient->id ?? null, // <-- add this
            'next_visit' => $data['next_visit'] ?? '',
            'status' => ucfirst($data['status'] ?? 'scheduled')
        ];
    });


       $patients = \App\Models\core1\Patient::where('care_type','outpatient')
    ->when($user->role === 'doctor', function($q) use ($user, $appointmentsRaw) {
        // Only patients that have appointments for this doctor
        $patientIdsWithAppointments = $appointmentsRaw->pluck('patient_id')->unique();
        $q->whereIn('id', $patientIdsWithAppointments);
    })

    ->get();




        return view('core.core1.outpatient.index', compact(
            'stats',
            'appointments',
            'registrations',
            'prescriptions',
            'diagnosticOrders',
            'followUps',
            'patients' // <-- added
        ));
    }
    /*
    |--------------------------------------------------------------------------
    | Update Consultation Status
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, $id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['doctor', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:waiting,in_consultation,consulted'
        ]);

        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Save Triage
    |--------------------------------------------------------------------------
    */
    public function saveTriage(Request $request, $id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['doctor'])) {
            abort(403);
        }

        $request->validate([
            'triage_note' => 'required',
            'vital_signs' => 'required'
        ]);

        $appointment = Appointment::findOrFail($id);

        // Doctor can only triage her own patients
        if ($user->role === 'doctor' && $appointment->doctor_id != $user->id) {
            abort(403);
        }

        $appointment->update([
            'triage_note' => $request->triage_note,
            'vital_signs' => $request->vital_signs
        ]);

        return back()->with('success', 'Triage updated successfully.');
    }
    public function createPrescription()
{
    $user = auth()->user();

    // Only doctor can create prescriptions
    if (!in_array($user->role, ['doctor'])) {
        abort(403);
    }

    // Get doctor’s outpatient patients
    $patients = $user->role === 'doctor' 
        ? $user->patients()->where('care_type','outpatient')->get()
        : collect();

    // Example medications
    $medications = [
        'Atovastatin 40mg', 
        'Metformin 500mg',
        'Lisinopril 10mg',
        'Amlodipine 5mg',
        'Paracetamol 500mg'
    ];

    // Example dosages
    $dosages = [
        'Once Daily (Morning)',
        'Once Daily (Night)',
        'Twice Daily',
        'Three Times Daily (Morning, Afternoon, Night)',
        'As Needed'
    ];

    // Example instructions
    $instructions = [
        'Take with food',
        'Avoid alcohol',
        'Monitor blood pressure',
        'Take on empty stomach',
        'With plenty of water'
    ];

    return view('core.core1.outpatient.create_prescription', compact(
        'patients','medications','dosages','instructions'
    ));
}

/*
|--------------------------------------------------------------------------
| Store Prescription
|--------------------------------------------------------------------------
*/
// Store a new prescription
public function storePrescription(Request $request)
{
    $user = auth()->user();

    if (!in_array($user->role, ['doctor'])) {
        abort(403);
    }

    $request->validate([
        'patient_id' => 'required|exists:patients_core1,id', // <-- FIXED TABLE NAME
        'medication' => 'required|string',
        'dosage' => 'required|string',
        'instruction' => 'nullable|string',
    ]);

    MedicalRecord::create([
        'patient_id' => $request->patient_id,
        'doctor_id' => $user->id,
        'record_type' => 'prescription',
        'prescription' => json_encode([
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'instructions' => $request->instruction,
        ]),
        'record_date' => now(),
    ]);

    return back()->with('success', 'Prescription saved successfully.');
}


// Update an existing prescription
public function updatePrescription(Request $request, $id)
{
    $user = auth()->user();

    if (!in_array($user->role, ['doctor'])) {
        abort(403);
    }

    $request->validate([
        'medication' => 'required|string',
        'dosage' => 'required|string',
        'instruction' => 'nullable|string',
    ]);

    $record = MedicalRecord::findOrFail($id);

    $record->update([
        'prescription' => json_encode([
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'instructions' => $request->instruction,
        ]),
    ]);

    return back()->with('success', 'Prescription updated successfully.');
}

/*
|--------------------------------------------------------------------------
| Store Lab Order
|--------------------------------------------------------------------------
*/
public function storeLabOrder(Request $request)
{
    $user = auth()->user();

    if (!in_array($user->role, ['doctor','admin'])) {
        abort(403);
    }

    $request->validate([
        'patient_id' => 'required|exists:patients_core1,id',
        'test' => 'required|string',
        'clinical_note' => 'required|string',
    ]);

    \App\Models\core1\MedicalRecord::create([
        'patient_id' => $request->patient_id,
        'doctor_id' => $user->id,
        'record_type' => 'lab_order',
        'prescription' => json_encode([
            'test' => $request->test,
            'clinical_note' => $request->clinical_note,
            'status' => 'ordered' // default
        ]),
        'record_date' => now(),
    ]);

    return back()->with('success','Lab order created successfully.');
}
/*
|-------------------------------------------------------------------------- 
| Store Follow-Up
|-------------------------------------------------------------------------- 
*/
public function storeFollowUp(Request $request)
{
    $user = auth()->user();

    // Only doctor can schedule follow-ups
    if (!in_array($user->role, ['doctor'])) {
        abort(403);
    }

    $request->validate([
        'patient_id' => 'required|exists:patients_core1,id',
        'next_visit' => 'required|date|after:today',
    ]);

    // Check if patient has a consulted appointment
    $appointment = \App\Models\core1\Appointment::where('patient_id', $request->patient_id)
        ->where('status', 'consulted')
        ->first();

    if (!$appointment) {
        return back()->with('error', 'Follow-up can only be scheduled for patients who have been consulted.');
    }

    // Create the follow-up record
    \App\Models\core1\MedicalRecord::create([
        'patient_id' => $request->patient_id,
        'doctor_id' => $user->id,
        'record_type' => 'follow_up',
        'prescription' => json_encode([
            'next_visit' => $request->next_visit,
            'status' => 'scheduled',
        ]),
        'record_date' => now(),
    ]);

    return back()->with('success', 'Follow-up scheduled successfully.');
}

/*
|-------------------------------------------------------------------------- 
| Update Follow-Up (Modify Instructions / Change Date)
|-------------------------------------------------------------------------- 
*/
public function updateFollowUp(Request $request, $id)
{
    $user = auth()->user();

    // Only doctor can modify follow-ups
    if (!in_array($user->role, ['doctor'])) {
        abort(403);
    }

    $request->validate([
        'next_visit' => 'required|date|after:today',
    ]);

    $record = \App\Models\core1\MedicalRecord::findOrFail($id);

    // Update next visit date and set status to scheduled
    $record->update([
        'prescription' => json_encode([
            'next_visit' => $request->next_visit,
            'status' => 'scheduled',
        ]),
    ]);

    return back()->with('success', 'Follow-up date updated successfully.');
}


}

