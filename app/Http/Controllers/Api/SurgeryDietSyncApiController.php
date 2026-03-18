<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\core1\SurgeryOrder;
use App\Models\core1\DietOrder;
use App\Models\core2\OperatingRoomBooking;
use App\Models\core2\NutritionalAssessment;
use App\Models\user\Core\core1\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SurgeryDietSyncApiController extends Controller
{
    /**
     * Receive a surgery or diet order from Core 1.
     */
    public function receiveOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:surgery,diet',
            'core1_order_id' => 'required|integer',
            'encounter_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'doctor_id' => 'required|integer',
            'details' => 'required|array',
        ]);

        try {
            if ($validated['type'] === 'surgery') {
                $order = OperatingRoomBooking::create([
                    'operating_booking_id' => 'OR-' . strtoupper(uniqid()),
                    'patient_id' => $validated['patient_id'],
                    'booking_date' => $validated['details']['booking_date'] ?? null,
                    'surgeon_id' => $validated['details']['surgeon_id'] ?? null,
                    'core1_surgery_order_id' => $validated['core1_order_id'],
                    'encounter_id' => $validated['encounter_id'],
                    'proposed_date' => $validated['details']['proposed_date'] ?? null,
                    'proposed_time' => $validated['details']['proposed_time'] ?? null,
                    'status' => 'Received',
                ]);
            } else {
                $order = NutritionalAssessment::create([
                    'enrollment_id' => 'NUT-' . strtoupper(uniqid()),
                    'patient_id' => $validated['patient_id'],
                    'enrollment_status' => 'Pending',
                    'core1_diet_order_id' => $validated['core1_order_id'],
                    'encounter_id' => $validated['encounter_id'],
                ]);
            }

            return response()->json([
                'success' => true,
                'core2_id' => $order->id,
                'message' => ucfirst($validated['type']) . ' order received by Core 2.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('SurgeryDietSync receiveOrder failed: ' . $e->getMessage(), [
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process order in Core 2.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync results (surgery summary or meal delivery) back to Core 1.
     */
    public function sendResult(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:surgery,diet',
            'core1_order_id' => 'required|integer',
            'core2_fulfillment_id' => 'required|integer',
            'result_data' => 'required|array',
        ]);

        try {
            if ($validated['type'] === 'surgery') {
                $order = SurgeryOrder::findOrFail($validated['core1_order_id']);
                $order->update([
                    'status' => 'Completed',
                    'core2_order_id' => $validated['core2_fulfillment_id'],
                ]);
                
                // Add to medical records
                MedicalRecord::create([
                    'patient_id' => $order->patient_id,
                    'doctor_id' => $order->doctor_id,
                    'record_type' => 'surgery',
                    'diagnosis' => $order->procedure_name,
                    'notes' => json_encode($validated['result_data']),
                    'record_date' => now(),
                ]);
            } else {
                $order = DietOrder::findOrFail($validated['core1_order_id']);
                $order->update([
                    'status' => 'Completed',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' result synced successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('SurgeryDietSync sendResult failed: ' . $e->getMessage(), [
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync result to Core 1.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check order status.
     */
    public function checkStatus(Request $request, int $id): JsonResponse
    {
        $type = $request->query('type');
        
        if ($type === 'surgery') {
            $order = OperatingRoomBooking::where('core1_surgery_order_id', $id)->first();
        } else {
            $order = NutritionalAssessment::where('core1_diet_order_id', $id)->first();
        }

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $order->status ?? $order->enrollment_status,
        ]);
    }
}
