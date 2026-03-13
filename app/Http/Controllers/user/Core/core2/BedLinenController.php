<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\RoomAssignment;
use App\Models\core2\BedStatusAllocation;
use App\Models\core2\PatientTransferManagement;
use App\Models\core2\HouseKeepingStatus;
use App\Models\core1\Bed;
use App\Models\core1\Ward;
use App\Models\core1\Encounter;
use App\Services\core1\AdmissionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BedLinenController extends Controller
{
    // ── Pending Admissions Queue ─────────────────────────────────────────────

    public function pendingAdmissionsIndex(Request $request)
    {
        $records = RoomAssignment::where('status', 'Pending')
            ->latest()
            ->paginate(15);

        // Also show assigned (recent) for context
        $assigned = RoomAssignment::where('status', 'Assigned')
            ->latest()
            ->limit(10)
            ->get();

        return view('core.core2.bed-linen.pending-admissions.index', compact('records', 'assigned'));
    }

    /**
     * JSON endpoint returning all beds with current status for the 2D floor map.
     */
    public function floorMapData(Request $request)
    {
        $wards = Ward::with(['rooms.beds.admissions' => function($query) {
            $query->where('status', 'Admitted')->with('encounter.patient');
        }])->get();

        $floors = [];

        foreach ($wards as $ward) {
            $wardData = [
                'id'    => $ward->id,
                'name'  => $ward->name,
                'type'  => $ward->ward_type ?? 'WARD',
                'rooms' => [],
            ];

            foreach ($ward->rooms as $room) {
                $roomData = [
                    'id'          => $room->id,
                    'room_number' => $room->room_number,
                    'room_type'   => $room->room_type,
                    'beds'        => [],
                ];

                foreach ($room->beds as $bed) {
                    $activeAdmission = $bed->admissions->first();
                    $patientName = null;
                    $mrn = null;
                    
                    if ($activeAdmission && $activeAdmission->encounter && $activeAdmission->encounter->patient) {
                        $patientName = $activeAdmission->encounter->patient->name ?? null;
                        if (!$patientName && isset($activeAdmission->encounter->patient->first_name)) {
                             $patientName = $activeAdmission->encounter->patient->first_name . ' ' . $activeAdmission->encounter->patient->last_name;
                        }
                        $mrn = $activeAdmission->encounter->patient->mrn ?? null;
                    }

                    $roomData['beds'][] = [
                        'id'           => $bed->id,
                        'bed_number'   => $bed->bed_number,
                        'status'       => $bed->status,
                        'patient_name' => $patientName,
                        'mrn'          => $mrn,
                    ];
                }

                $wardData['rooms'][] = $roomData;
            }

            $floors[] = $wardData;
        }

        return response()->json($floors);
    }

    /**
     * Allocate a bed from the 2D picker for a pending admission.
     * Bridges Core 1 AdmissionService and Core 2 sync.
     */
    public function allocateBed(Request $request)
    {
        $validated = $request->validate([
            'room_assignment_id' => 'required|exists:room_assignments_core2,id',
            'bed_id'             => 'required|exists:beds_core1,id',
        ]);

        try {
            $roomAssignment = RoomAssignment::findOrFail($validated['room_assignment_id']);

            if ($roomAssignment->status !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This admission request has already been processed.'
                ], 400);
            }

            $encounter = Encounter::findOrFail($roomAssignment->encounter_id);
            $bed = Bed::findOrFail($validated['bed_id']);

            // Use AdmissionService to perform the admission (which also syncs to Core 2)
            $admissionService = app(AdmissionService::class);
            $admissionService->admit($encounter, $bed);

            return response()->json([
                'success' => true,
                'message' => 'Patient successfully admitted and bed allocated.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bed allocation failed: ' . $e->getMessage()
            ], 400);
        }
    }

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

    /**
     * Update bed status from the 2D floor map.
     */
    public function updateBedStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Available,Cleaning,Maintenance,Occupied',
        ]);

        try {
            $bed = Bed::findOrFail($id);

            // Guardrail: DO NOT allow manual transition to "Occupied" if it violates architecture.
            // If they try to set it to Occupied from here, they should be advised to use the Admission flow.
            if ($validated['status'] === 'Occupied' && $bed->status !== 'Occupied') {
                 return response()->json([
                    'success' => false,
                    'message' => 'Beds cannot be manually set to Occupied. Please use the Patient Admission workflow to assign a bed.'
                ], 403);
            }

            // Check if bed is currently occupied. If so, and we are trying to set it to something else
            // we probably shouldn't allow it without a discharge, but we will allow transitioning 
            // from Cleaning/Maintenance back to Available.
            if ($bed->status === 'Occupied' && $validated['status'] !== 'Occupied') {
                 return response()->json([
                    'success' => false,
                    'message' => 'Cannot change status of an Occupied bed directly. The patient must be transferred or discharged first.'
                ], 403);
            }

            $bed->status = $validated['status'];
            $bed->save();

            // Log the change in Core 2 history (optional but good for tracking)
            BedStatusAllocation::create([
                'bed_id' => $bed->id,
                'room_id' => $bed->room_id,
                'status' => $validated['status'],
                'patient_id' => null, // If we were tracking this better we might lookup the active admission
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bed status updated to ' . $validated['status'],
                'new_status' => $bed->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bed status: ' . $e->getMessage()
            ], 500);
        }
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
