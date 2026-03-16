<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\core1\Prescription;
use App\Models\core2\Prescription as PharmacyOrder;
use App\Models\core2\DrugInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PharmacySyncApiController extends Controller
{
    /**
     * Notify Core 1 that a medication has been dispensed in Core 2.
     */
    public function dispense(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'core1_prescription_id' => 'required|integer',
            'core2_pharmacy_id'     => 'required|integer',
            'pharmacist_id'         => 'nullable|integer',
            'dispensed_at'          => 'nullable|date',
            'status'                => 'required|string|in:Dispensed,Cancelled',
        ]);

        try {
            // Update Core 2 Order
            $pharmacyOrder = PharmacyOrder::findOrFail($validated['core2_pharmacy_id']);
            $pharmacyOrder->update([
                'status'        => $validated['status'],
                'pharmacist_id' => $validated['pharmacist_id'],
                'dispensed_at'  => $validated['dispensed_at'] ?? now(),
            ]);

            // Sync status back to Core 1 Prescription
            $prescription = Prescription::findOrFail($validated['core1_prescription_id']);
            $prescription->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pharmacy status synced to Core 1 successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('PharmacySync dispense failed: ' . $e->getMessage(), [
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync pharmacy status to Core 1.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check the status of a prescription in Core 2.
     */
    public function checkStatus(int $id): JsonResponse
    {
        $pharmacyOrder = PharmacyOrder::where('core1_prescription_id', $id)->first();

        if (!$pharmacyOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Prescription not found in Core 2 Pharmacy.',
            ], 404);
        }

        return response()->json([
            'success'   => true,
            'status'    => $pharmacyOrder->status,
            'dispensed_at' => $pharmacyOrder->dispensed_at?->toDateTimeString(),
        ]);
    }

    /**
     * Search drug inventory in Core 2.
     */
    public function searchDrugs(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([]);
        }

        $drugs = DrugInventory::where('drug_name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['drug_name', 'drug_num', 'quantity']);

        return response()->json($drugs);
    }
}
