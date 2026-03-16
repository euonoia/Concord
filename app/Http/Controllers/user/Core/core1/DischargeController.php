<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\core1\Admission;
use App\Services\core1\AdmissionService;
use Illuminate\Http\Request;

class DischargeController extends Controller
{
    protected AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function index()
    {
        $user = Auth::user();

        // 1. Fetch IPD Admissions (Admitted or Doctor Approved)
        $admissionQuery = Admission::with([
            'encounter.patient.bills.validator', 
            'encounter.doctor', 
            'bed.room.ward',
            'discharge.clearingDoctor'
        ])
        ->whereIn('status', ['Admitted', 'Doctor Approved']);

        if ($user->role_slug === 'doctor') {
            $admissionQuery->whereHas('encounter', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        $admissions = $admissionQuery->latest('admission_date')->get();

        // 2. Fetch OPD Encounters (Pending Billing)
        $opdQuery = \App\Models\core1\Encounter::with(['patient.bills.validator', 'doctor'])
            ->where('type', 'OPD')
            ->where('status', 'Pending Billing');

        if ($user->role_slug === 'doctor') {
            $opdQuery->where('doctor_id', $user->id);
        }

        $opdEncounters = $opdQuery->latest()->get();

        // 3. Unify into a single list for the view
        // We'll map them to a consistent structure
        $items = collect();

        foreach ($admissions as $admission) {
            $items->push([
                'id' => $admission->id,
                'type' => 'IPD',
                'patient' => $admission->encounter->patient,
                'doctor' => $admission->encounter->doctor,
                'location' => $admission->bed ? ($admission->bed->room->ward->name . ' - Room ' . $admission->bed->room->room_number . ' - Bed ' . $admission->bed->bed_number) : 'No Bed',
                'admission_date' => $admission->admission_date,
                'status' => $admission->status,
                'clearance_clinical' => [
                    'approved' => $admission->status === 'Doctor Approved',
                    'doctor' => $admission->discharge?->clearingDoctor?->name
                ],
                'clearance_financial' => [
                    'bill' => $admission->encounter->patient->bills()
                        ->where('encounter_id', $admission->encounter_id)
                        ->latest()
                        ->first()
                ],
                'original_record' => $admission
            ]);
        }

        foreach ($opdEncounters as $encounter) {
            $items->push([
                'id' => $encounter->id,
                'type' => 'OPD',
                'patient' => $encounter->patient,
                'doctor' => $encounter->doctor,
                'location' => 'Outpatient',
                'admission_date' => $encounter->created_at,
                'status' => 'Pending Billing',
                'clearance_clinical' => [
                    'approved' => true, // OPD is "Doctor Approved" once it reaches Pending Billing from consultation
                    'doctor' => $encounter->doctor->name ?? 'Attending Doctor'
                ],
                'clearance_financial' => [
                    'bill' => $encounter->patient->bills()
                        ->where('encounter_id', $encounter->id)
                        ->latest()
                        ->first()
                ],
                'original_record' => $encounter
            ]);
        }

        $items = $items->sortByDesc('admission_date');

        return view('core.core1.discharge.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_id'           => 'required|exists:admissions_core1,id',
            'discharge_summary'      => 'required|string',
            'final_diagnosis'        => 'required|string',
            'discharge_type'         => 'required|string',
            'condition_on_discharge' => 'required|string',
            'follow_up_instructions' => 'nullable|string',
            'follow_up_date'         => 'nullable|date',
        ]);

        try {
            $admission = Admission::findOrFail($validated['admission_id']);
            
            $this->admissionService->requestDischarge($admission, $validated);

            return redirect()->back()->with('success', 'Discharge approved by doctor. Waiting for financial clearance.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Discharge initiation failed: ' . $e->getMessage());
        }
    }

    /**
     * Finalize the discharge after financial clearance.
     */
    public function finalize(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required|in:IPD,OPD'
        ]);

        try {
            if ($request->type === 'IPD') {
                $admission = Admission::findOrFail($request->id);
                $this->admissionService->finalizeDischarge($admission);
            } else {
                $encounter = \App\Models\core1\Encounter::findOrFail($request->id);
                
                // Financial Clearance Check for OPD
                $bill = \App\Models\user\Core\core1\Bill::where('encounter_id', $encounter->id)
                    ->latest()
                    ->first();
                
                if (!$bill || $bill->status !== 'paid') {
                    throw new \Exception('Patient requires Financial Clearance (Paid Bill) before final release.');
                }

                $encounter->update(['status' => 'Closed']);
            }

            return redirect()->back()->with('success', 'Patient finalized and record closed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Finalization failed: ' . $e->getMessage());
        }
    }
}
