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
use App\Services\core1\AdmissionSyncService;
use Carbon\Carbon;

class OutpatientController extends Controller
{
    protected $outpatientService;
    protected $admissionSyncService;

    public function __construct(OutpatientService $outpatientService, AdmissionSyncService $admissionSyncService)
    {
        $this->outpatientService = $outpatientService;
        $this->admissionSyncService = $admissionSyncService;
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
        $diagnosticOrders = LabOrder::whereIn('encounter_id', $encounterIds)
            ->with(['patient', 'doctor', 'encounter.patient'])
            ->latest()
            ->get();

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
            // Queue patient to Core 2 Room Assignment for bed allocation
            $encounter->update(['type' => 'IPD']);
            $this->admissionSyncService->queueForRoomAssignment($encounter);

            return back()->with('success', 'Patient has been recommended for Admission. Queued for Room Assignment.');
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
            // Queue patient to Core 2 Room Assignment for bed allocation
            $encounter->update(['type' => 'IPD']);
            $this->admissionSyncService->queueForRoomAssignment($encounter);

            return back()->with('success', 'Admission recommended. Patient queued for Room Assignment.');
        }

        // Default: Discharge path - move to Pending Billing
        $encounter->update(['status' => 'Pending Billing']);
        
        // Trigger charge aggregation for the new bill
        app(\App\Services\core1\BillingService::class)->aggregateCharges($encounter);
        
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
            'quantity' => 'required|integer|min:1',
        ]);

        $prescription = Prescription::create($request->all());

        // Sync to Core 2 Pharmacy
        $syncService = app(\App\Services\core1\PrescriptionSyncService::class);
        $syncService->syncToCore2($prescription);

        return back()->with('success', 'Prescription issued and sent to pharmacy.');
    }

    public function storeLabOrder(Request $request)
    {
        $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'test_name' => 'required|string',
            'clinical_note' => 'nullable|string',
            'priority' => 'nullable|in:Routine,Urgent,STAT',
        ]);

        $encounter = Encounter::with('patient', 'doctor')->findOrFail($request->encounter_id);

        $labOrder = LabOrder::create([
            'encounter_id' => $encounter->id,
            'patient_id'   => $encounter->patient_id,
            'doctor_id'    => auth()->user()->id,
            'test_name'    => $request->test_name,
            'clinical_note' => $request->clinical_note,
            'priority'     => $request->priority ?? 'Routine',
            'status'       => 'Ordered',
            'sync_status'  => 'Pending',
        ]);

        // Sync to Core 2 Laboratory via internal API
        $syncService = app(\App\Services\core1\LabSyncService::class);
        $syncService->syncToCore2($labOrder, [
            'patient_name'    => $encounter->patient->name ?? 'Unknown',
            'patient_mrn'     => $encounter->patient->mrn ?? null,
            'ordering_doctor' => 'Dr. ' . (auth()->user()->name ?? 'Unknown'),
        ]);

        return back()->with('success', 'Lab order created and synced to laboratory.');
    }

    public function storeSurgeryOrder(Request $request)
    {
        $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'procedure_name' => 'required|string',
            'priority' => 'nullable|in:Routine,Urgent,STAT',
            'clinical_indication' => 'nullable|string',
            'proposed_date' => 'required|date|after_or_equal:today',
            'proposed_time' => 'required',
        ]);

        $encounter = Encounter::findOrFail($request->encounter_id);
        $service = app(\App\Services\core1\SurgeryDietService::class);
        $service->orderSurgery($encounter, $request->all());

        return back()->with('success', 'Surgery order created and synced to OR.');
    }

    public function storeDietOrder(Request $request)
    {
        $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'diet_type' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        $encounter = Encounter::findOrFail($request->encounter_id);
        $service = app(\App\Services\core1\SurgeryDietService::class);
        $service->orderDiet($encounter, $request->all());

        return back()->with('success', 'Diet order created and synced to Nutrition.');
    }

    public function getDiagnosticOrdersJson()
    {
        $user = auth()->user();
        $query = Encounter::whereIn('type', ['OPD', 'Pending'])
            ->where('status', 'Active');

        if ($user->role_slug === 'doctor') {
            $query->where('doctor_id', $user->id);
        }
        $encounterIds = $query->pluck('id');

        $diagnosticOrders = LabOrder::whereIn('encounter_id', $encounterIds)
            ->with(['patient', 'doctor', 'encounter.patient'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'created_at_fmt' => $order->created_at->format('Y-m-d'),
                    'patient_name' => $order->patient->name ?? $order->encounter->patient->name ?? 'Unknown',
                    'doctor_full' => $order->doctor->name ?? 'Unknown',
                    'test_name' => $order->test_name,
                    'priority' => $order->priority ?? 'Routine',
                    'clinical_note' => $order->clinical_note,
                    'sync_status' => $order->sync_status ?? 'Pending',
                    'result_data' => $order->result_data,
                    'result_received_at_fmt' => $order->result_received_at ? $order->result_received_at->format('Y-m-d H:i') : ''
                ];
            });

        return response()->json($diagnosticOrders);
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
            // Queue patient to Core 2 Room Assignment for bed allocation
            $encounter->update(['type' => 'IPD']);
            $this->admissionSyncService->queueForRoomAssignment($encounter);

            return back()->with('success', $message . ' Patient queued for Room Assignment.');
        }

        return back()->with('success', $message);
    }

    public function administerMedication(Request $request, Prescription $prescription)
    {
        \App\Models\core1\MedicationAdministration::create([
            'prescription_id' => $prescription->id,
            'encounter_id'    => $prescription->encounter_id,
            'administered_by' => auth()->id(),
            'administered_at' => now(),
            'status'          => 'Administered',
        ]);

        $prescription->update(['status' => 'Administered']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Medication marked as administered.'
            ]);
        }

        return back()->with('success', 'Medication marked as administered.');
    }
}

