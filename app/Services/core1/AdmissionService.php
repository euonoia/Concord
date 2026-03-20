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
     * Initiate Clinical Discharge (Doctor's Clearance).
     * Sets status to 'Doctor Approved' and captures all documentation required by HIS rules.
     */
    public function requestDischarge(Admission $admission, array $data): bool
    {
        return DB::transaction(function () use ($admission, $data) {
            // 1. Update Admission status to 'Doctor Approved'
            // Bed remains 'Occupied' until final release
            $admission->update([
                'status' => 'Doctor Approved'
            ]);

            // 2. Update encounter status for billing context
            if ($admission->encounter) {
                $admission->encounter->update(['status' => 'Pending Billing']);
                
                // 3. Aggregate final IPD charges for the billing office
                $billingService = app(BillingService::class);
                $billingService->aggregateCharges($admission->encounter);
            }

            // 4. Create/Update Discharge Record (Phase 4: Doctor's Clearance)
            DB::table('discharges_core1')->updateOrInsert(
                ['encounter_id' => $admission->encounter_id],
                [
                    'clearing_doctor_id'     => auth()->id(),
                    'discharge_summary'      => $data['discharge_summary'] ?? '',
                    'final_diagnosis'        => $data['final_diagnosis'] ?? '',
                    'discharge_type'         => $data['discharge_type'] ?? 'Routine',
                    'condition_on_discharge' => $data['condition_on_discharge'] ?? 'Improved',
                    'follow_up_instructions' => $data['follow_up_instructions'] ?? null,
                    'follow_up_date'         => $data['follow_up_date'] ?? null,
                    'created_at'             => Carbon::now(),
                    'updated_at'             => Carbon::now(),
                ]
            );

            return true;
        });
    }

    /**
     * Finalize Discharge & Release Bed (Final Act).
     * To be called after BOTH Clinical (Doctor) and Financial (Paid Bill) clearances.
     */
    public function finalizeDischarge(Admission $admission): bool
    {
        $result = DB::transaction(function () use ($admission) {
            // 1. Strict Validation: Clinical Clearance (Doctor's Approval)
            if ($admission->status !== 'Doctor Approved') {
                throw new \Exception('Patient requires Clinical Clearance (Doctor Approval) before final release.');
            }

            // 2. Strict Validation: Financial Clearance (Paid Bill)
            if ($admission->encounter) {
                $bill = \App\Models\user\Core\core1\Bill::where('encounter_id', $admission->encounter_id)
                    ->latest()
                    ->first();
                
                if (!$bill || $bill->status !== 'paid') {
                    throw new \Exception('Patient requires Financial Clearance (Paid Bill) before final release.');
                }
            }

            // 3. Release the Bed (Phase 4: Bed Release)
            if ($admission->bed) {
                $admission->bed->update(['status' => 'Available']);
            }

            // 4. Update Admission record
            $admission->update([
                'discharge_date' => Carbon::now(),
                'status'         => 'Discharged'
            ]);

            // 5. Permanently Close Encounter
            if ($admission->encounter) {
                $admission->encounter->update(['status' => 'Closed']);
            }

            return true;
        });

        // 6. Sync discharge to Core 2 Bed & Linen system
        $this->syncService->syncDischargeToCore2($admission);

        return $result;
    }

    /**
     * Transfer a patient to a new bed.
     * Releases old bed and occupies new bed within the same admission record.
     */
    public function transfer(Admission $admission, Bed $newBed): bool
    {
        $oldBedId = $admission->bed_id;
        $oldBed = Bed::findOrFail($oldBedId);

        $result = DB::transaction(function () use ($admission, $newBed, $oldBed) {
            // 1. Lock new bed 
            $newBed = Bed::where('id', $newBed->id)->lockForUpdate()->firstOrFail();

            if ($newBed->status !== 'Available') {
                throw new \Exception('The selected bed is no longer available.');
            }

            // 2. Release Old Bed (Set to Available, or Cleaning if per hospital flow)
            $oldBed->update(['status' => 'Available']);

            // 3. Update Admission with New Bed
            $admission->update([
                'bed_id' => $newBed->id
            ]);

            // 4. Occupy New Bed
            $newBed->update(['status' => 'Occupied']);

            return true;
        });

        // 5. Sync transfer to Core 2
        $this->syncService->syncTransferToCore2($admission, $oldBed, $newBed);

        return $result;
    }

    /**
     * Request a patient transfer (Clinician side).
     * Queues a request for Bed & Linen (Core 2) to fulfill.
     */
    public function requestTransfer(Admission $admission, ?int $targetBedId = null): bool
    {
        // 1. Queue for room assignment as a 'Transfer' type
        $this->syncService->queueTransferRequest($admission, $targetBedId);

        // 2. Optional: update encounter status or something
        // For now, just queuing is enough as per HIS flow.
        
        return true;
    }
}
