<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Encounter;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;
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
        $query = Encounter::with(['patient', 'doctor', 'triage'])
            ->where('type', 'OPD')
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
        $appointments = $encountersRaw->map(function ($encounter) {
            return [
                'id' => $encounter->id,
                'time' => $encounter->created_at->format('Y-m-d h:i A'),
                'patient' => $encounter->patient->name ?? 'Unknown',
                'type' => $encounter->type,
                'status' => $encounter->triage ? 'Triaged' : 'Active',
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Arrival Logs & Triage Data
        |--------------------------------------------------------------------------
        */
        $registrations = $encountersRaw->map(function ($encounter) use ($user) {
            $status = $encounter->triage ? 'Triaged' : 'Waiting';
            
            $canAction = false;
            if ($user->role_slug === 'doctor' && $encounter->doctor_id == $user->id) {
                $canAction = true;
            }

            return [
                'id' => $encounter->id,
                'date' => $encounter->created_at->format('Y-m-d h:i A'),
                'patient' => $encounter->patient->name ?? 'Unknown',
                'triage' => $encounter->triage ? "BP: {$encounter->triage->blood_pressure}, HR: {$encounter->triage->heart_rate}" : 'No Triage',
                'status' => $status,
                'isEmergency' => false, // Could be derived from encounter if needed
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

        return view('core.core1.outpatient.index', compact(
            'stats',
            'appointments',
            'registrations',
            'prescriptions',
            'diagnosticOrders',
            'followUps',
            'patients'
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
            'notes' => 'nullable|string'
        ]);

        $encounter = Encounter::findOrFail($id);
        $this->outpatientService->recordTriage($encounter, $request->all());

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
        $this->outpatientService->saveConsultation($encounter, $request->all());

        return back()->with('success', 'Consultation notes saved.');
    }

    public function completeConsultation(Request $request, $id)
    {
        $encounter = Encounter::findOrFail($id);
        $this->outpatientService->completeEncounter($encounter);

        return redirect()->route('core1.outpatient.index')->with('success', 'Encounter closed and consultation completed.');
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
}

