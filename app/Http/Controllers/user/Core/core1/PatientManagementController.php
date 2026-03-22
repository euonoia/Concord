<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\Patient;
use App\Models\user\Core\core1\AuditLog;
use App\Models\User;
use App\Http\Requests\core1\Patients\StorePatientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientManagementController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $statusFilter = $request->input('status', '');

        $user = auth()->user();
        $isDoctor = $user->role_slug === 'doctor';
        $isNurse = $user->role_slug === 'nurse';

        $query = Patient::where('registration_status', '!=', 'PRE_REGISTERED')
            ->where(function ($q) {
                // 1. Newly registered (no encounters)
                $q->whereDoesntHave('encounters')
                // 2. OR has an actively running encounter
                ->orWhereHas('encounters', function ($e) {
                    $e->where('status', '!=', 'Closed')
                      ->orWhere(function ($sub) {
                          $sub->where('type', 'IPD')
                              ->whereHas('admission', function ($a) {
                                  $a->whereNull('discharge_date');
                              });
                      });
                });
            });

        if ($isDoctor) {
            $query->whereHas('appointments', function ($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->whereIn('status', ['scheduled', 'accepted', 'waiting', 'in_consultation', 'consulted']);
            });
        }

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('mrn', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $patients = $query->with([
            'encounters' => function($q) { $q->latest(); },
            'encounters.admission.bed.room.ward',
            'encounters.consultation',
            'encounters.triage'
        ])
        ->select('patients_core1.*')
        // Order by the most recent encounter first, fallback to patient creation date
        ->orderByRaw("
            COALESCE(
                (SELECT created_at FROM encounters_core1 WHERE patient_id = patients_core1.id ORDER BY created_at DESC LIMIT 1),
                patients_core1.created_at
            ) DESC
        ")
        ->paginate(15);

        $nurses = [];
        if (auth()->user()->isAdmin() || auth()->user()->isHeadNurse()) {
            $nurses = User::where('role', 'nurse')->get();
        }

        if ($isDoctor) {
            $patientIds = Patient::where('registration_status', '!=', 'PRE_REGISTERED')
                ->whereHas('appointments', function ($q) use ($user) {
                    $q->where('doctor_id', $user->id)
                      ->whereIn('status', ['scheduled', 'accepted', 'waiting', 'in_consultation', 'consulted']);
                })->pluck('id');
        } elseif ($isNurse && !$user->isHeadNurse()) {
            $patientIds = Patient::where('registration_status', '!=', 'PRE_REGISTERED')->pluck('id');
        } else {
            $patientIds = Patient::where('registration_status', '!=', 'PRE_REGISTERED')->pluck('id');
        }

        $stats = [
            'total'          => $patientIds->count(),
            'active'         => Patient::whereIn('id', $patientIds)->where('status', 'active')->count(),
            'new_today'      => Patient::whereIn('id', $patientIds)->whereDate('created_at', today())->count(),
            'new_this_month' => Patient::whereIn('id', $patientIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('core.core1.patients.index', compact(
            'patients', 'searchTerm', 'statusFilter', 'stats', 'nurses'
        ));
    }

    public function create()
    {
        return view('core.core1.patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['gender'] = strtolower($validated['gender']);

        // Duplicate detection
        $duplicates = Patient::detectDuplicates($validated);
        if ($duplicates->isNotEmpty()) {
            $preReg = $duplicates->firstWhere('registration_status', 'PRE_REGISTERED');
            if ($preReg) {
                return redirect()->route('core1.patients.complete-registration', $preReg)
                    ->with('info', 'A pre-registered patient with matching details was found. Please complete their registration.');
            }
            return redirect()->back()
                ->withInput()
                ->with('warning', 'A patient with matching phone or email already exists. Please review before creating a new record.');
        }


        $validated['mrn']                  = Patient::generateMRN();
        $validated['registration_status']  = 'REGISTERED';
        $validated['status']               = 'active';
        $validated['last_visit']           = now();
        $validated['created_by']           = auth()->id();

        $patient = Patient::create($validated);

        $this->logAudit('patient_created', Patient::class, $patient->id, [], $patient->toArray());
        $this->logAudit('mrn_generated', Patient::class, $patient->id, [], ['mrn' => $patient->mrn]);

        return redirect()->route('core1.patients.index')->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        $this->logAudit('patient_viewed', Patient::class, $patient->id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'patient' => $patient->load('assignedNurse'),
                'age' => $patient->age
            ]);
        }

        return view('core.core1.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('core.core1.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'                 => 'required|string|max:255',
            'middle_name'                => 'nullable|string|max:255',
            'last_name'                  => 'required|string|max:255',
            'date_of_birth'              => 'required|date',
            'gender'                     => 'required|in:male,female,other',
            'phone'                      => 'required|string',
            'email'                      => 'required|email|unique:patients_core1,email,' . $patient->id,
            'address'                    => 'nullable|string',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_phone'    => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'blood_type'                 => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'allergies'                  => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'insurance_provider'         => 'nullable|string|max:255',
            'policy_number'              => 'nullable|string|max:255',
        ]);

        $validated['gender']     = strtolower($validated['gender']);
        $validated['updated_by'] = auth()->id();

        $old = $patient->toArray();
        $patient->update($validated);

        $this->logAudit('patient_updated', Patient::class, $patient->id, $old, $patient->fresh()->toArray());

        return redirect()->route('core1.patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();
        return redirect()->route('core1.patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function move(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'care_type'      => 'required|in:inpatient,outpatient',
            'admission_date' => 'required|date',
            'doctor_id'      => 'required|exists:users_core1,id',
            'reason'         => 'nullable|string',
        ]);

        $patient->update($data);

        return $data['care_type'] === 'inpatient'
            ? redirect()->route('core1.inpatient.index')->with('success', 'Patient admitted successfully.')
            : redirect()->route('core1.outpatient.index')->with('success', 'Patient moved to outpatient care.');
    }

    public function updateStatus(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:scheduled,waiting,in consultation,consulted',
        ]);

        $patient->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    public function assignNurse(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'nurse_id' => 'required|exists:users_core1,id',
        ]);

        $patient->update(['assigned_nurse_id' => $validated['nurse_id']]);

        return back()->with('success', 'Nurse assigned to patient successfully.');
    }

    // ─────────────────────────────────────────────
    // Phase 5: Duplicate Detection Endpoint
    // ─────────────────────────────────────────────

    public function checkDuplicates(Request $request)
    {
        $duplicates = Patient::detectDuplicates([
            'phone'      => $request->input('phone', ''),
            'email'      => $request->input('email', ''),
            'first_name' => $request->input('first_name', ''),
            'last_name'  => $request->input('last_name', ''),
            'date_of_birth' => $request->input('date_of_birth'),
        ]);

        return response()->json([
            'duplicates' => $duplicates->map(fn($p) => [
                'id'                  => $p->id,
                'name'                => $p->name,
                'phone'               => $p->phone,
                'email'               => $p->email,
                'registration_status' => $p->registration_status ?? 'REGISTERED',
                'mrn'                 => $p->mrn,
            ]),
        ]);
    }

    // ─────────────────────────────────────────────
    // Phase 6: Complete Registration
    // ─────────────────────────────────────────────

    public function showCompleteRegistration(Patient $patient)
    {
        if (($patient->registration_status ?? '') !== 'PRE_REGISTERED') {
            return redirect()->route('core1.patients.show', $patient)
                ->with('info', 'This patient is already registered.');
        }

        return view('core.core1.patients.complete-registration', compact('patient'));
    }

    public function completeRegistration(Request $request, Patient $patient): RedirectResponse
    {
        if (($patient->registration_status ?? '') !== 'PRE_REGISTERED') {
            return redirect()->route('core1.patients.show', $patient)
                ->with('info', 'This patient is already registered.');
        }

        $validated = $request->validate([
            'first_name'                 => 'required|string|max:255',
            'middle_name'                => 'nullable|string|max:255',
            'last_name'                  => 'required|string|max:255',
            'date_of_birth'              => 'required|date',
            'gender'                     => 'required|in:male,female,other',
            'address'                    => 'nullable|string',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_phone'    => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'blood_type'                 => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'allergies'                  => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'insurance_provider'         => 'nullable|string|max:255',
            'policy_number'              => 'nullable|string|max:255',
        ]);

        $validated['gender'] = strtolower($validated['gender']);



        $validated['mrn']                 = Patient::generateMRN();
        $validated['registration_status'] = 'REGISTERED';
        $validated['status']              = 'active';
        $validated['last_visit']          = now();
        $validated['updated_by']          = auth()->id();

        $old = $patient->toArray();

        DB::transaction(function () use ($patient, $validated, $old) {
            $patient->update($validated);
            $this->logAudit('patient_registered', Patient::class, $patient->id, $old, $patient->fresh()->toArray());
            $this->logAudit('mrn_generated', Patient::class, $patient->id, [], ['mrn' => $patient->mrn]);
        });

        return redirect()->route('core1.patients.show', $patient)
            ->with('success', 'Patient registration completed. MRN: ' . $patient->fresh()->mrn);
    }

    // ─────────────────────────────────────────────
    // Phase 6: Merge Patients
    // ─────────────────────────────────────────────

    public function mergePatients(Request $request): RedirectResponse
    {
        $request->validate([
            'primary_patient_id'   => 'required|exists:patients_core1,id',
            'secondary_patient_id' => 'required|exists:patients_core1,id|different:primary_patient_id',
        ]);

        $primary   = Patient::findOrFail($request->primary_patient_id);
        $secondary = Patient::findOrFail($request->secondary_patient_id);

        DB::transaction(function () use ($primary, $secondary) {
            // Transfer appointments to primary
            DB::table('appointments_core1')
                ->where('patient_id', $secondary->id)
                ->update(['patient_id' => $primary->id]);

            $old = $secondary->toArray();

            // Mark secondary as merged
            $secondary->update([
                'registration_status' => 'MERGED',
                'merged_into_id'      => $primary->id,
                'updated_by'          => auth()->id(),
            ]);

            $this->logAudit('patient_merged', Patient::class, $secondary->id, $old, [
                'merged_into_id' => $primary->id,
                'primary_mrn'    => $primary->mrn,
            ]);
        });

        return redirect()->route('core1.patients.show', $primary)
            ->with('success', 'Patient records merged successfully.');
    }

    // ─────────────────────────────────────────────
    // Audit Logging Helper
    // ─────────────────────────────────────────────

    private function logAudit(string $action, string $modelType, int $modelId, array $oldValues = [], array $newValues = []): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
