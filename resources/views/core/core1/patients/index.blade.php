@extends('layouts.core1.layouts.app')

@section('title', 'Patient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    @if(session('success'))
        <div class="alert alert-success d-flex items-center gap-2" role="alert">
            <i class="fas fa-check-circle"></i>
            <p class="m-0">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error d-flex items-center gap-2" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <p class="m-0">{{ session('error') }}</p>
        </div>
    @endif

    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Patient Management</h2>
            <p class="core1-subtitle">Manage patient records and registrations</p>
        </div>

        {{-- Only show Register Patient button for Admin and Receptionist --}}
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
        <button type="button" onclick="openRegisterModal()" class="core1-btn core1-btn-primary">
            <i class="fas fa-plus"></i>
            <span class="ml-2">Register Patient</span>
        </button>
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
                            @if(auth()->user()->isAdmin() || auth()->user()->isHeadNurse())
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
                                <div class="text-sm text-dark">
                                    {{ $patient->assignedNurse->name ?? 'Unassigned' }}
                                </div>
                            @endif
                        </td>
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
                                <a href="{{ route('core1.patients.show', $patient) }}" 
                                   class="core1-icon-action text-blue"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                @if(auth()->user()->role !== 'doctor')
                                    <a href="{{ route('core1.patients.edit', $patient) }}" 
                                       class="core1-icon-action text-orange"
                                       title="Edit Patient">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                {{-- Book Appointment --}}
                                <a href="{{ route('core1.appointments.create', ['patient_id' => $patient->id]) }}" 
                                   class="core1-icon-action text-purple"
                                   title="Book Appointment">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>

                                {{-- Delete --}}
                                @if(auth()->user()->role !== 'doctor')
                                    <form action="{{ route('core1.patients.destroy', $patient) }}" 
                                          method="POST"
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
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
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
                        
                        <!-- Name -->
                        <div class="relative col-span-2">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Full Name" required>
                            <label for="name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Full Name *</label>
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- DOB -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date of Birth" required>
                            <label for="date_of_birth" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Date of Birth *</label>
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
                            <label for="gender" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Gender *</label>
                            @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Phone Number" required>
                            <label for="phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Phone Number *</label>
                            @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Email Address" required>
                            <label for="email" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Email Address *</label>
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Address -->
                        <div class="relative col-span-2">
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Complete Address">
                            <label for="address" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Complete Address</label>
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
                            <label for="blood_type" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Blood Type</label>
                            @error('blood_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Allergies -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="allergies" id="allergies" value="{{ old('allergies') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Allergies (if any)">
                            <label for="allergies" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Allergies (if any)</label>
                            @error('allergies') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Medical History -->
                        <div class="relative col-span-2">
                            <textarea name="medical_history" id="medical_history" rows="2" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Pre-existing Conditions, Past Surgeries, etc.">{{ old('medical_history') }}</textarea>
                            <label for="medical_history" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Medical History</label>
                            @error('medical_history') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Section 3: Emergency Information -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">3. Emergency Information</h4>
                        </div>

                        <!-- Emergency Contact Name -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Emergency Contact Name">
                            <label for="emergency_contact_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Emergency Contact Name</label>
                            @error('emergency_contact_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Emergency Contact Phone -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Emergency Contact Phone">
                            <label for="emergency_contact_phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Emergency Contact Phone</label>
                            @error('emergency_contact_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Emergency Contact Relation -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Relationship to Patient">
                            <label for="emergency_contact_relation" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Relationship to Patient</label>
                            @error('emergency_contact_relation') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Section 4: Insurance Information -->
                        <div class="col-span-2 mt-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">4. Insurance Information</h4>
                        </div>

                        <!-- Insurance Provider -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Insurance Provider">
                            <label for="insurance_provider" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Insurance Provider</label>
                            @error('insurance_provider') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Policy Number -->
                        <div class="relative col-span-2 sm:col-span-1">
                            <input type="text" name="policy_number" id="policy_number" value="{{ old('policy_number') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Policy/Member Number">
                            <label for="policy_number" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Policy/Member Number</label>
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
    
    // Close modal when clicking outside
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
        @if($errors->has('name') || $errors->has('date_of_birth') || $errors->has('gender') || $errors->has('phone') || $errors->has('email'))
            openRegisterModal();
        @endif
    });
</script>
@endpush
