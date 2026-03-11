<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\TestOrder;
use App\Models\core2\SampleTracking;
use App\Models\core2\ResultValidation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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
