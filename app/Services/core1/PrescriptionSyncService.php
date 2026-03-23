<?php

namespace App\Services\core1;

use App\Models\core1\Prescription;
use App\Models\core2\Prescription as PharmacyOrder;
use Illuminate\Support\Facades\Log;

class PrescriptionSyncService
{
    /**
     * Sync a Core 1 prescription to Core 2 Pharmacy via direct DB write.
     * Since Core 1 and Core 2 share the same database, we write directly
     * to prescriptions_core2 instead of going through HTTP API, following
     * the LabSyncService pattern.
     *
     * @param Prescription $prescription The newly created prescription
     * @return bool Whether the sync was successful
     * @throws \Exception
     */
    public function syncToCore2(Prescription $prescription): bool
    {
        try {
            // Check inventory in Core 2 before syncing
            $inventory = \App\Models\core2\DrugInventory::where('drug_name', trim($prescription->medication))->first();
            
            if (!$inventory) {
                $inventory = \App\Models\core2\DrugInventory::where('drug_name', 'LIKE', '%' . trim($prescription->medication) . '%')->first();
            }

            if (!$inventory || $inventory->quantity < $prescription->quantity) {
                $available = $inventory ? $inventory->quantity : 0;
                throw new \Exception("Insufficient stock in Pharmacy. Requested: {$prescription->quantity}, Available: {$available}");
            }

            // Create the record in Core 2 Pharmacy
            $pharmacyOrder = PharmacyOrder::create([
                'prescription_id'        => $prescription->id, // This matches core2's logic of identifier
                'core1_prescription_id'  => $prescription->id,
                'patient_id'             => $prescription->encounter->patient_id ?? null,
                'doctor_id'              => $prescription->encounter->doctor_id ?? null,
                'date'                   => now()->toDateString(),
                'drug_id'                => $prescription->medication, // Mapping name to drug_id for now if inventory not found
                'quantity'               => $prescription->quantity,
                'status'                 => 'Received',
            ]);

            // Update Core 1 record with the pharmacy link
            $prescription->update([
                'status'            => 'Synced',
                'core2_pharmacy_id' => $pharmacyOrder->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('PharmacySync: Failed to sync prescription to Core 2.', [
                'error'           => $e->getMessage(),
                'prescription_id' => $prescription->id,
            ]);

            throw $e;
        }
    }
}
