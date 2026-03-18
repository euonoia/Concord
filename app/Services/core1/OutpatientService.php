<?php

namespace App\Services\core1;

use App\Models\core1\Encounter;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutpatientService
{
    /**
     * Record Triage for an OPD encounter.
     * follows Phase 2: Triage of HIS Architect Rules.
     */
    public function recordTriage(Encounter $encounter, array $vitals): Triage
    {
        return DB::transaction(function () use ($encounter, $vitals) {
            // HIS Architect Rules: Triage history is required. Always create a new record.
            $triage = new Triage();
            
            $triage->fill([
                'encounter_id' => $encounter->id,
                'blood_pressure' => $vitals['blood_pressure'] ?? null,
                'heart_rate' => $vitals['heart_rate'] ?? null,
                'respiratory_rate' => $vitals['respiratory_rate'] ?? null,
                'temperature' => $vitals['temperature'] ?? null,
                'spo2' => $vitals['spo2'] ?? $vitals['oxygen_saturation'] ?? null,
                'triage_level' => $vitals['triage_level'] ?? null,
                'notes' => $vitals['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $triage->save();

            return $triage;
        });
    }

    /**
     * Record Consultation SOAP notes.
     * follows Phase 4: Path A — Outpatient (OPD) Consultation.
     */
    public function saveConsultation(Encounter $encounter, array $notes): Consultation
    {
        return DB::transaction(function () use ($encounter, $notes) {
            $consultation = Consultation::where('encounter_id', $encounter->id)->first() ?: new Consultation();

            $consultation->fill([
                'encounter_id' => $encounter->id,
                'subjective' => $notes['subjective'] ?? null,
                'objective' => $notes['objective'] ?? null,
                'assessment' => $notes['assessment'] ?? null,
                'plan' => $notes['plan'] ?? null,
                'doctor_notes' => $notes['doctor_notes'] ?? null,
            ]);

            $consultation->save();

            return $consultation;
        });
    }

    /**
     * Complete OPD encounter and close it.
     */
    public function completeEncounter(Encounter $encounter): bool
    {
        return $encounter->update([
            'status' => 'Closed',
            'updated_at' => Carbon::now()
        ]);
    }
}
