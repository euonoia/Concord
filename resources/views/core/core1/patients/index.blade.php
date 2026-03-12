@extends('core.core1.layouts.app')

@section('title', 'Patient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">

    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Patient Management</h2>
            <p class="core1-subtitle">Manage patient records and registrations</p>
        </div>

        {{-- Only show Register Patient button for Admin and Receptionist --}}
        @if(in_array(auth()->user()->role_slug, ['admin', 'admin_core1', 'receptionist']))
        <div class="d-flex gap-2">
        <button type="button" onclick="openRegisterModal()" class="core1-btn core1-btn-primary">
            <i class="fas fa-plus"></i>
            <span class="ml-2">Register Patient</span>
        </button>
        <button type="button" onclick="document.getElementById('mergeModal').style.display='flex'"
                class="core1-btn core1-btn-outline">
            <i class="fas fa-code-branch"></i>
            <span class="ml-2">Merge Records</span>
        </button>
        </div>
        @endif
    </div>


    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Total Patients</p>
                <p class="core1-title">{{ $stats['total'] }}</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Active Patients</p>
                <p class="core1-title text-green">{{ $stats['active'] }}</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">New Today</p>
                <p class="core1-title text-purple">{{ $stats['new_today'] }}</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">This Month</p>
                <p class="core1-title text-orange">{{ $stats['new_this_month'] }}</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('core1.patients.index') }}" class="core1-search-form">
        <div class="core1-search-input-wrapper">
            <i class="fas fa-search core1-search-icon"></i>
            <input
                type="text"
                name="search"
                value="{{ $searchTerm }}"
                placeholder="Search by name, patient ID, or email..."
                class="core1-search-input"
            >
        </div>
        <select name="status" class="core1-input w-auto m-0">
            <option value="">All Status</option>
            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="core1-btn core1-btn-primary">
            <i class="fas fa-search"></i>
            <span class="ml-2">Search</span>
        </button>
        @if($searchTerm || $statusFilter)
            <a href="{{ route('core1.patients.index') }}" class="core1-btn core1-btn-outline">
                <i class="fas fa-times"></i>
                <span class="ml-2">Clear</span>
            </a>
        @endif
    </form>

    <div class="core1-table-container">
        <table class="core1-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Contact Info</th>
                    <th>Age/Gender</th>
                    <th>Assigned Nurse</th>
                    @if(auth()->user()->role !== 'doctor')
                        <th>Assigned Doctor</th>
                    @endif
                    <th>Last Visit</th>
                    <th>Reg. Status</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>
                            <div class="d-flex items-center gap-3">
                                <div class="core1-avatar">
                                    {{ strtoupper(substr($patient->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-dark">{{ $patient->name }}</div>
                                    <div class="text-xs text-gray font-mono">{{ $patient->patient_id }}</div>
                                    @if($patient->mrn)
                                        <div class="text-xs font-mono font-bold mt-1" style="color:#1a3a5a;">
                                            <i class="fas fa-id-card text-xxs"></i> {{ $patient->mrn }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="text-sm text-dark d-flex items-center gap-2">
                                <i class="fas fa-phone text-xs text-gray"></i>
                                {{ $patient->phone }}
                            </div>
                            <div class="text-sm text-gray d-flex items-center gap-2 mt-4">
                                <i class="fas fa-envelope text-xs text-gray"></i>
                                {{ $patient->email }}
                            </div>
                        </td>

                        <td>
                            <div class="d-flex items-center gap-2">
                                <span class="text-sm font-medium text-dark">{{ $patient->age ?? 'N/A' }}</span>
                                <span class="text-gray">|</span>
                                <span class="text-sm text-gray text-capitalize">{{ $patient->gender }}</span>
                            </div>
                        </td>

     <td>
    @if(auth()->user()->role === 'admin')
        {{-- Admin: show nurse name only, read-only --}}
        <div class="text-sm text-dark">
            {{ $patient->assignedNurse->name ?? 'Not Admitted' }}
        </div>
    @elseif(auth()->user()->isHeadNurse() && $patient->care_type)
        {{-- Head Nurse: keep editable dropdown --}}
        <form action="{{ route('core1.patients.assign-nurse', $patient) }}" method="POST" class="m-0 d-flex gap-2">
            @csrf
            <select name="nurse_id" onchange="this.form.submit()" class="core1-input text-xs w-auto py-5 px-10 m-0">
                <option value="">-- Assign Nurse --</option>
                @foreach($nurses as $nurse)
                    <option value="{{ $nurse->id }}" {{ $patient->assigned_nurse_id == $nurse->id ? 'selected' : '' }}>
                        {{ $nurse->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @else
        {{-- Nurse or others: show assigned nurse without PRIORITY --}}
        <div class="text-sm text-dark">
            {{ $patient->assignedNurse->name ?? 'Not Admitted' }}
        </div>
    @endif
</td>


                        @if(auth()->user()->role !== 'doctor')
                            <td>
                                @php
                                    $doctor = $patient->appointments()->latest()->first()?->doctor;
                                @endphp
                                <div class="text-sm text-dark">
                                    {{ $doctor->name ?? 'Not Assigned' }}
                                </div>
                            </td>
                        @endif

                        <td>
                            <div class="text-sm text-dark">
                                {{ $patient->last_visit ? $patient->last_visit->format('M d, Y') : 'Never' }}
                            </div>
                            @if($patient->last_visit)
                                <div class="text-xs text-gray mt-4">
                                    {{ $patient->last_visit->diffForHumans() }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @php $rs = $patient->registration_status ?? 'REGISTERED'; @endphp
                            <span class="core1-badge {{ $rs === 'REGISTERED' ? 'core1-badge-active' : 'core1-badge-inactive' }}">
                                <i class="fas {{ $rs === 'REGISTERED' ? 'fa-check-circle' : ($rs === 'MERGED' ? 'fa-code-branch' : 'fa-clock') }} text-xxs"></i>
                                <span class="ml-2">{{ str_replace('_', ' ', $rs) }}</span>
                            </span>
                        </td>
                        <td>
                            @php
                                $isPriority = auth()->user()->role === 'nurse' && $patient->assigned_nurse_id === auth()->user()->id;
                            @endphp
                            <span class="core1-badge {{ $patient->status === 'active' ? 'core1-badge-active' : 'core1-badge-inactive' }}">
                                <i class="fas fa-circle text-xxs"></i>
                                <span class="ml-2">
                                    {{ ucfirst($patient->status) }} 
                                    @if($isPriority)
                                        (PRIORITY)
                                    @endif
                                </span>
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td>
                           @php
                            $hasAppointment = $patient->appointments()
                                ->whereIn('status', ['scheduled', 'accepted'])
                                ->exists();
                            $canMovePatient = in_array(auth()->user()->role, ['admin', 'doctor']);
                            $isAdmin = auth()->user()->role === 'admin';
                        @endphp

                        {{-- Admin view: show actions if patient has appointment OR already admitted --}}
                        @if(!$isAdmin || ($isAdmin && ($hasAppointment || $patient->care_type)) || auth()->user()->role === 'doctor')
                            <div class="d-flex items-center justify-center gap-2">
                                {{-- View --}}
                                <button type="button" onclick="openPatientModal('{{ $patient->id }}')" 
                                   class="core1-icon-action text-blue"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- Edit --}}
                                @if(auth()->user()->role !== 'doctor')
                                    <button type="button" onclick="openEditModal('{{ $patient->id }}')" 
                                       class="core1-icon-action text-blue"
                                       title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endif

                                {{-- Complete Registration (PRE_REGISTERED only) --}}
                                @if(($patient->registration_status ?? '') === 'PRE_REGISTERED' && auth()->user()->role_slug === 'receptionist')
                                    <a href="{{ route('core1.patients.complete-registration', $patient) }}"
                                       class="core1-icon-action" style="color:#059669;" title="Complete Registration">
                                        <i class="fas fa-user-check"></i>
                                    </a>
                                @endif


                                {{-- Delete --}}
                                @if(auth()->user()->role !== 'doctor')
                                    <form action="{{ route('core1.patients.destroy', $patient) }}" 
                                          method="POST"
                                          class="m-0 d-flex items-center"
                                          onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="core1-icon-action text-red">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(!$patient->care_type)
                                    <form method="POST" action="{{ route('core1.encounters.store') }}" class="d-flex gap-1" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                        <input type="hidden" name="type" value="Pending">
                                        <input type="hidden" name="chief_complaint" value="Walk-in / Arrival">
                                        <button class="core1-btn-sm core1-btn-primary" style="padding: 2px 10px; font-size: 0.75rem;">
                                            <i class="fas fa-hospital-user mr-5"></i> Send to Triage
                                        </button>
                                    </form>
                                @elseif($patient->care_type)
                                    <span class="core1-badge {{ $patient->care_type === 'inpatient' ? 'core1-badge-active' : 'core1-badge-inactive' }}">
                                        {{ strtoupper($patient->care_type) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            {{-- Admin view, patient not scheduled and not admitted --}}
                            @if($isAdmin && !$hasAppointment && !$patient->care_type)
                                <span class="text-gray text-xs">No actions available</span>
                            @endif
                        @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-40">
                            <div class="d-flex flex-col items-center justify-center">
                                <div class="icon-box-large">
                                    <i class="fas fa-user-slash"></i>
                                </div>
                                <p class="text-dark font-medium text-lg">No patients found</p>
                                <p class="text-gray text-sm mb-5">
                                    @if($searchTerm || $statusFilter)
                                        Try adjusting your search or filters
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
        <div class="d-flex justify-between items-center mt-25">
            <div class="text-sm text-gray">
                Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} patients
            </div>
            <div>
                {{ $patients->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Register Patient Modal -->
<!-- Register Patient Modal -->
<div id="registerModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:750px; max-width:90%; max-height: 90vh; overflow-y: auto; padding:0; border-top:none; border-radius:12px;">
        <!-- Modal Header -->
        <div class="core1-flex-between" style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0; position: sticky; top: 0; z-index: 10;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h3 class="core1-title" id="modal-title" style="font-size: 18px; line-height:1.2; margin:0; padding:0;">Register New Patient</h3>
                    <p class="core1-subtitle" style="font-size: 13px; margin:2px 0 0 0; padding:0;">Fill out the form below to add a new record.</p>
                </div>
            </div>
            <button type="button" onclick="closeRegisterModal()" class="core1-btn-sm" style="background: transparent; border: none; color: var(--text-gray); font-size: 1.8rem; cursor: pointer; padding:0;">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <form action="{{ route('core1.patients.store') }}" method="POST" id="registerForm" style="padding:0; margin:0;">
            @csrf
            <div style="padding: 25px; display: flex; flex-direction: column; gap: 25px;">
                
                {{-- Section 1: Patient Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-person-vcard"></i> 1. Patient Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="core1-input" required>
                            @error('first_name') <small style="color:var(--danger); font-size:10px;">{{ $message }}</small> @enderror
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="core1-input">
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="core1-input" required>
                            @error('last_name') <small style="color:var(--danger); font-size:10px;">{{ $message }}</small> @enderror
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Gender</label>
                            <select name="gender" class="core1-input" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="core1-input" required>
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Complete Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" class="core1-input">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Medical Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-heart-pulse"></i> 2. Medical Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">Blood Type</label>
                            <select name="blood_type" class="core1-input">
                                <option value="Unknown">Unknown</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Allergies (if any)</label>
                            <input type="text" name="allergies" value="{{ old('allergies') }}" class="core1-input">
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Medical History</label>
                            <textarea name="medical_history" rows="2" class="core1-input" style="resize:none;">{{ old('medical_history') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Emergency Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-telephone-outbound"></i> 3. Emergency Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Relationship</label>
                            <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="core1-input">
                        </div>
                    </div>
                </div>

                {{-- Section 4: Insurance Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-shield-check"></i> 4. Insurance Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">Insurance Provider</label>
                            <input type="text" name="insurance_provider" value="{{ old('insurance_provider') }}" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Policy/Member Number</label>
                            <input type="text" name="policy_number" value="{{ old('policy_number') }}" class="core1-input">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div style="padding: 20px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 12px 12px; position: sticky; bottom:0;">
                <button type="button" onclick="closeRegisterModal()" class="core1-btn core1-btn-outline" style="border-radius: 8px; padding: 10px 20px;">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary" style="border-radius: 8px; padding: 10px 25px;">
                    <i class="bi bi-check-circle mr-5"></i> Confirm Registration
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- Edit Patient Modal --}}
<div id="editPatientModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:750px; max-width:90%; max-height: 90vh; overflow-y: auto; padding:0; border-top:none; border-radius:12px;">
        <!-- Modal Header -->
        <div class="core1-flex-between" style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0; position: sticky; top: 0; z-index: 10;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--warning-light-more); color: var(--warning); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h3 class="core1-title" style="font-size: 18px; line-height:1.2; margin:0; padding:0;">Edit Patient Profile</h3>
                    <p class="core1-subtitle" style="font-size: 13px; margin:2px 0 0 0; padding:0;">Update patient demographic and medical records.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="core1-btn-sm" style="background: transparent; border: none; color: var(--text-gray); font-size: 1.8rem; cursor: pointer; padding:0;">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <form id="editPatientForm" method="POST" style="padding:0; margin:0;">
            @csrf
            @method('PUT')
            <div style="padding: 25px; display: flex; flex-direction: column; gap: 25px;">
                
                {{-- Section 1: Patient Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-person-vcard"></i> 1. Patient Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Middle Name</label>
                            <input type="text" name="middle_name" id="edit_middle_name" class="core1-input">
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Gender</label>
                            <select name="gender" id="edit_gender" class="core1-input" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Phone Number</label>
                            <input type="tel" name="phone" id="edit_phone" class="core1-input" required>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="core1-input" required>
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Complete Address</label>
                            <input type="text" name="address" id="edit_address" class="core1-input">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Medical Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-heart-pulse"></i> 2. Medical Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">Blood Type</label>
                            <select name="blood_type" id="edit_blood_type" class="core1-input">
                                <option value="Unknown">Unknown</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Allergies (if any)</label>
                            <input type="text" name="allergies" id="edit_allergies" class="core1-input">
                        </div>
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Medical History</label>
                            <textarea name="medical_history" id="edit_medical_history" rows="2" class="core1-input" style="resize:none;"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Emergency Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-telephone-outbound"></i> 3. Emergency Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group" style="grid-column: span 2;">
                            <label class="core1-form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" id="edit_emergency_contact_name" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Relationship</label>
                            <input type="text" name="emergency_contact_relation" id="edit_emergency_contact_relation" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" id="edit_emergency_contact_phone" class="core1-input">
                        </div>
                    </div>
                </div>

                {{-- Section 4: Insurance Information --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-shield-check"></i> 4. Insurance Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="core1-form-group">
                            <label class="core1-form-label">Insurance Provider</label>
                            <input type="text" name="insurance_provider" id="edit_insurance_provider" class="core1-input">
                        </div>
                        <div class="core1-form-group">
                            <label class="core1-form-label">Policy/Member Number</label>
                            <input type="text" name="policy_number" id="edit_policy_number" class="core1-input">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div style="padding: 20px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 12px 12px; position: sticky; bottom:0;">
                <button type="button" onclick="closeEditModal()" class="core1-btn core1-btn-outline" style="border-radius: 8px; padding: 10px 20px;">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary" style="border-radius: 8px; padding: 10px 25px;">
                    <i class="bi bi-save mr-5"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Duplicate Warning Modal --}}
<div id="duplicateModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:500px; max-width:90%; padding:0; border-top:none; border-radius:12px;">
        <!-- Modal Header -->
        <div style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--warning-light-more); color: var(--warning); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h3 class="core1-title" style="font-size: 16px; margin:0;">Possible Duplicates Found</h3>
                    <p class="core1-subtitle" style="font-size: 12px; margin:2px 0 0 0;">Review existing records before proceeding.</p>
                </div>
            </div>
        </div>

        <div style="padding: 20px 25px;">
            <p style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 15px;">Existing Matches</p>
            <div id="duplicateList" style="display: flex; flex-direction: column; gap: 10px; max-height: 300px; overflow-y: auto; padding-right: 5px;"></div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 15px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; flex-direction: column; gap: 10px; border-radius: 0 0 12px 12px;">
            <button type="button" onclick="closeDuplicateModal(); openRegisterModal();" class="core1-btn core1-btn-outline" style="width: 100%; border-radius: 8px;">
                <i class="bi bi-person-plus"></i> Create New Patient Anyway
            </button>
            <button type="button" onclick="closeDuplicateModal();" class="core1-btn core1-btn-primary" style="width: 100%; border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Go Back and Edit
            </button>
        </div>
    </div>
</div>

{{-- Patient Details Modal --}}
<div id="patientDetailsModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:300; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:750px; max-width:90%; max-height: 85vh; overflow-y: auto; padding:0; border-top:none; border-radius:12px;">
        <!-- Modal Header -->
        <div class="core1-flex-between" style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0; position: sticky; top: 0; z-index: 10;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h3 class="core1-title" id="modalPatientName" style="font-size: 18px; line-height:1.2; margin:0; padding:0;">Patient Name</h3>
                    <p class="core1-subtitle" id="modalPatientMRN" style="font-size: 13px; margin:2px 0 0 0; padding:0; font-family: monospace;">MRN: ---</p>
                </div>
            </div>
            <button type="button" onclick="closePatientModal()" class="core1-btn-sm" style="background: transparent; border: none; color: var(--text-gray); font-size: 1.8rem; cursor: pointer; padding:0;">
                <i class="bi bi-x"></i>
            </button>
        </div>
        
        <div style="padding: 25px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                {{-- Section 1: Demographics --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-person-lines-fill"></i> 1. Demographics
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Birth Date</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalDOB">---</p>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Gender</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalGender">---</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Email</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalEmail">---</p>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Age</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalAge">---</p>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Phone</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalPhone">---</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Address</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalAddress">---</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Medical Info --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-heart-pulse"></i> 2. Medical Info
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Blood Type</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalBloodType">---</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Allergies</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--danger);" id="modalAllergies">---</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Medical History</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalHistory">---</p>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Emergency Contact --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-exclamation-triangle"></i> 3. Emergency Contact
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Contact Name</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECName">---</p>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Relation</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECRelation">---</p>
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Phone</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECPhone">---</p>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Insurance --}}
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-shield-lock"></i> 4. Insurance
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Provider</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalInsurance">---</p>
                        </div>
                        <div style="grid-column: span 2;">
                            <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Policy Number</label>
                            <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalPolicy">---</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 20px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; justify-content: flex-end; border-radius: 0 0 12px 12px;">
            <button type="button" onclick="closePatientModal()" class="core1-btn core1-btn-outline" style="border-radius: 8px; padding: 10px 20px;">Close Record</button>
        </div>
    </div>
</div>

{{-- Merge Patients Modal --}}
<div id="mergeModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1100; align-items:center; justify-content:center;">
    <div class="core1-modal-content core1-card" style="width:500px; max-width:90%; padding:0; border-top:none; border-radius:12px;">
        <!-- Modal Header -->
        <div style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                    <i class="bi bi-intersect"></i>
                </div>
                <div>
                    <h3 class="core1-title" style="font-size: 16px; margin:0;">Merge Patient Records</h3>
                    <p class="core1-subtitle" style="font-size: 12px; margin:2px 0 0 0;">Combine pre-registered data into a primary record.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('core1.patients.merge') }}" method="POST" style="padding:0; margin:0;">
            @csrf
            <div style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                <div class="core1-form-group">
                    <label class="core1-form-label" style="font-size: 10px; color: var(--text-gray);">Primary Patient (REGISTERED)</label>
                    <select name="primary_patient_id" class="core1-input" required>
                        <option value="">— Select Primary Patient —</option>
                        @foreach($patients as $p)
                            @if(($p->registration_status ?? '') === 'REGISTERED')
                                <option value="{{ $p->id }}">{{ $p->name }} • {{ $p->mrn ?? $p->patient_id }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="core1-form-group">
                    <label class="core1-form-label" style="font-size: 10px; color: var(--text-gray);">Secondary Patient (PRE-REGISTERED)</label>
                    <select name="secondary_patient_id" class="core1-input" required>
                        <option value="">— Select Pre-Registered Patient —</option>
                        @foreach($patients as $p)
                            @if(($p->registration_status ?? '') === 'PRE_REGISTERED')
                                <option value="{{ $p->id }}">{{ $p->name }} • {{ $p->phone }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div style="background: var(--warning-light-more); padding: 12px; border-radius: 8px; border: 1px solid var(--warning-light);">
                    <p style="font-size: 11px; color: var(--warning); margin:0;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        All appointments from the secondary patient will be transferred. This action is irreversible.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 15px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; justify-content: flex-end; gap: 12px; border-radius: 0 0 12px 12px;">
                <button type="button" onclick="document.getElementById('mergeModal').style.display='none'" class="core1-btn core1-btn-outline" style="border-radius: 8px; padding: 8px 15px;">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary" style="border-radius: 8px; padding: 8px 20px; background: var(--primary);" onclick="return confirm('Confirm permanent merge?')">
                    <i class="bi bi-intersect mr-5"></i> Confirm Merge
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        }
    }

    function openRegisterModal() {
        document.getElementById('registerModal').style.display = 'flex';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
    }

    function openPatientModal(id) {
        // Show loading state or clear previous
        document.getElementById('modalPatientName').innerText = 'Loading...';
        document.getElementById('patientDetailsModal').style.display = 'flex';

        fetch(`/core/patients/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const p = data.patient;
            document.getElementById('modalPatientName').innerText = p.first_name + ' ' + (p.middle_name ? p.middle_name + ' ' : '') + p.last_name;
            document.getElementById('modalPatientMRN').innerText = 'MRN: ' + (p.mrn || 'Not assigned');
            document.getElementById('modalDOB').innerText = p.date_of_birth ? new Date(p.date_of_birth).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '---';
            document.getElementById('modalAge').innerText = data.age + ' years';
            document.getElementById('modalGender').innerText = p.gender ? p.gender.charAt(0).toUpperCase() + p.gender.slice(1) : '---';
            document.getElementById('modalPhone').innerText = p.phone || '---';
            document.getElementById('modalEmail').innerText = p.email || '---';
            document.getElementById('modalAddress').innerText = p.address || '---';
            
            document.getElementById('modalBloodType').innerText = p.blood_type || '---';
            document.getElementById('modalAllergies').innerText = p.allergies || 'None';
            document.getElementById('modalHistory').innerText = p.medical_history || 'None';
            
            document.getElementById('modalECName').innerText = p.emergency_contact_name || '---';
            document.getElementById('modalECPhone').innerText = p.emergency_contact_phone || '---';
            document.getElementById('modalECRelation').innerText = p.emergency_contact_relation || '---';
            
            document.getElementById('modalInsurance').innerText = p.insurance_provider || '---';
            document.getElementById('modalPolicy').innerText = p.policy_number || '---';
            

        })
        .catch(error => {
            console.error('Error fetching patient details:', error);
            document.getElementById('modalPatientName').innerText = 'Error loading details';
        });
    }

    function closePatientModal() {
        document.getElementById('patientDetailsModal').style.display = 'none';
    }

    function openEditModal(id) {
        document.getElementById('editPatientModal').style.display = 'flex';

        fetch(`/core/patients/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const p = data.patient;
            const form = document.getElementById('editPatientForm');
            form.action = `/core/patients/${id}`;
            
            // Populate fields
            document.getElementById('edit_first_name').value = p.first_name || '';
            document.getElementById('edit_middle_name').value = p.middle_name || '';
            document.getElementById('edit_last_name').value = p.last_name || '';
            document.getElementById('edit_date_of_birth').value = p.date_of_birth ? p.date_of_birth.split('T')[0] : '';
            document.getElementById('edit_gender').value = p.gender || '';
            document.getElementById('edit_phone').value = p.phone || '';
            document.getElementById('edit_email').value = p.email || '';
            document.getElementById('edit_address').value = p.address || '';
            document.getElementById('edit_blood_type').value = p.blood_type || 'Unknown';
            document.getElementById('edit_allergies').value = p.allergies || '';
            document.getElementById('edit_medical_history').value = p.medical_history || '';
            document.getElementById('edit_emergency_contact_name').value = p.emergency_contact_name || '';
            document.getElementById('edit_emergency_contact_relation').value = p.emergency_contact_relation || '';
            document.getElementById('edit_emergency_contact_phone').value = p.emergency_contact_phone || '';
            document.getElementById('edit_insurance_provider').value = p.insurance_provider || '';
            document.getElementById('edit_policy_number').value = p.policy_number || '';

            // Trigger floating label logic by ensuring inputs are treated as filled
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.value) {
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        })
        .catch(error => {
            console.error('Error fetching patient for edit:', error);
            alert('Could not load patient data. Please try again.');
            closeEditModal();
        });
    }

    function closeEditModal() {
        document.getElementById('editPatientModal').style.display = 'none';
    }

    function openDuplicateModal(duplicates) {
        const list = document.getElementById('duplicateList');
        list.innerHTML = '';
        duplicates.forEach(p => {
            const isPreReg = p.registration_status === 'PRE_REGISTERED';
            list.innerHTML += `
            <div style="border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; display: flex; justify-content: space-between; align-items: center; background: white;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="core1-icon-box" style="background: var(--bg-light); color: var(--text-gray); width: 35px; height: 35px; border-radius: 6px; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 13px; color: var(--text-dark);">${p.name}</div>
                        <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 5px;">
                            <span>${p.phone}</span> • <span style="font-weight: 600; color: ${isPreReg?'var(--warning)':'var(--success)'};">${p.registration_status}</span>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 5px;">
                    <a href="/core/patients/${p.id}" class="core1-btn-sm" style="background: var(--bg-light); color: var(--text-dark); padding: 5px 10px; font-size: 10px; border-radius: 6px;">View</a>
                    ${isPreReg ? `<a href="/core/patients/${p.id}/complete-registration" class="core1-btn-sm" style="background: var(--success); color: white; padding: 5px 10px; font-size: 10px; border-radius: 6px;">Register</a>` : ''}
                </div>
            </div>`;
        });
        document.getElementById('duplicateModal').style.display = 'flex';
    }

    function closeDuplicateModal() {
        document.getElementById('duplicateModal').style.display = 'none';
    }
    
    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const mergeModal = document.getElementById('mergeModal');
        if (mergeModal) {
            mergeModal.addEventListener('click', function(e) {
                if (e.target === mergeModal) {
                    mergeModal.style.display = 'none';
                }
            });
        }

        // Auto-open modal if there are validation errors related to patient registration
        @if($errors->has('first_name') || $errors->has('last_name') || $errors->has('date_of_birth') || $errors->has('gender') || $errors->has('phone') || $errors->has('email'))
            openRegisterModal();
        @endif

        // Duplicate detection interceptor on register form submit
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const firstName = registerForm.querySelector('[name="first_name"]')?.value ?? '';
                const lastName = registerForm.querySelector('[name="last_name"]')?.value ?? '';
                const dob = registerForm.querySelector('[name="date_of_birth"]')?.value ?? '';
                const email = registerForm.querySelector('[name="email"]')?.value ?? '';
                
                if (!firstName || !lastName || !dob || !email) return;
                
                e.preventDefault();
                const query = new URLSearchParams({
                    first_name: firstName,
                    last_name: lastName,
                    date_of_birth: dob,
                    email: email
                });
                
                fetch(`{{ route('core1.patients.check-duplicates') }}?${query.toString()}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.duplicates?.length > 0) {
                            closeRegisterModal();
                            openDuplicateModal(data.duplicates);
                        } else {
                            registerForm.submit();
                        }
                    })
                    .catch(() => registerForm.submit());
            });
        }
    });
</script>
@endpush

