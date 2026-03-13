<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\core1\LabOrder;
use App\Models\core2\TestOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabSyncApiController extends Controller
{
    /**
     * Receive a lab order from Core 1 and create a TestOrder in Core 2.
     */
    public function receiveOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'core1_lab_order_id' => 'required|integer',
            'encounter_id' => 'required|integer',
            'patient_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
            'test_name' => 'required|string|max:255',
            'clinical_note' => 'nullable|string',
            'patient_name' => 'nullable|string|max:255',
            'patient_mrn' => 'nullable|string|max:50',
            'ordering_doctor' => 'nullable|string|max:255',
            'priority' => 'nullable|in:Routine,Urgent,STAT',
            'date_ordered' => 'nullable|date',
        ]);

        try {
            $testOrder = TestOrder::create([
                'order_id' => 'LAB-' . str_pad($validated['core1_lab_order_id'], 6, '0', STR_PAD_LEFT),
                'core1_lab_order_id' => $validated['core1_lab_order_id'],
                'encounter_id' => $validated['encounter_id'],
                'patient_id' => $validated['patient_id'] ?? null,
                'test_id' => $validated['test_name'],
                'test_name' => $validated['test_name'],
                'clinical_note' => $validated['clinical_note'] ?? null,
                'patient_name' => $validated['patient_name'] ?? null,
                'patient_mrn' => $validated['patient_mrn'] ?? null,
                'ordering_doctor' => $validated['ordering_doctor'] ?? null,
                'priority' => $validated['priority'] ?? 'Routine',
                'date_ordered' => $validated['date_ordered'] ?? now()->toDateString(),
                'status' => 'Received',
            ]);

            return response()->json([
                'success' => true,
                'core2_order_id' => $testOrder->id,
                'order_id' => $testOrder->order_id,
                'message' => 'Lab order received by Core 2 Laboratory.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('LabSync receiveOrder failed: ' . $e->getMessage(), [
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create test order in Core 2.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Receive lab results from Core 2 and update the LabOrder in Core 1.
     */
    public function sendResult(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'core1_lab_order_id' => 'required|integer',
            'core2_order_id' => 'required|integer',
            'result_data' => 'required|string',
            'validated_by' => 'nullable|string|max:255',
        ]);

        try {
            $labOrder = LabOrder::findOrFail($validated['core1_lab_order_id']);

            $labOrder->update([
                'result_data' => $validated['result_data'],
                'sync_status' => 'ResultReceived',
                'result_received_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lab results synced to Core 1 successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('LabSync sendResult failed: ' . $e->getMessage(), [
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync results to Core 1.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check the status of a lab order in Core 2.
     */
    public function checkStatus(int $id): JsonResponse
    {
        $testOrder = TestOrder::where('core1_lab_order_id', $id)->first();

        if (!$testOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found in Core 2.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order_id' => $testOrder->order_id,
            'status' => $testOrder->status,
            'result_data' => $testOrder->result_data,
            'updated_at' => $testOrder->updated_at?->toDateTimeString(),
        ]);
    }
}
