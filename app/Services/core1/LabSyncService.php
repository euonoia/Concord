<?php

namespace App\Services\core1;

use App\Models\core1\LabOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LabSyncService
{
    /**
     * Sync a Core 1 lab order to Core 2 Laboratory via internal API.
     *
     * @param LabOrder $labOrder  The newly created lab order
     * @param array    $context   Additional context: patient_name, patient_mrn, ordering_doctor
     * @return bool Whether the sync was successful
     */
    public function syncToCore2(LabOrder $labOrder, array $context = []): bool
    {
        $payload = [
            'core1_lab_order_id' => $labOrder->id,
            'encounter_id'       => $labOrder->encounter_id,
            'patient_id'         => $labOrder->patient_id,
            'doctor_id'          => $labOrder->doctor_id,
            'test_name'          => $labOrder->test_name,
            'clinical_note'      => $labOrder->clinical_note,
            'patient_name'       => $context['patient_name'] ?? null,
            'patient_mrn'        => $context['patient_mrn'] ?? null,
            'ordering_doctor'    => $context['ordering_doctor'] ?? null,
            'priority'           => $labOrder->priority ?? 'Routine',
            'date_ordered'       => $labOrder->created_at->toDateString(),
        ];

        try {
            $baseUrl = rtrim(config('app.url'), '/');
            $response = Http::timeout(10)
                ->acceptJson()
                ->post("{$baseUrl}/api/lab-sync/order", $payload);

            if ($response->successful() && $response->json('success')) {
                $labOrder->update([
                    'sync_status'    => 'Synced',
                    'core2_order_id' => $response->json('core2_order_id'),
                ]);

                return true;
            }

            Log::warning('LabSync: Core 2 returned non-success response.', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'payload' => $payload,
            ]);

            $labOrder->update(['sync_status' => 'Failed']);

            return false;
        } catch (\Exception $e) {
            Log::error('LabSync: Failed to sync to Core 2.', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);

            $labOrder->update(['sync_status' => 'Failed']);

            return false;
        }
    }
}
