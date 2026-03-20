<?php

namespace App\Services\core1;

use App\Models\core1\Admission;
use App\Models\core1\Encounter;
use App\Models\core1\Bed;
use App\Models\core2\RoomAssignment;
use App\Models\core2\BedStatusAllocation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdmissionSyncService
{
    /**
     * Queue a patient for room assignment in Core 2.
     * Called when "Send to Admission" or "Recommend Admission" is triggered.
     */
    public function queueForRoomAssignment(Encounter $encounter): RoomAssignment
    {
        $encounter->loadMissing(['patient', 'triage']);

        $triageSummary = null;
        $triageLevel = null;

        if ($encounter->triage) {
            $t = $encounter->triage;
            $triageSummary = "BP: {$t->blood_pressure}, HR: {$t->heart_rate}, Temp: {$t->temperature}, SpO2: {$t->spo2}";
            $triageLevel = $t->triage_level;
        }

        $assignmentId = 'RA-' . strtoupper(uniqid());

        return RoomAssignment::create([
            'assignment_id'  => $assignmentId,
            'patient_id'     => $encounter->patient_id,
            'patient_name'   => $encounter->patient->name ?? 'Unknown',
            'mrn'            => $encounter->patient->mrn ?? null,
            'encounter_id'   => $encounter->id,
            'triage_summary' => $triageSummary,
            'triage_level'   => $triageLevel,
            'status'         => 'Pending',
            'date_assigned'  => Carbon::now()->toDateString(),
        ]);
    }
    
    /**
     * Queue a patient for transfer in Core 2.
     * Called when a clinician requests a transfer in Core 1.
     */
    public function queueTransferRequest(Admission $admission, ?int $targetBedId = null): RoomAssignment
    {
        $admission->loadMissing(['encounter.patient', 'bed']);
        $encounter = $admission->encounter;

        $assignmentId = 'TR-' . strtoupper(uniqid());

        return RoomAssignment::create([
            'assignment_id'  => $assignmentId,
            'patient_id'     => $encounter->patient_id,
            'patient_name'   => $encounter->patient->name ?? 'Unknown',
            'mrn'            => $encounter->patient->mrn ?? null,
            'encounter_id'   => $encounter->id,
            'source_bed_id'  => $admission->bed_id,
            'bed_id_core1'   => $targetBedId, // The requested target bed
            'request_type'   => 'Transfer',
            'status'         => 'Pending',
            'date_assigned'  => Carbon::now()->toDateString(),
        ]);
    }

    /**
     * Sync an admission event to Core 2 after bed allocation.
     * Called after AdmissionService::admit() completes.
     */
    public function syncAdmissionToCore2(Encounter $encounter, Bed $bed, Admission $admission): void
    {
        $bed->loadMissing('room.ward');

        $wardName  = $bed->room->ward->name ?? 'Unknown Ward';
        $roomLabel = $bed->room->room_number ?? '';
        $bedLabel  = $bed->bed_number ?? '';

        // Update the pending RoomAssignment to Assigned
        $roomAssignment = RoomAssignment::where('encounter_id', $encounter->id)
            ->where('status', 'Pending')
            ->first();

        if ($roomAssignment) {
            $roomAssignment->update([
                'bed_id_core1' => $bed->id,
                'ward_name'    => $wardName,
                'bed_number'   => $bedLabel,
                'room'         => "Ward: {$wardName} | Room: {$roomLabel} | Bed: {$bedLabel}",
                'status'       => 'Assigned',
            ]);
        }

        // Create or update Bed Status Allocation in Core 2
        BedStatusAllocation::updateOrCreate(
            [
                'bed_id_core1'  => $bed->id,
            ],
            [
                'bed_id'       => 'BED-' . $bed->id,
                'room_id'      => 'ROOM-' . ($bed->room_id ?? '0'),
                'status'       => 'Occupied',
                'patient_id'   => $encounter->patient_id,
                'encounter_id' => $encounter->id,
            ]
        );
    }

    /**
     * Sync a discharge event to Core 2.
     * Releases bed and updates room assignment status.
     */
    public function syncDischargeToCore2(Admission $admission): void
    {
        $admission->loadMissing(['encounter', 'bed']);

        // Update Room Assignment to Discharged
        RoomAssignment::where('encounter_id', $admission->encounter_id)
            ->where('status', 'Assigned')
            ->update(['status' => 'Discharged']);

        // Update Bed Status Allocation to Available
        BedStatusAllocation::where('bed_id_core1', $admission->bed_id)
            ->update([
                'status'       => 'Available',
                'patient_id'   => null,
                'encounter_id' => null,
            ]);
    }

    /**
     * Sync a transfer event to Core 2.
     * Updates old bed to Available and new bed to Occupied.
     */
    public function syncTransferToCore2(Admission $admission, Bed $oldBed, Bed $newBed): void
    {
        $admission->loadMissing('encounter.patient');
        
        // 1. Release Old Bed in Core 2
        BedStatusAllocation::where('bed_id_core1', $oldBed->id)
            ->update([
                'status'       => 'Available',
                'patient_id'   => null,
                'encounter_id' => null,
            ]);

        // 2. Occupy New Bed in Core 2
        BedStatusAllocation::updateOrCreate(
            ['bed_id_core1' => $newBed->id],
            [
                'bed_id'       => 'BED-' . $newBed->id,
                'room_id'      => 'ROOM-' . ($newBed->room_id ?? '0'),
                'status'       => 'Occupied',
                'patient_id'   => $admission->encounter->patient_id,
                'encounter_id' => $admission->encounter_id,
            ]
        );

        // 3. Log the transfer in Core 2 history
        \App\Models\core2\PatientTransferManagement::create([
            'transfer_id'   => 'TRF-' . strtoupper(uniqid()),
            'patient_id'    => $admission->encounter->patient_id,
            'encounter_id'  => $admission->encounter_id,
            'from_location' => "Bed: {$oldBed->bed_number} (Room: " . ($oldBed->room->room_number ?? 'N/A') . ")",
            'to_location'   => "Bed: {$newBed->bed_number} (Room: " . ($newBed->room->room_number ?? 'N/A') . ")",
            'transfer_date' => Carbon::now()->toDateString(),
        ]);
    }
}
