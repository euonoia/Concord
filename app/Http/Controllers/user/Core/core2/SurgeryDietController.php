<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\OperatingRoomBooking;
use App\Models\core2\NutritionalAssessment;
use App\Models\core2\UtilizationReporting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SurgeryDietController extends Controller
{
    // ── Operating Room Booking ──────────────────────────────────────────────────

    public function orBookingIndex(Request $request)
    {
        $records = OperatingRoomBooking::latest()->paginate(15);
        return view('core.core2.surgery-diet.or-booking.index', compact('records'));
    }

    public function orBookingCreate()
    {
        return view('core.core2.surgery-diet.or-booking.create');
    }

    public function orBookingStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'operating_booking_id' => 'required|string|max:50',
            'patient_id'           => 'nullable|integer',
            'booking_date'         => 'nullable|date',
            'surgeon_id'           => 'nullable|integer',
        ]);

        OperatingRoomBooking::create($validated);

        return redirect()->route('core2.surgery-diet.or-booking.index')
            ->with('success', 'OR booking record added successfully.');
    }

    // ── Nutritional Assessment & Consultation ───────────────────────────────────

    public function nutritionalIndex(Request $request)
    {
        $records = NutritionalAssessment::latest()->paginate(15);
        return view('core.core2.surgery-diet.nutritional.index', compact('records'));
    }

    public function nutritionalCreate()
    {
        return view('core.core2.surgery-diet.nutritional.create');
    }

    public function nutritionalStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enrollment_id'    => 'required|string|max:50',
            'patient_id'       => 'nullable|integer',
            'package_id'       => 'nullable|string|max:50',
            'enrollment_status'=> 'nullable|string|max:50',
        ]);

        NutritionalAssessment::create($validated);

        return redirect()->route('core2.surgery-diet.nutritional.index')
            ->with('success', 'Nutritional assessment record added successfully.');
    }

    // ── Utilization Reporting ───────────────────────────────────────────────────

    public function utilizationIndex(Request $request)
    {
        $records = UtilizationReporting::latest()->paginate(15);
        return view('core.core2.surgery-diet.utilization.index', compact('records'));
    }

    public function utilizationCreate()
    {
        return view('core.core2.surgery-diet.utilization.create');
    }

    public function utilizationStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_id'        => 'required|string|max:50',
            'module_name'      => 'nullable|string|max:100',
            'usage_metrics'    => 'nullable|string|max:100',
            'reporting_period' => 'nullable|date',
        ]);

        UtilizationReporting::create($validated);

        return redirect()->route('core2.surgery-diet.utilization.index')
            ->with('success', 'Utilization report added successfully.');
    }
}
