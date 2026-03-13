<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Encounter;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;
use App\Models\core1\Ward;
use App\Models\user\Core\core1\Patient;
use App\Services\core1\OutpatientService;
use Carbon\Carbon;

class OutpatientController extends Controller
{
    protected $outpatientService;

    public function __construct(OutpatientService $outpatientService)
    {
        $this->outpatientService = $outpatientService;
    }

    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Base Query (OPD Encounters)
        |--------------------------------------------------------------------------
        */
        $query = Encounter::with(['patient', 'doctor', 'triage', 'consultation'])
            ->whereIn('type', ['OPD', 'Pending'])
            ->where('status', 'Active');

        // Doctor sees only her patients
        if ($user->role_slug === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        $encountersRaw = $query->orderBy('created_at', 'desc')->get();

        /*
        |--------------------------------------------------------------------------
        | Consultation Tracking Data
        |--------------------------------------------------------------------------
        */
        $appointments = $encountersRaw->where('type', 'OPD')->map(function ($encounter) {
            $status = 'Active';
            if ($encounter->triage) {
                $status = 'Triaged';
            }
            if ($encounter->consultation) {
                $status = 'In consultation';
            }

            return [
                'id' => $encounter->id,
                'time' => $encounter->created_at->format('Y-m-d h:i A'),
                'patient' => $encounter->patient->name ?? 'Unknown',
                'patient_id' => $encounter->patient_id,
                'triage' => $encounter->triage ? [
                    'blood_pressure' => $encounter->triage->blood_pressure,
                    'heart_rate'     => $encounter->triage->heart_rate,
                    'temperature'    => $encounter->triage->temperature,
                    'spo2'           => $encounter->triage->spo2,
                    'triage_level'   => $encounter->triage->triage_level,
                    'notes'          => $encounter->triage->notes,
                    'summary'        => "BP: {$encounter->triage->blood_pressure}, HR: {$encounter->triage->heart_rate}, Temp: {$encounter->triage->temperature}"
                ] : null,
                'type' => $encounter->type,
                'status' => $status,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Arrival Logs & Triage Data
        |--------------------------------------------------------------------------
        */
        $registrations = $encountersRaw->map(function ($encounter) use ($user) {
            $status = $encounter->triage ? 'Triaged' : 'Waiting';
            
            $canAction = true;

            return [
                'id' => $encounter->id,
                'date' => $encounter->created_at->format('Y-m-d h:i A'),
                'patient' => $encounter->patient->name ?? 'Unknown',
                'triage' => $encounter->triage ? "BP: {$encounter->triage->blood_pressure}, HR: {$encounter->triage->heart_rate}" : 'No Triage',
                'status' => $status,
                'type' => $encounter->type,
                'patient_id' => $encounter->patient_id,
                'isEmergency' => false,
                'canAction' => $canAction
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */
        $stats = [
            'my_appointments' => $encountersRaw->count(),
            'consulted' => Encounter::where('type', 'OPD')
                ->where('status', 'Closed')
                ->whereDate('updated_at', today())
                ->count(),
            'pending_results' => 0,
            'avg_consultation_time' => '15 min',
        ];

        // Fetching existing orders/prescriptions linked to these active encounters
        $encounterIds = $encountersRaw->pluck('id');

        $prescriptions = Prescription::whereIn('encounter_id', $encounterIds)->get();
        $diagnosticOrders = LabOrder::whereIn('encounter_id', $encounterIds)->get();

        $followUps = []; // Potentially handle follow-ups via a dedicated table later

        $patients = Patient::where('care_type', 'outpatient')->get();

        $wards = Ward::with(['rooms.beds.admissions' => function($query) {
            $query->where('status', 'Admitted')->with('encounter.patient');
        }])->get();

        $doctors = \App\Models\User::where('role_slug', 'doctor')->get();

        return view('core.core1.outpatient.index', compact(
            'stats',
            'appointments',
            'registrations',
            'prescriptions',
            'diagnosticOrders',
            'followUps',
            'patients',
            'wards',
            'doctors'
        ));
    }
    /*
    |--------------------------------------------------------------------------
    | Triage & Consultation Actions
    |--------------------------------------------------------------------------
    */

    public function saveTriage(Request $request, $id)
    {
        $request->validate([
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer',
            'temperature' => 'nullable|numeric',
            'spo2' => 'nullable|integer',
            'triage_level' => 'nullable|in:1,2,3,4,5',
            'notes' => 'nullable|string',
            'send_to_admission' => 'nullable|boolean'
        ]);

        $encounter = Encounter::findOrFail($id);
        $this->outpatientService->recordTriage($encounter, $request->all());

        if ($request->input('send_to_admission')) {
            $encounter->update(['type' => 'IPD']);
            return back()->with('open_admission_modal', $encounter->id)
                ->with('success', 'Triage recorded and admission recommended. Please complete the admission details.');
        }

        return back()->with('success', 'Triage vitals recorded.');
    }

    public function saveConsultation(Request $request, $id)
    {
        $request->validate([
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'doctor_notes' => 'nullable|string'
        ]);

        $encounter = Encounter::findOrFail($id);
        
        if (!$encounter->triage) {
            return back()->with('error', 'Patient must be triaged before consultation.');
        }

        $this->outpatientService->saveConsultation($encounter, $request->all());

        return back()->with('success', 'Consultation notes saved.');
    }

    public function completeConsultation(Request $request, $id)
    {
        $encounter = Encounter::findOrFail($id);

        if (!$encounter->triage) {
            return back()->with('error', 'Patient must be triaged before closing encounter.');
        }

        $disposition = $request->input('disposition', 'discharge');

        if ($disposition === 'admit') {
            $encounter->update(['type' => 'IPD']);
            return back()->with('open_admission_modal', $encounter->id)
                ->with('success', 'Admission recommended. Please complete the admission details.');
        }

        // Default: Discharge path - move to Pending Billing
        $encounter->update(['status' => 'Pending Billing']);
        
        return redirect()->route('core1.billing.index')
            ->with('success', 'Consultation completed. Patient moved to billing for discharge settlement.');
    }

    /*
    |--------------------------------------------------------------------------
    | Orders & Prescriptions
    |--------------------------------------------------------------------------
    */

    public function storePrescription(Request $request)
    {
        $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'medication' => 'required|string',
            'dosage' => 'required|string',
            'instructions' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        Prescription::create($request->all());

        return back()->with('success', 'Prescription issued successfully.');
    }

    public function storeLabOrder(Request $request)
    {
        $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'test_name' => 'required|string',
            'clinical_note' => 'nullable|string',
        ]);

        LabOrder::create($request->all());

        return back()->with('success', 'Lab order created.');
    }
    public function disposition(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:OPD,IPD'
        ]);

        $encounter = Encounter::findOrFail($id);
        $encounter->update(['type' => $validated['type']]);

        $message = "Encounter dispositioned to " . $validated['type'] . ".";

        if ($validated['type'] === 'IPD') {
            return back()->with('open_admission_modal', $encounter->id)
                ->with('success', $message . ' Please complete the admission details.');
        }

        return back()->with('success', $message);
    }
}

