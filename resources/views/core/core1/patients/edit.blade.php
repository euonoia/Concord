@extends('core.core1.layouts.app')

@section('title', 'Edit Patient')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-header">
        <h2 class="core1-title">Edit Patient</h2>
        <p class="core1-subtitle">Update patient information</p>
    </div>

    <div class="core1-card core1-card-compact">
        <form action="{{ route('core1.patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="core1-form-grid">
                {{-- 1. Demographics --}}
                <div class="core1-col-span-2">
                    <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider border-b pb-2 mb-4">1. Demographics</h4>
                </div>

                <div class="core1-form-group">
                    <label for="first_name" class="core1-label">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required
                           class="core1-input @error('first_name') core1-input-error @enderror">
                    @error('first_name') <p class="core1-error-text">{{ $message }}</p> @enderror
                </div>

                <div class="core1-form-group">
                    <label for="middle_name" class="core1-label">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $patient->middle_name) }}"
                           class="core1-input @error('middle_name') core1-input-error @enderror">
                </div>

                <div class="core1-form-group">
                    <label for="last_name" class="core1-label">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required
                           class="core1-input @error('last_name') core1-input-error @enderror">
                    @error('last_name') <p class="core1-error-text">{{ $message }}</p> @enderror
                </div>

                <div class="core1-form-group">
                    <label for="date_of_birth" class="core1-label">Date of Birth *</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required
                           class="core1-input @error('date_of_birth') core1-input-error @enderror">
                </div>

                <div class="core1-form-group">
                    <label for="gender" class="core1-label">Gender *</label>
                    <select id="gender" name="gender" required class="core1-input">
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="core1-form-group">
                    <label for="phone" class="core1-label">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required class="core1-input">
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="email" class="core1-label">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $patient->email) }}" required class="core1-input">
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="address" class="core1-label">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $patient->address) }}" class="core1-input">
                </div>

                {{-- 2. Medical Info --}}
                <div class="core1-col-span-2 mt-4">
                    <h4 class="text-xs font-bold text-green-600 uppercase tracking-wider border-b pb-2 mb-4">2. Medical Information</h4>
                </div>
                
                <div class="core1-form-group">
                    <label for="blood_type" class="core1-label">Blood Type</label>
                    <select name="blood_type" id="blood_type" class="core1-input">
                        <option value="Unknown" {{ old('blood_type', $patient->blood_type) == 'Unknown' ? 'selected' : '' }}>Unknown</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" {{ old('blood_type', $patient->blood_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="core1-form-group">
                    <label for="allergies" class="core1-label">Allergies</label>
                    <input type="text" name="allergies" id="allergies" value="{{ old('allergies', $patient->allergies) }}" class="core1-input" placeholder="e.g. Peanuts, Penicillin">
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="medical_history" class="core1-label">Medical History</label>
                    <textarea name="medical_history" id="medical_history" class="core1-input" rows="2">{{ old('medical_history', $patient->medical_history) }}</textarea>
                </div>

                {{-- 3. Emergency Information --}}
                <div class="core1-col-span-2 mt-4">
                    <h4 class="text-xs font-bold text-orange-600 uppercase tracking-wider border-b pb-2 mb-4">3. Emergency Contact</h4>
                </div>

                <div class="core1-form-group">
                    <label for="emergency_contact_name" class="core1-label">Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="core1-input">
                </div>

                <div class="core1-form-group">
                    <label for="emergency_contact_phone" class="core1-label">Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="core1-input">
                </div>

                <div class="core1-form-group">
                    <label for="emergency_contact_relation" class="core1-label">Relationship</label>
                    <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}" class="core1-input" placeholder="e.g. Spouse, Parent">
                </div>

                {{-- 4. Insurance Information --}}
                <div class="core1-col-span-2 mt-4">
                    <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider border-b pb-2 mb-4">4. Insurance Information</h4>
                </div>

                <div class="core1-form-group">
                    <label for="insurance_provider" class="core1-label">Insurance Provider</label>
                    <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider', $patient->insurance_provider) }}" class="core1-input">
                </div>

                <div class="core1-form-group">
                    <label for="policy_number" class="core1-label">Policy Number</label>
                    <input type="text" name="policy_number" id="policy_number" value="{{ old('policy_number', $patient->policy_number) }}" class="core1-input">
                </div>
            </div>

            <div class="core1-form-actions">
                <button type="submit" class="core1-btn core1-btn-primary" style="background-color:#1a3a5a;color:white;">
                    Update Patient Record
                </button>
                <a href="{{ route('core1.patients.index') }}" class="core1-btn core1-btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


