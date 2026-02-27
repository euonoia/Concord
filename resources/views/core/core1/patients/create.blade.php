@extends('layouts.core1.layouts.app')

@section('title', 'Register New Patient')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-header">
        <h1 class="core1-title">Register New Patient</h1>
        <p class="core1-subtitle">Add a new patient to the system</p>
    </div>

    <div class="core1-card core1-card-compact">
        <form action="{{ route('core1.patients.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2 mt-4">
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

            <div class="core1-form-actions mt-8 pt-4 border-t border-gray-200">
                <button type="submit" class="core1-btn core1-btn-primary" style="background-color: #1a3a5a; color: white;">
                    Register Patient
                </button>
                <a href="{{ route('core1.patients.index') }}" class="core1-btn core1-btn-outline">
                    Cancel
                </a>
            </div>
        </form>
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
</script>
@endpush


