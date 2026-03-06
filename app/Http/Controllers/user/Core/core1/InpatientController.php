<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InpatientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isDoctor = $user->role_slug === 'doctor';
        $isNurse = $user->role_slug === 'nurse';

        // Query inpatients
        $inpatients = Patient::query()
            ->where('care_type', 'inpatient')
            ->when($isDoctor, fn($q) => $q->where('doctor_id', $user->id))
            ->when(!$isDoctor && !$isNurse, fn($q) => $q)
            ->latest()
            ->get();

        // Stats
        $stats = [
            'current_inpatients' => $inpatients->count(),
            'occupied' => $inpatients->count(),
            'discharges_today' => Patient::where('care_type', 'inpatient')
                                        ->when($isNurse, fn($q) => $q->where('assigned_nurse_id', $user->id))
                                        ->whereDate('last_visit', today())
                                        ->count(),
        ];

        // Beds placeholder
        $beds = [];
        for ($i = 1; $i <= 10; $i++) {
            $beds[] = [
                'id' => 'Bed ' . $i,
                'type' => 'General',
                'status' => 'available',
                'bg' => 'core1-bed-available',
                'patient' => '',
                'patient_id' => '',
            ];
        }

        // Nurses for dropdown
        $nurses = [];
        if ($user->role_slug === 'admin' || $user->role_slug === 'head_nurse') {
            $nurses = User::where('role_slug', 'nurse')->get();
        }

        return view('core.core1.inpatient.index', compact('inpatients', 'stats', 'beds', 'nurses'));
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
