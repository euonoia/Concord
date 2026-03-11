<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\RoomAssignment;
use App\Models\core2\BedStatusAllocation;
use App\Models\core2\PatientTransferManagement;
use App\Models\core2\HouseKeepingStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class BedLinenController extends Controller
{
    // ── Room Assignment ─────────────────────────────────────────────────────────

    public function roomAssignmentIndex(Request $request)
    {
        $records = RoomAssignment::latest()->paginate(15);
        return view('core.core2.bed-linen.room-assignment.index', compact('records'));
    }

    public function roomAssignmentCreate()
    {
        return view('core.core2.bed-linen.room-assignment.create');
    }

    public function roomAssignmentStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => 'required|string|max:50',
            'patient_id'    => 'nullable|integer',
            'room'          => 'nullable|string|max:50',
            'date_assigned' => 'nullable|date',
        ]);

        RoomAssignment::create($validated);

        return redirect()->route('core2.bed-linen.room-assignment.index')
            ->with('success', 'Room assignment record added successfully.');
    }

    // ── Bed Status & Allocation ─────────────────────────────────────────────────

    public function bedStatusIndex(Request $request)
    {
        $records = BedStatusAllocation::latest()->paginate(15);
        return view('core.core2.bed-linen.bed-status.index', compact('records'));
    }

    public function bedStatusCreate()
    {
        return view('core.core2.bed-linen.bed-status.create');
    }

    public function bedStatusStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bed_id'     => 'required|string|max:50',
            'room_id'    => 'nullable|string|max:50',
            'status'     => 'nullable|string|max:50',
            'patient_id' => 'nullable|integer',
        ]);

        BedStatusAllocation::create($validated);

        return redirect()->route('core2.bed-linen.bed-status.index')
            ->with('success', 'Bed status record added successfully.');
    }

    // ── Patient Transfer Management ─────────────────────────────────────────────

    public function patientTransferIndex(Request $request)
    {
        $records = PatientTransferManagement::latest()->paginate(15);
        return view('core.core2.bed-linen.patient-transfer.index', compact('records'));
    }

    public function patientTransferCreate()
    {
        return view('core.core2.bed-linen.patient-transfer.create');
    }

    public function patientTransferStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transfer_id'   => 'required|string|max:50',
            'patient_id'    => 'nullable|integer',
            'from_location' => 'nullable|string|max:100',
            'to_location'   => 'nullable|string|max:100',
            'transfer_date' => 'nullable|date',
        ]);

        PatientTransferManagement::create($validated);

        return redirect()->route('core2.bed-linen.patient-transfer.index')
            ->with('success', 'Patient transfer record added successfully.');
    }

    // ── House Keeping & Cleaning Status ────────────────────────────────────────

    public function houseKeepingIndex(Request $request)
    {
        $records = HouseKeepingStatus::latest()->paginate(15);
        return view('core.core2.bed-linen.house-keeping.index', compact('records'));
    }

    public function houseKeepingCreate()
    {
        return view('core.core2.bed-linen.house-keeping.create');
    }

    public function houseKeepingStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'house_keeping_id' => 'required|string|max:50',
            'room_id'          => 'nullable|string|max:50',
            'bed_id'           => 'nullable|string|max:50',
            'status'           => 'nullable|string|max:50',
            'last_cleaned_date'=> 'nullable|date_format:Y-m-d H:i:s',
        ]);

        HouseKeepingStatus::create($validated);

        return redirect()->route('core2.bed-linen.house-keeping.index')
            ->with('success', 'Housekeeping status record added successfully.');
    }
}
