<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\Patient;
use App\Models\core1\User;
use Illuminate\Http\Request;

class PatientManagementController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->get('search', '');
        $statusFilter = $request->get('status', '');

        $user = auth()->user();
        $isDoctor = $user->role === 'doctor';
        $isNurse = $user->role === 'nurse';

       $query = Patient::query();

if ($isDoctor) {
    $query->whereHas('appointments', function ($q) use ($user) {
        $q->where('doctor_id', $user->id)
          ->whereIn('status', ['scheduled','accepted','waiting','in_consultation','consulted']);
    });
}

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('patient_id', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $patients = $query->latest()->paginate(15);
        // Nurses for assignment (Head Nurse/Admin only)
        $nurses = [];
        if (auth()->user()->isAdmin() || auth()->user()->isHeadNurse()) {
            $nurses = User::where('role', 'nurse')->get();
        }
        // Stats
        if ($isDoctor) {
            // Get IDs of patients who have relevant appointments with this doctor
            $patientIds = Patient::whereHas('appointments', function ($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->whereIn('status', ['scheduled','accepted','waiting','in_consultation','consulted']);
            })->pluck('id');
        } elseif ($isNurse && !$user->isHeadNurse()) {
            // Nurse sees ALL patients in stats, even if not assigned
            $patientIds = Patient::pluck('id');
        } else {
            // Admin/Head Nurse sees all patients
            $patientIds = Patient::pluck('id');
        }
        
        $stats = [
            'total' => $patientIds->count(),
            'active' => Patient::whereIn('id', $patientIds)->where('status', 'active')->count(),
            'new_today' => Patient::whereIn('id', $patientIds)->whereDate('created_at', today())->count(),
            'new_this_month' => Patient::whereIn('id', $patientIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
        
        return view('core.core1.patients.index', compact(
            'patients',
            'searchTerm',
            'statusFilter',
            'stats',
            'nurses'
        ));
    }

    public function move(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'care_type' => 'required|in:inpatient,outpatient',
            'admission_date' => 'required|date',
            'doctor_id' => 'required|exists:users_core1,id',
            'reason' => 'nullable|string',
        ]);

        $patient->update($data);

        return $data['care_type'] === 'inpatient'
            ? redirect()->route('core1.inpatient.index')->with('success', 'Patient admitted successfully.')
            : redirect()->route('core1.outpatient.index')->with('success', 'Patient moved to outpatient care.');
    }

    public function updateStatus(Request $request, Patient $patient)
    {
        $request->validate([
            'status' => 'required|in:scheduled,waiting,in consultation,consulted'
        ]);

        $patient->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status updated.');
    }

    public function create()
    {
        return view('core.core1.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other,Male,Female,Other',
            'phone' => 'required|string',
            'email' => 'required|email|unique:patients_core1,email',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'policy_number' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
        ]);

        $validated['gender'] = strtolower($validated['gender']);

        $year = date('Y');

        $lastNumber = Patient::where('patient_id', 'like', "HMS-{$year}-%")
            ->selectRaw("MAX(CAST(SUBSTRING(patient_id, 10, 5) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        $validated['patient_id'] = 'HMS-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';
        $validated['last_visit'] = now();

        Patient::create($validated);

        return redirect()->route('core1.patients.index')->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        return view('core.core1.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('core.core1.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other,Male,Female,Other',
            'phone' => 'required|string',
            'email' => 'required|email|unique:patients_core1,email,' . $patient->id,
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'insurance_provider' => 'nullable|string|max:255',
            'policy_number' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
        ]);

        $validated['gender'] = strtolower($validated['gender']);

        $patient->update($validated);

        return redirect()->route('core1.patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('core1.patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function assignNurse(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:users_core1,id',
        ]);

        $patient->update([
            'assigned_nurse_id' => $validated['nurse_id']
        ]);

        return back()->with('success', 'Nurse assigned to patient successfully.');
    }
}
