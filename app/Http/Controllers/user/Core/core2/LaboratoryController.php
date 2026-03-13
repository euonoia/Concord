<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\TestOrder;
use App\Models\core2\SampleTracking;
use App\Models\core2\ResultValidation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LaboratoryController extends Controller
{
    // ── Test Ordering & Registration ────────────────────────────────────────────

    public function testOrdersIndex(Request $request)
    {
        $records = TestOrder::latest()->paginate(15);
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
     * Update the status of a test order (lab workflow progression).
     */
    public function updateOrderStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Received,SampleCollected,Processing,ResultReady,Validated,Sent',
        ]);

        $order = TestOrder::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return back()->with('success', "Order status updated to {$validated['status']}.");
    }

    /**
     * Enter result data for a test order.
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
     * Validate result and send back to Core 1 via API.
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

        // Send result back to Core 1 via API
        try {
            $baseUrl = rtrim(config('app.url'), '/');
            $response = Http::timeout(10)->acceptJson()->post("{$baseUrl}/api/lab-sync/result", [
                'core1_lab_order_id' => $order->core1_lab_order_id,
                'core2_order_id'     => $order->id,
                'result_data'        => $order->result_data,
                'validated_by'       => $order->validated_by_name,
            ]);

            if ($response->successful() && $response->json('success')) {
                $order->update([
                    'status'         => 'Sent',
                    'result_sent_at' => now(),
                ]);

                return back()->with('success', 'Result validated and sent to Core 1 Diagnostic Orders.');
            }

            Log::warning('LabSync validateAndSend: Core 1 returned non-success.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return back()->with('error', 'Result validated but failed to sync to Core 1. Check logs.');
        } catch (\Exception $e) {
            Log::error('LabSync validateAndSend failed: ' . $e->getMessage());

            return back()->with('error', 'Result validated but sync failed: ' . $e->getMessage());
        }
    }

    // ── Sample Tracking & LIS Integration ──────────────────────────────────────

    public function sampleTrackingIndex(Request $request)
    {
        $records = SampleTracking::latest()->paginate(15);
        return view('core.core2.laboratory.sample-tracking.index', compact('records'));
    }

    public function sampleTrackingCreate()
    {
        return view('core.core2.laboratory.sample-tracking.create');
    }

    public function sampleTrackingStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sample_id'     => 'required|string|max:50',
            'test_order_id' => 'nullable|string|max:50',
            'status'        => 'nullable|string|max:50',
            'lab_id'        => 'nullable|string|max:50',
        ]);

        SampleTracking::create($validated);

        return redirect()->route('core2.laboratory.sample-tracking.index')
            ->with('success', 'Sample tracking record added successfully.');
    }

    // ── Result Entry & Validation ───────────────────────────────────────────────

    public function resultValidationIndex(Request $request)
    {
        $records = ResultValidation::latest()->paginate(15);
        return view('core.core2.laboratory.result-validation.index', compact('records'));
    }

    public function resultValidationCreate()
    {
        return view('core.core2.laboratory.result-validation.create');
    }

    public function resultValidationStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'result_id'    => 'required|string|max:50',
            'sample_id'    => 'nullable|string|max:50',
            'test_result'  => 'nullable|string',
            'validated_by' => 'nullable|string|max:100',
        ]);

        ResultValidation::create($validated);

        return redirect()->route('core2.laboratory.result-validation.index')
            ->with('success', 'Result validation record added successfully.');
    }
}
