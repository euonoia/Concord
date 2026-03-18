<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\Admission;
use App\Models\core1\Bed;
use App\Models\core1\Ward;
use App\Models\core1\Encounter;
use App\Models\core1\Prescription;
use App\Models\core1\MedicationAdministration;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InpatientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch real active admissions following HIS Architect rules
        $activeAdmissions = Admission::with([
            'encounter.patient.assignedNurse', 
            'encounter.doctor', 
            'encounter.triage', 
            'encounter.triages.creator',
            'encounter.prescriptions',
            'bed.room.ward'
        ])
            ->whereIn('status', ['Admitted', 'Doctor Approved'])
            ->latest()
            ->get();

        // Stats derived from real production data
        $stats = [
            'current_inpatients' => $activeAdmissions->count(),
            'occupied' => Bed::where('status', 'Occupied')->count(),
            'discharges_today' => Admission::where('status', 'Discharged')
                ->whereDate('discharge_date', Carbon::today())
                ->count(),
        ];

        // Flat bed list for legacy tab (kept for backward compatibility)
        $beds = Bed::with(['room.ward', 'admissions' => function ($q) {
            $q->whereIn('status', ['Admitted', 'Doctor Approved'])->with('encounter.patient');
        }])->get();

        $uiBeds = $beds->map(function ($bed) {
            $activeAdmission = $bed->admissions->first();
            return [
                'id'         => 'Bed ' . $bed->bed_number,
                'ward'       => $bed->room->ward->name,
                'room'       => $bed->room->room_number,
                'type'       => $bed->room->room_type,
                'status'     => strtolower($bed->status),
                'bg'         => $bed->status === 'Available'
                    ? 'core1-bed-available'
                    : ($bed->status === 'Occupied' ? 'core1-bed-occupied' : 'core1-bed-cleaning'),
                'patient'    => $activeAdmission ? $activeAdmission->encounter->patient->name : '',
                'patient_id' => $activeAdmission ? $activeAdmission->encounter->patient->mrn  : '',
            ];
        });

        // ── 2D Floor Map: group wards by ward_type ────────────────────────────────
        $floorMap = $this->getFloorMapData();
        // ─────────────────────────────────────────────────────────────────────────

        // Nurses for dropdown (Head Nurse/Admin only)
        $nurses = [];
        if ($user->role_slug === 'admin' || $user->role_slug === 'head_nurse') {
            $nurses = User::where('role_slug', 'nurse')->get();
        }

        return view('core.core1.inpatient.index', [
            'inpatients' => $activeAdmissions,
            'stats'      => $stats,
            'beds'        => $uiBeds,
            'nurses'     => $nurses,
            'floorMap'   => $floorMap,
        ]);
    }

    public function deactivate(Patient $patient)
    {
        $newStatus = $patient->status === 'inactive' ? 'active' : 'inactive';

        $patient->update([
            'status' => $newStatus,
        ]);

        return back()->with('success', 'Patient status updated successfully.');
    }

    /**
     * Generate the 2D Floor Map data structure.
     *
     * @return array
     */
    protected function getFloorMapData(): array
    {
        $zoneOrder = ['ICU', 'ER', 'WARD', 'OR'];
        $wards = Ward::with(['rooms.beds.admissions' => function ($q) {
            $q->whereIn('status', ['Admitted', 'Doctor Approved'])->with(['encounter.patient', 'encounter.triages.creator']);
        }])->get();

        $floorMap = [];
        foreach ($zoneOrder as $zone) {
            $floorMap[$zone] = [
                'wards' => [],
                'total' => 0,
                'occ'   => 0,
                'avail' => 0,
            ];
        }

        foreach ($wards as $ward) {
            $zoneKey = strtoupper(trim($ward->ward_type));
            if (!array_key_exists($zoneKey, $floorMap)) {
                $zoneKey = 'WARD';
            }

            $wardRooms = [];
            foreach ($ward->rooms as $room) {
                $roomBeds = [];
                foreach ($room->beds as $bed) {
                    $admission = $bed->admissions->first();
                    $status = strtolower($bed->status);
                    $roomBeds[] = [
                        'bed_number'   => $bed->bed_number,
                        'status'       => $status,
                        'patient'      => $admission ? optional($admission->encounter->patient)->name : null,
                        'mrn'          => $admission ? optional($admission->encounter->patient)->mrn  : null,
                        'encounter_id' => $admission ? $admission->encounter_id : null,
                        'triage'       => ($admission && $admission->encounter->triage) ? [
                            'bp'   => $admission->encounter->triage->blood_pressure,
                            'hr'   => $admission->encounter->triage->heart_rate,
                            'temp' => $admission->encounter->triage->temperature,
                            'spo2' => $admission->encounter->triage->spo2,
                            'history' => $admission->encounter->triages,
                        ] : null,
                    ];
                    $floorMap[$zoneKey]['total']++;
                    if ($status === 'occupied')  $floorMap[$zoneKey]['occ']++;
                    if ($status === 'available') $floorMap[$zoneKey]['avail']++;
                }
                if (!empty($roomBeds)) {
                    $wardRooms[] = [
                        'room_number' => $room->room_number,
                        'room_type'   => $room->room_type,
                        'beds'        => $roomBeds,
                    ];
                }
            }

            if (!empty($wardRooms)) {
                $floorMap[$zoneKey]['wards'][] = [
                    'name'  => $ward->name,
                    'rooms' => $wardRooms,
                ];
            }
        }

        return $floorMap;
    }

    public function getPrescriptionsJson(Encounter $encounter)
    {
        $prescriptions = $encounter->prescriptions()
            ->latest()
            ->get()
            ->map(function ($rx) {
                return [
                    'id' => $rx->id,
                    'medication' => $rx->medication,
                    'dosage' => $rx->dosage,
                    'instructions' => $rx->instructions,
                    'status' => $rx->status,
                    'administer_url' => route('core1.outpatient.administerMedication', $rx->id)
                ];
            });

        return response()->json($prescriptions);
    }

    public function administerAll(Encounter $encounter)
    {
        $prescriptions = $encounter->prescriptions()->where('status', '!=', 'Administered')->get();

        if ($prescriptions->isEmpty()) {
            return back()->with('info', 'No pending medications to administer.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($prescriptions, $encounter) {
            foreach ($prescriptions as $rx) {
                MedicationAdministration::create([
                    'prescription_id' => $rx->id,
                    'encounter_id'    => $encounter->id,
                    'administered_by' => auth()->id(),
                    'administered_at' => now(),
                    'status'          => 'Administered',
                ]);

                $rx->update(['status' => 'Administered']);
            }
        });

        return back()->with('success', 'All pending medications have been marked as administered.');
    }
}
