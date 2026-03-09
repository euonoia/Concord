@extends('core.core1.layouts.app')

@section('title', 'Patient Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Patient Details</h2>
            <p class="core1-subtitle">View patient information @if($patient->mrn) &bull; <span class="font-mono" style="color:#1a3a5a;">{{ $patient->mrn }}</span> @endif</p>
        </div>
        <div class="core1-flex-gap-2">
            <a href="{{ route('core1.patients.edit', $patient) }}" class="core1-btn core1-btn-primary">
                <i class="fas fa-edit"></i>
                <span class="pl-20">Edit Patient</span>
            </a>
            <form action="{{ route('core1.patients.destroy', $patient) }}" method="POST" class="core1-flex m-0" onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="core1-btn core1-btn-danger">
                    <i class="fas fa-trash"></i>
                    <span class="pl-20">Delete Patient</span>
                </button>
            </form>
            <a href="{{ route('core1.patients.index') }}" class="core1-btn core1-btn-outline">
                <i class="fas fa-arrow-left"></i>
                <span class="pl-20">Back to List</span>
            </a>
        </div>
    </div>

    <div class="core1-card core1-card-compact">
        <div class="core1-info-grid">
            <div class="core1-info-item">
                <h3>Patient ID</h3>
                <p>{{ $patient->patient_id ?? 'Not assigned' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>MRN</h3>
                @if($patient->mrn)
                    <p class="font-mono font-bold" style="color:#1a3a5a;">{{ $patient->mrn }}</p>
                @else
                    <p class="text-gray-400 text-sm italic">Not yet assigned</p>
                @endif
            </div>
            <div class="core1-info-item">
                <h3>Registration Status</h3>
                <span class="core1-badge {{ ($patient->registration_status ?? '') === 'REGISTERED' ? 'core1-badge-active' : 'core1-badge-inactive' }}">
                    {{ str_replace('_', ' ', $patient->registration_status ?? 'REGISTERED') }}
                </span>
            </div>
            <div class="core1-info-item">
                <h3>Name</h3>
                <p>{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Birth Date</h3>
                <p>{{ $patient->date_of_birth->format('M d, Y') }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Gender</h3>
                <p>{{ ucfirst($patient->gender) }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Email</h3>
                <p>{{ $patient->email }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Age</h3>
                <p>{{ $patient->age }} years</p>
            </div>
            <div class="core1-info-item">
                <h3>Phone</h3>
                <p>{{ $patient->phone }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Address</h3>
                <p>{{ $patient->address ?? 'N/A' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Assigned Nurse</h3>
                <p>{{ $patient->assignedNurse->name ?? 'None' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Status</h3>
                <div>
                    <span class="core1-badge {{ $patient->status === 'active' ? 'core1-badge-active' : 'core1-badge-inactive' }}">
                        {{ ucfirst($patient->status) }}
                    </span>
                </div>
            </div>

            {{-- 2. Medical Info --}}
            <div class="core1-col-span-2 mt-4">
                <h4 class="text-xs font-bold text-green-600 uppercase tracking-wider border-b pb-2 mb-2">Medical Information</h4>
            </div>
            <div class="core1-info-item">
                <h3>Blood Type</h3>
                <p>{{ $patient->blood_type ?? 'Unknown' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Allergies</h3>
                <p>{{ $patient->allergies ?? 'None' }}</p>
            </div>
            <div class="core1-info-item core1-col-span-2">
                <h3>Medical History</h3>
                <p>{{ $patient->medical_history ?? 'None' }}</p>
            </div>

            {{-- 3. Emergency Info --}}
            <div class="core1-col-span-2 mt-4">
                <h4 class="text-xs font-bold text-orange-600 uppercase tracking-wider border-b pb-2 mb-2">Emergency Contact</h4>
            </div>
            <div class="core1-info-item">
                <h3>Contact Name</h3>
                <p>{{ $patient->emergency_contact_name ?? '---' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Relationship</h3>
                <p>{{ $patient->emergency_contact_relation ?? '---' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Phone</h3>
                <p>{{ $patient->emergency_contact_phone ?? '---' }}</p>
            </div>

            {{-- 4. Insurance Info --}}
            <div class="core1-col-span-2 mt-4">
                <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider border-b pb-2 mb-2">Insurance Information</h4>
            </div>
            <div class="core1-info-item">
                <h3>Provider</h3>
                <p>{{ $patient->insurance_provider ?? '---' }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Policy Number</h3>
                <p>{{ $patient->policy_number ?? '---' }}</p>
            </div>
            
            @if($patient->last_visit)
            <div class="core1-info-item">
                <h3>Last Visit</h3>
                <p>{{ $patient->last_visit->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="core1-form-actions">
        <a href="{{ route('core1.appointments.create', ['patient_id' => $patient->id]) }}" class="core1-btn core1-btn-success">
            <i class="fas fa-calendar-plus"></i>
            <span class="pl-20">Book Appointment</span>
        </a>
        <a href="{{ route('core1.medical-records.index', ['patient' => $patient->id]) }}" class="core1-btn core1-btn-outline">
            <i class="fas fa-file-medical"></i>
            <span class="pl-20">View Medical Records</span>
        </a>
    </div>
</div>
@endsection


