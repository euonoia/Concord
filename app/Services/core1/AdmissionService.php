<?php

namespace App\Services\core1;

use App\Models\core1\Admission;
use App\Models\core1\Encounter;
use App\Models\core1\Bed;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\core1\AdmissionSyncService;

class AdmissionService
{
    protected AdmissionSyncService $syncService;

    public function __construct(AdmissionSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Admit a patient to a bed.
     * follows Phase 3: Clinical Care (ADT) of HIS Architect Rules.
     */
    public function admit(Encounter $encounter, Bed $bed): Admission
    {
        $admission = DB::transaction(function () use ($encounter, $bed) {
            // 1. Lock bed for update to prevent double booking
            $bed = Bed::where('id', $bed->id)->lockForUpdate()->firstOrFail();
            
            if ($bed->status !== 'Available') {
                throw new \Exception('The selected bed is no longer available.');
            }

            // 2. Create Admission record
            $admission = Admission::create([
                'encounter_id' => $encounter->id,
                'bed_id' => $bed->id,
                'admission_date' => Carbon::now(),
                'status' => 'Admitted'
            ]);

            // 3. Update Bed status to Occupied
            $bed->update(['status' => 'Occupied']);

            // 4. Update Encounter type to IPD (Status Escalation)
            $encounter->update(['type' => 'IPD']);

            return $admission;
        });

        // 5. Sync admission to Core 2 Bed & Linen
        $this->syncService->syncAdmissionToCore2($encounter, $bed, $admission);

        return $admission;
    }

    /**
     * Discharge a patient.
     * follows Phase 4: Conclusion (Discharge & Ledger) of HIS Architect Rules.
     */
    public function discharge(Admission $admission, array $data): bool
    {
        $result = DB::transaction(function () use ($admission, $data) {
            // 1. Validate discharge summary and diagnosis are provided (as per rule)
            if (empty($data['discharge_summary']) || empty($data['final_diagnosis'])) {
                throw new \Exception('Discharge summary and final diagnosis are required.');
            }

            // 2. Update Admission status
            $admission->update([
                'discharge_date' => Carbon::now(),
                'status' => 'Discharged'
            ]);

            // 3. Release the Bed (Phase 4: Bed Release)
            $admission->bed->update(['status' => 'Available']);

            // 4. Close the Encounter (Phase 4: Encounter Closure)
            $admission->encounter->update(['status' => 'Closed']);

            // 5. Create Discharge Record
            DB::table('discharges_core1')->insert([
                'encounter_id' => $admission->encounter_id,
                'discharge_summary' => $data['discharge_summary'],
                'final_diagnosis' => $data['final_diagnosis'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return true;
        });

        // 6. Sync discharge to Core 2 Bed & Linen (release bed)
        $this->syncService->syncDischargeToCore2($admission);

        return $result;
    }
}
