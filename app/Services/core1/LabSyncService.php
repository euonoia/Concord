<?php

namespace App\Services\core1;

use App\Models\core1\LabOrder;
use App\Models\core2\TestOrder;
use Illuminate\Support\Facades\Log;

class LabSyncService
{
    /**
     * Sync a Core 1 lab order to Core 2 Laboratory via direct DB write.
     * Since Core 1 and Core 2 share the same database, we write directly
     * to test_orders_core2 instead of going through HTTP API.
     *
     * @param LabOrder $labOrder  The newly created lab order
     * @param array    $context   Additional context: patient_name, patient_mrn, ordering_doctor
     * @return bool Whether the sync was successful
     */
    public function syncToCore2(LabOrder $labOrder, array $context = []): bool
    {
        try {
            $testOrder = TestOrder::create([
                'order_id'           => 'LAB-' . str_pad($labOrder->id, 6, '0', STR_PAD_LEFT),
                'core1_lab_order_id' => $labOrder->id,
                'encounter_id'       => $labOrder->encounter_id,
                'patient_id'         => $labOrder->patient_id,
                'test_id'            => $labOrder->test_name,
                'test_name'          => $labOrder->test_name,
                'clinical_note'      => $labOrder->clinical_note,
                'patient_name'       => $context['patient_name'] ?? null,
                'patient_mrn'        => $context['patient_mrn'] ?? null,
                'ordering_doctor'    => $context['ordering_doctor'] ?? null,
                'priority'           => $labOrder->priority ?? 'Routine',
                'date_ordered'       => $labOrder->created_at->toDateString(),
                'status'             => 'Received',
            ]);

            $labOrder->update([
                'sync_status'    => 'Synced',
                'core2_order_id' => $testOrder->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('LabSync: Failed to sync to Core 2.', [
                'error'        => $e->getMessage(),
                'lab_order_id' => $labOrder->id,
            ]);

            $labOrder->update(['sync_status' => 'Failed']);

            return false;
        }
    }
}
