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
        <button type="button" onclick="document.getElementById('mergeModal').style.display='block'"
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

                                {{-- Move to Inpatient/Outpatient --}}
                                @if($canMovePatient && !$patient->care_type)
                                    <form method="POST" action="{{ route('core1.patients.move', $patient) }}" class="d-flex gap-1" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="care_type" value="inpatient">
                                        <input type="hidden" name="admission_date" value="{{ now()->toDateString() }}">
                                        <input type="hidden" name="doctor_id" value="{{ auth()->user()->id }}">
                                        <input type="hidden" name="reason" value="Routine Checkup">
                                        <button class="core1-btn-sm core1-btn-outline" style="padding: 2px 5px; font-size: 0.75rem;">Move to Inpatient</button>
                                    </form>

                                    <form method="POST" action="{{ route('core1.patients.move', $patient) }}" class="d-flex gap-1" style="margin: 0;">
                                        @csrf
                                        <input type="hidden" name="care_type" value="outpatient">
                                        <input type="hidden" name="admission_date" value="{{ now()->toDateString() }}">
                                        <input type="hidden" name="doctor_id" value="{{ auth()->user()->id }}">
                                        <input type="hidden" name="reason" value="Routine Checkup">
                                        <button class="core1-btn-sm core1-btn-outline" style="padding: 2px 5px; font-size: 0.75rem;">Move to Outpatient</button>
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
<div id="registerModal" class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" style="z-index: -1;"></div>
    
    <div class="flex min-h-screen w-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold leading-6 text-gray-900" id="modal-title">Register New Patient</h3>
                    <p class="mt-2 text-sm text-gray-500">Please fill out the form below to add a new patient to the system.</p>
                </div>
                
                <form action="{{ route('core1.patients.store') }}" method="POST" class="space-y-6" id="registerForm">
                    @csrf
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <!-- Section 1: Patient Information -->
                        <div class="col-span-2">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">1. Patient Information</h4>
                        </div>
                        
                        {{-- Name split into first/middle/last --}}
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="first_name" id="modal_first_name" value="{{ old('first_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="First Name" required>
                            <label for="modal_first_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">First Name</label>
                            @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="middle_name" id="modal_middle_name" value="{{ old('middle_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Middle Name">
                            <label for="modal_middle_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Middle Name</label>
                        </div>
                        <div class="relative col-span-2">
                            <input type="text" name="last_name" id="modal_last_name" value="{{ old('last_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Last Name" required>
                            <label for="modal_last_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Last Name</label>
                            @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- DOB -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date of Birth" required>
                            <label for="date_of_birth" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Date of Birth</label>
                            @error('date_of_birth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Gender -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <select name="gender" id="gender" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent" required>
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <label for="gender" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Gender</label>
                            @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Phone Number" required>
                            <label for="phone" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Phone Number</label>
                            @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Email Address" required>
                            <label for="email" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Email Address</label>
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Address -->
                        <div class="relative col-span-2">
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Complete Address">
                            <label for="address" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Complete Address</label>
                            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Section 2: Medical Information -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">2. Medical Information</h4>
                        </div>

                        <!-- Blood Type -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <select name="blood_type" id="blood_type" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent">
                                <option value="" disabled {{ old('blood_type') ? '' : 'selected' }}>Select Blood Type</option>
                                <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                <option value="Unknown" {{ old('blood_type') == 'Unknown' ? 'selected' : '' }}>Unknown</option>
                            </select>
                            <label for="blood_type" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Blood Type</label>
                            @error('blood_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Allergies -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="allergies" id="allergies" value="{{ old('allergies') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Allergies (if any)">
                            <label for="allergies" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Allergies (if any)</label>
                            @error('allergies') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Medical History -->
                        <div class="relative col-span-2">
                            <textarea name="medical_history" id="medical_history" rows="2" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Pre-existing Conditions, Past Surgeries, etc.">{{ old('medical_history') }}</textarea>
                            <label for="medical_history" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Medical History</label>
                            @error('medical_history') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Section 3: Emergency Information -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">3. Emergency Information</h4>
                        </div>

                        <!-- Emergency Contact Name -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Emergency Contact Name">
                            <label for="emergency_contact_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Emergency Contact Name</label>
                            @error('emergency_contact_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Emergency Contact Phone -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Emergency Contact Phone">
                            <label for="emergency_contact_phone" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Emergency Contact Phone</label>
                            @error('emergency_contact_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Emergency Contact Relation -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Relationship to Patient">
                            <label for="emergency_contact_relation" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Relationship to Patient</label>
                            @error('emergency_contact_relation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Section 4: Insurance Information -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">4. Insurance Information</h4>
                        </div>

                        <!-- Insurance Provider -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Insurance Provider">
                            <label for="insurance_provider" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Insurance Provider</label>
                            @error('insurance_provider') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Policy Number -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="policy_number" id="policy_number" value="{{ old('policy_number') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Policy/Member Number">
                            <label for="policy_number" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Policy/Member Number</label>
                            @error('policy_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                    </div>
                    
                    <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeRegisterModal()" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto rounded-lg bg-[#1a3a5a] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-[#142d45] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 rounded-b-2xl">
                <p class="text-xs text-center text-gray-500 flex items-center justify-center gap-1">
                    <i class="fas fa-lock text-xxs"></i> Patient data is secure and encrypted.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Edit Patient Modal --}}
<div id="editPatientModal" class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" style="z-index: -1;"></div>
    
    <div class="flex min-h-screen w-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold leading-6 text-gray-900" id="edit-modal-title">Edit Patient Record</h3>
                    <p class="mt-2 text-sm text-gray-500">Update the patient information below.</p>
                </div>
                
                <form id="editPatientForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <!-- Section 1: Demographics -->
                        <div class="col-span-2">
                            <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider border-b pb-1">1. Demographics</h4>
                        </div>
                        
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="first_name" id="edit_first_name" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="First Name" required>
                            <label for="edit_first_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">First Name</label>
                        </div>
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="middle_name" id="edit_middle_name" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Middle Name">
                            <label for="edit_middle_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Middle Name</label>
                        </div>
                        <div class="relative col-span-2">
                            <input type="text" name="last_name" id="edit_last_name" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Last Name" required>
                            <label for="edit_last_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Last Name</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="date" name="date_of_birth" id="edit_date_of_birth" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" required>
                            <label for="edit_date_of_birth" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Date of Birth</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <select name="gender" id="edit_gender" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <label for="edit_gender" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Gender</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="phone" id="edit_phone" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Phone Number" required>
                            <label for="edit_phone" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Phone Number</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="email" name="email" id="edit_email" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Email Address" required>
                            <label for="edit_email" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Email Address</label>
                        </div>

                        <div class="relative col-span-2">
                            <input type="text" name="address" id="edit_address" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Complete Address">
                            <label for="edit_address" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Complete Address</label>
                        </div>

                        <!-- Section 2: Medical Info -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-xs font-bold text-green-600 uppercase tracking-wider border-b pb-1">2. Medical Information</h4>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <select name="blood_type" id="edit_blood_type" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent">
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
                            <label for="edit_blood_type" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Blood Type</label>
                        </div>
                        
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="allergies" id="edit_allergies" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Allergies (if any)">
                            <label for="edit_allergies" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Allergies (if any)</label>
                        </div>

                        <div class="relative col-span-2">
                            <textarea name="medical_history" id="edit_medical_history" rows="2" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Medical History"></textarea>
                            <label for="edit_medical_history" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Medical History</label>
                        </div>

                        <!-- Section 3: Emergency Contact -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-xs font-bold text-orange-600 uppercase tracking-wider border-b pb-1">3. Emergency Contact</h4>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_name" id="edit_emergency_contact_name" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Contact Name">
                            <label for="edit_emergency_contact_name" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Contact Name</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_relation" id="edit_emergency_contact_relation" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Relationship">
                            <label for="edit_emergency_contact_relation" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Relationship</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="emergency_contact_phone" id="edit_emergency_contact_phone" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Contact Phone">
                            <label for="edit_emergency_contact_phone" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Contact Phone</label>
                        </div>

                        <!-- Section 4: Insurance -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider border-b pb-1">4. Insurance</h4>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="insurance_provider" id="edit_insurance_provider" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Insurance Provider">
                            <label for="edit_insurance_provider" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Insurance Provider</label>
                        </div>

                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="policy_number" id="edit_policy_number" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Policy Number">
                            <label for="edit_policy_number" class="absolute left-3 top-0 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-0 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Policy Number</label>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeEditModal()" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto rounded-lg bg-[#1a3a5a] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-[#142d45] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Duplicate Warning Modal --}}
<div id="duplicateModal" class="fixed inset-0 z-[1050] overflow-y-auto" style="display:none;" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" style="z-index:-1;"></div>
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-lg">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#fef3c7;">
                        <i class="fas fa-exclamation-triangle" style="color:#d97706;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Possible Duplicate Patient Found</h3>
                        <p class="text-sm text-gray-500">Review the matches below before proceeding.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 max-h-64 overflow-y-auto">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Existing Records</p>
                <div id="duplicateList"></div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex flex-col sm:flex-row gap-2 justify-end border-t border-gray-200">
                <button type="button" onclick="closeDuplicateModal(); openRegisterModal();" class="core1-btn core1-btn-outline text-sm">
                    <i class="fas fa-plus"></i><span class="pl-10">Create New Patient Anyway</span>
                </button>
                <button type="button" onclick="closeDuplicateModal();" class="core1-btn core1-btn-primary text-sm" style="background-color:#1a3a5a;color:white;">
                    <i class="fas fa-arrow-left"></i><span class="pl-10">Go Back & Review</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Patient Details Modal --}}
<div id="patientDetailsModal" class="fixed inset-0 z-[1060] overflow-y-auto" style="display:none;" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" style="z-index:-1;"></div>
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-3xl">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#eff6ff;">
                        <i class="fas fa-user-circle" style="color:#2563eb;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" id="modalPatientName">Patient Name</h3>
                        <p class="text-sm text-gray-500" id="modalPatientMRN">MRN: ---</p>
                    </div>
                </div>
                <button type="button" onclick="closePatientModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                {{-- Modal Content Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Section 1: Demographics --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider border-b pb-2">1. Demographics</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Birth Date</label>
                                <p class="text-sm font-medium text-gray-900" id="modalDOB">---</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Gender</label>
                                <p class="text-sm font-medium text-gray-900" id="modalGender">---</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Email</label>
                                <p class="text-sm font-medium text-gray-900" id="modalEmail">---</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Age</label>
                                <p class="text-sm font-medium text-gray-900" id="modalAge">---</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Phone</label>
                                <p class="text-sm font-medium text-gray-900" id="modalPhone">---</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Address</label>
                                <p class="text-sm font-medium text-gray-900" id="modalAddress">---</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Medical Info --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-green-600 uppercase tracking-wider border-b pb-2">2. Medical Info</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Blood Type</label>
                                <p class="text-sm font-medium text-gray-900" id="modalBloodType">---</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Allergies</label>
                                <p class="text-sm font-medium text-gray-900" id="modalAllergies">---</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Medical History</label>
                                <p class="text-sm font-medium text-gray-900" id="modalHistory">---</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Emergency Contact --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-orange-600 uppercase tracking-wider border-b pb-2">3. Emergency Contact</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Contact Name</label>
                                <p class="text-sm font-medium text-gray-900" id="modalECName">---</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Relation</label>
                                <p class="text-sm font-medium text-gray-900" id="modalECRelation">---</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Phone</label>
                                <p class="text-sm font-medium text-gray-900" id="modalECPhone">---</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Insurance --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider border-b pb-2">4. Insurance</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Provider</label>
                                <p class="text-sm font-medium text-gray-900" id="modalInsurance">---</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase">Policy Number</label>
                                <p class="text-sm font-medium text-gray-900" id="modalPolicy">---</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex gap-2 justify-end border-t border-gray-200">

                <button type="button" onclick="closePatientModal()" class="core1-btn core1-btn-outline text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Merge Patients Modal --}}
<div id="mergeModal" class="fixed inset-0 z-[1060] overflow-y-auto" style="display:none;" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" style="z-index:-1;"></div>
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-md">
            <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#ede9fe;">
                        <i class="fas fa-code-branch" style="color:#7c3aed;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Merge Patient Records</h3>
                        <p class="text-sm text-gray-500">The pre-registered record will be absorbed into the primary record.</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('core1.patients.merge') }}" method="POST">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Primary Patient (REGISTERED)</label>
                        <select name="primary_patient_id" class="core1-input w-full mt-1" required>
                            <option value="">— Select Primary Patient —</option>
                            @foreach($patients as $p)
                                @if(($p->registration_status ?? '') === 'REGISTERED')
                                    <option value="{{ $p->id }}">{{ $p->name }} &bull; {{ $p->mrn ?? $p->patient_id }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Secondary Patient (PRE REGISTERED — will be merged)</label>
                        <select name="secondary_patient_id" class="core1-input w-full mt-1" required>
                            <option value="">— Select Pre-Registered Patient —</option>
                            @foreach($patients as $p)
                                @if(($p->registration_status ?? '') === 'PRE_REGISTERED')
                                    <option value="{{ $p->id }}">{{ $p->name }} &bull; {{ $p->phone }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="rounded-lg p-3" style="background-color:#fef3c7;border:1px solid #fde68a;">
                        <p class="text-xs" style="color:#92400e;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            All appointments from the secondary patient will be transferred to the primary patient. This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex gap-2 justify-end border-t border-gray-200">
                    <button type="button" onclick="document.getElementById('mergeModal').style.display='none'" class="core1-btn core1-btn-outline text-sm">Cancel</button>
                    <button type="submit" class="core1-btn text-sm" style="background-color:#7c3aed;color:white;" onclick="return confirm('Are you sure? This will merge the records permanently.')">
                        <i class="fas fa-code-branch"></i><span class="pl-10">Confirm Merge</span>
                    </button>
                </div>
            </form>
        </div>
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
        document.getElementById('registerModal').style.display = 'block';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
    }

    function openPatientModal(id) {
        // Show loading state or clear previous
        document.getElementById('modalPatientName').innerText = 'Loading...';
        document.getElementById('patientDetailsModal').style.display = 'block';

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
        // Show loading state if needed
        document.getElementById('editPatientModal').style.display = 'block';

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
            <div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-weight:600;font-size:14px;">${p.name}</div>
                    <div style="font-size:12px;color:#6b7280;">${p.phone} &bull; ${p.email ?? ''}</div>
                    <span style="font-size:11px;padding:2px 8px;border-radius:999px;font-weight:500;background:${isPreReg?'#fef3c7':'#d1fae5'};color:${isPreReg?'#92400e':'#065f46'};">
                        ${p.registration_status}
                    </span>
                </div>
                <div style="display:flex;flex-direction:column;gap:4px;margin-left:12px;">
                    <a href="/core/patients/${p.id}" class="core1-btn core1-btn-outline" style="font-size:12px;padding:4px 10px;">View</a>
                    ${isPreReg ? `<a href="/core/patients/${p.id}/complete-registration" class="core1-btn" style="font-size:12px;padding:4px 10px;background:#059669;color:white;">Complete Reg.</a>` : ''}
                </div>
            </div>`;
        });
        document.getElementById('duplicateModal').style.display = 'block';
    }

    function closeDuplicateModal() {
        document.getElementById('duplicateModal').style.display = 'none';
    }
    
    // Close modals when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('registerModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal || e.target.classList.contains('backdrop-blur-sm') || e.target.classList.contains('min-h-screen')) {
                    closeRegisterModal();
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

