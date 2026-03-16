<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\TestOrder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class LaboratoryController extends Controller
{
    // ── Test Ordering & Registration ────────────────────────────────────────────
    // Shows ONLY "Received" orders — the intake queue for incoming lab requests.

    public function testOrdersIndex(Request $request)
    {
        $records = TestOrder::where('status', 'Received')
            ->latest()
            ->paginate(15);

        return view('core.core2.laboratory.test-orders.index', compact('records'));
    }

    public function testOrdersCreate()
    {
        return view('core.core2.laboratory.test-orders.create');
    }

    public function testOrdersStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id'     => 'required|string|max:50',
            'patient_id'   => 'nullable|integer',
            'test_id'      => 'nullable|string|max:50',
            'date_ordered' => 'nullable|date',
        ]);

        TestOrder::create($validated);

        return redirect()->route('core2.laboratory.test-orders.index')
            ->with('success', 'Test order record added successfully.');
    }

    /**
     * Collect sample — transitions order from Received → SampleCollected.
     * Generates a barcode and captures collection timestamp + technician name.
     */
    public function collectSample(Request $request, int $id): RedirectResponse
    {
        $order = TestOrder::findOrFail($id);

        if ($order->status !== 'Received') {
            return back()->with('error', 'Sample can only be collected from a Received order.');
        }

        $barcode = 'SMP-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);

        $order->update([
            'status'              => 'SampleCollected',
            'sample_barcode'      => $barcode,
            'sample_collected_at' => now(),
            'sample_collected_by' => auth()->user()->name ?? 'Lab Technician',
        ]);

        return back()->with('success', "Sample collected. Barcode: {$barcode}");
    }

    // ── Sample Tracking & LIS Integration ──────────────────────────────────────
    // Shows orders in SampleCollected or Processing status — the active bench queue.

    public function sampleTrackingIndex(Request $request)
    {
        $records = TestOrder::whereIn('status', ['SampleCollected', 'Processing'])
            ->latest()
            ->paginate(15);

        return view('core.core2.laboratory.sample-tracking.index', compact('records'));
    }

    /**
     * Start processing — transitions order from SampleCollected → Processing.
     */
    public function startProcessing(Request $request, int $id): RedirectResponse
    {
        $order = TestOrder::findOrFail($id);

        if ($order->status !== 'SampleCollected') {
            return back()->with('error', 'Only collected samples can be set to Processing.');
        }

        $order->update([
            'status'               => 'Processing',
            'processing_started_at' => now(),
        ]);

        return back()->with('success', 'Sample is now being processed.');
    }

    // ── Result Entry & Validation ───────────────────────────────────────────────
    // Shows orders in Processing, ResultReady, Validated, or Sent status.

    public function resultValidationIndex(Request $request)
    {
        $records = TestOrder::whereIn('status', ['Processing', 'ResultReady', 'Validated', 'Sent'])
            ->latest()
            ->paginate(15);

        return view('core.core2.laboratory.result-validation.index', compact('records'));
    }

    /**
     * Enter result data for a test order (Processing → ResultReady).
     */
    public function enterResult(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'result_data' => 'required|string',
        ]);

        $order = TestOrder::findOrFail($id);
        $order->update([
            'result_data' => $validated['result_data'],
            'status'      => 'ResultReady',
        ]);

        return back()->with('success', 'Result saved. Ready for validation.');
    }

    /**
     * Validate result and send back to Core 1 Diagnostic Orders via direct DB write.
     */
    public function validateAndSend(int $id): RedirectResponse
    {
        $order = TestOrder::findOrFail($id);

        if (!$order->result_data) {
            return back()->with('error', 'Cannot validate — no result data entered.');
        }

        if (!$order->core1_lab_order_id) {
            return back()->with('error', 'Cannot send — this order was not synced from Core 1.');
        }

        $order->update([
            'status'            => 'Validated',
            'validated_by_name' => auth()->user()->name ?? 'Lab Technician',
        ]);

        // Send result back to Core 1 Diagnostic Orders via direct DB write
        try {
            $labOrder = \App\Models\core1\LabOrder::findOrFail($order->core1_lab_order_id);

            $resultData = $order->result_data;
            if (is_string($resultData)) {
                $decoded = json_decode($resultData, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $resultData = $decoded;
                }
            }

            $labOrder->update([
                'result_data'        => $resultData,
                'sync_status'        => 'ResultReceived',
                'result_received_at' => now(),
            ]);

            $order->update([
                'status'         => 'Sent',
                'result_sent_at' => now(),
            ]);

            return back()->with('success', 'Result validated and sent to Core 1 Diagnostic Orders.');
        } catch (\Exception $e) {
            Log::error('LabSync validateAndSend failed: ' . $e->getMessage());

            return back()->with('error', 'Result validated but sync failed: ' . $e->getMessage());
        }
    }
}
