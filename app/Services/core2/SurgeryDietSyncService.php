<?php

namespace App\Services\core2;

use App\Models\core2\OperatingRoomBooking;
use App\Models\core2\NutritionalAssessment;
use App\Models\core2\SurgeryRecord;
use App\Models\core2\MealLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurgeryDietSyncService
{
    /**
     * Sync completed surgery data back to Core 1.
     */
    public function syncSurgeryCompletion(SurgeryRecord $record): bool
    {
        try {
            $booking = $record->booking;
            
            if (!$booking || !$booking->core1_surgery_order_id) {
                return false;
            }

            $response = Http::post(config('app.url') . '/api/surgery-diet-sync/result', [
                'type' => 'surgery',
                'core1_order_id' => $booking->core1_surgery_order_id,
                'core2_fulfillment_id' => $record->id,
                'result_data' => [
                    'procedure' => $booking->procedure_name ?? 'Minor Surgery',
                    'findings' => $record->findings,
                    'post_op' => $record->post_op_instructions,
                    'start' => $record->start_time,
                    'end' => $record->end_time,
                ],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('SurgeryDietSync: Failed to sync surgery result back to Core 1.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sync meal delivery status back to Core 1.
     */
    public function syncMealLog(MealLog $log): bool
    {
        try {
            $response = Http::post(config('app.url') . '/api/surgery-diet-sync/result', [
                'type' => 'diet',
                'core1_order_id' => $log->diet_order_id,
                'core2_fulfillment_id' => $log->id,
                'result_data' => [
                    'meal' => $log->meal_type,
                    'status' => $log->delivery_status,
                    'at' => $log->delivered_at,
                ],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('SurgeryDietSync: Failed to sync diet result back to Core 1.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
