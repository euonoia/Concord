<?php

namespace App\Services\core1;

use App\Models\core1\SurgeryOrder;
use App\Models\core1\DietOrder;
use App\Models\core1\Encounter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurgeryDietService
{
    /**
     * Create a surgery order and sync to Core 2.
     */
    public function orderSurgery(Encounter $encounter, array $data): SurgeryOrder
    {
        $order = SurgeryOrder::create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'doctor_id' => $encounter->doctor_id,
            'procedure_name' => $data['procedure_name'],
            'priority' => $data['priority'] ?? 'Routine',
            'clinical_indication' => $data['clinical_indication'] ?? null,
            'status' => 'Ordered',
        ]);

        $this->syncOrderToCore2('surgery', $order, $data);

        return $order;
    }

    /**
     * Create a diet order and sync to Core 2.
     */
    public function orderDiet(Encounter $encounter, array $data): DietOrder
    {
        $order = DietOrder::create([
            'encounter_id' => $encounter->id,
            'patient_id' => $encounter->patient_id,
            'doctor_id' => $encounter->doctor_id,
            'diet_type' => $data['diet_type'],
            'instructions' => $data['instructions'] ?? null,
            'status' => 'Active',
        ]);

        $this->syncOrderToCore2('diet', $order, $data);

        return $order;
    }

    /**
     * Internal helper to sync order via API.
     */
    protected function syncOrderToCore2(string $type, $order, array $details): void
    {
        try {
            $response = Http::post(config('app.url') . '/api/surgery-diet-sync/order', [
                'type' => $type,
                'core1_order_id' => $order->id,
                'encounter_id' => $order->encounter_id,
                'patient_id' => $order->patient_id,
                'doctor_id' => $order->doctor_id,
                'details' => $details,
            ]);

            if ($response->successful()) {
                $order->update(['status' => 'Synced']);
            } else {
                Log::warning("SurgeryDietSync: Core 2 rejected {$type} order sync.", [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("SurgeryDietSync: Failed to reach Core 2 API for {$type} order.", [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
        }
    }
}
