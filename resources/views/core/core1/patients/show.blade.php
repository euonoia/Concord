@extends('layouts.core1.layouts.app')

@section('title', 'Patient Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Patient Details</h2>
            <p class="core1-subtitle">View patient information</p>
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
                <p>{{ $patient->patient_id }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Name</h3>
                <p>{{ $patient->name }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Date of Birth</h3>
                <p>{{ $patient->date_of_birth->format('M d, Y') }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Age</h3>
                <p>{{ $patient->age ?? 'N/A' }} years</p>
            </div>
            <div class="core1-info-item">
                <h3>Gender</h3>
                <p>{{ ucfirst($patient->gender) }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Phone</h3>
                <p>{{ $patient->phone }}</p>
            </div>
            <div class="core1-info-item">
                <h3>Email</h3>
                <p>{{ $patient->email }}</p>
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
            @if($patient->address)
            <div class="core1-info-item core1-col-span-2">
                <h3>Address</h3>
                <p>{{ $patient->address }}</p>
            </div>
            @endif
            @if($patient->blood_type)
            <div class="core1-info-item">
                <h3>Blood Type</h3>
                <p>{{ $patient->blood_type }}</p>
            </div>
            @endif
            @if($patient->allergies)
            <div class="core1-info-item">
                <h3>Allergies</h3>
                <p>{{ $patient->allergies }}</p>
            </div>
            @endif
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


