<?php

namespace App\Services\core1;

use App\Models\core1\Admission;
use App\Models\core1\Encounter;
use App\Models\core1\Bed;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\core1\AdmissionSyncService;
use App\Services\core1\BillingService;

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
     * Initiate Clinical Discharge.
     * Sets status to 'Ready for Discharge' and triggers billing aggregation.
     */
    public function requestDischarge(Admission $admission, array $data): bool
    {
        return DB::transaction(function () use ($admission, $data) {
            // 1. Validate discharge summary and diagnosis are provided
            if (empty($data['discharge_summary']) || empty($data['final_diagnosis'])) {
                throw new \Exception('Discharge summary and final diagnosis are required.');
            }

            // 2. Update Admission status to 'Doctor Approved'
            // Bed remains 'Occupied' until financial clearance
            $admission->update([
                'status' => 'Doctor Approved'
            ]);

            // 3. Update encounter status for billing
            if ($admission->encounter) {
                $admission->encounter->update(['status' => 'Pending Billing']);
                
                // 4. Aggregate final IPD charges for the billing office
                $billingService = app(BillingService::class);
                $billingService->aggregateCharges($admission->encounter);
            }

            // 5. Create Discharge Record (Clinical Documentation)
            DB::table('discharges_core1')->updateOrInsert(
                ['encounter_id' => $admission->encounter_id],
                [
                    'discharge_summary' => $data['discharge_summary'],
                    'final_diagnosis' => $data['final_diagnosis'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            return true;
        });
    }

    /**
     * Finalize Discharge & Release Bed.
     * To be called after financial clearance.
     */
    public function finalizeDischarge(Admission $admission): bool
    {
        $result = DB::transaction(function () use ($admission) {
            // 1. Validate clinical discharge was completed
            if ($admission->status !== 'Doctor Approved') {
                throw new \Exception('Patient must have doctor approval before final release.');
            }

            // 2. Release the Bed (Phase 4: Bed Release)
            if ($admission->bed) {
                $admission->bed->update(['status' => 'Available']);
            }

            // 3. Complete Admission
            $admission->update([
                'discharge_date' => Carbon::now(),
                'status' => 'Discharged'
            ]);

            // 4. Close encounter if billing is fully paid (done in BillingService, but safety check here)
            if ($admission->encounter) {
                $bill = $admission->encounter->patient->bills()
                    ->where('encounter_id', $admission->encounter_id)
                    ->latest()
                    ->first();
                
                if ($bill && $bill->status === 'paid') {
                    $admission->encounter->update(['status' => 'Closed']);
                }
            }

            return true;
        });

        // 5. Sync discharge to Core 2 Bed & Linen (release bed)
        $this->syncService->syncDischargeToCore2($admission);

        return $result;
    }
}
