<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\Admission;
use App\Models\core1\Bed;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InpatientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isDoctor = $user->role_slug === 'doctor';
        $isNurse = $user->role_slug === 'nurse';

        // Fetch real active admissions following HIS Architect rules
        $admissionsQuery = Admission::with(['encounter.patient', 'encounter.doctor', 'bed.room.ward'])
            ->where('status', 'Admitted');

        // Admin, Head Nurse, Receptionist, Doctors can typically see the ward.
        // Let's allow everyone to see active admissions for now to avoid the silent empty list failure.

        $activeAdmissions = $admissionsQuery->latest()->get();

        // Stats derived from real production data
        $stats = [
            'current_inpatients' => $activeAdmissions->count(),
            'occupied' => Bed::where('status', 'Occupied')->count(),
            'discharges_today' => Admission::where('status', 'Discharged')
                ->whereDate('discharge_date', Carbon::today())
                ->count(),
        ];

        // Fetch actual Bed map
        $beds = Bed::with(['room.ward', 'admissions' => function($q) {
            $q->where('status', 'Admitted')->with('encounter.patient');
        }])->get();

        // Map to UI format
        $uiBeds = $beds->map(function($bed) {
            $activeAdmission = $bed->admissions->first();
            return [
                'id' => 'Bed ' . $bed->bed_number,
                'ward' => $bed->room->ward->name,
                'room' => $bed->room->room_number,
                'type' => $bed->room->room_type,
                'status' => strtolower($bed->status),
                'bg' => $bed->status === 'Available' ? 'core1-bed-available' : ($bed->status === 'Occupied' ? 'core1-bed-occupied' : 'core1-bed-cleaning'),
                'patient' => $activeAdmission ? $activeAdmission->encounter->patient->name : '',
                'patient_id' => $activeAdmission ? $activeAdmission->encounter->patient->mrn : '',
            ];
        });

        // Nurses for dropdown (Head Nurse/Admin only)
        $nurses = [];
        if ($user->role_slug === 'admin' || $user->role_slug === 'head_nurse') {
            $nurses = User::where('role_slug', 'nurse')->get();
        }

        return view('core.core1.inpatient.index', [
            'inpatients' => $activeAdmissions, // Passed as 'inpatients' for backward compatibility in view loop names
            'stats' => $stats,
            'beds' => $uiBeds,
            'nurses' => $nurses
        ]);
    }

    public function deactivate(Patient $patient)
{
    $newStatus = $patient->status === 'inactive' ? 'active' : 'inactive';

    $patient->update([
        'status' => $newStatus
    ]);

    return back()->with('success', 'Patient status updated successfully.');
}

}
