@extends('core.core1.layouts.app')

@section('title', 'Complete Patient Registration')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">

    <div class="core1-header">
        <h1 class="core1-title">Complete Patient Registration</h1>
        <p class="core1-subtitle">
            Completing registration for:
            <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>
        </p>
    </div>

    {{-- Pre-registration info summary --}}
    <div class="core1-card core1-card-compact mb-20" style="border-left:4px solid #d97706;">
        <div class="d-flex items-center gap-3 mb-10">
            <i class="fas fa-info-circle" style="color:#d97706;"></i>
            <h4 class="text-sm font-bold text-gray-700">Pre-Registration Info (from online booking)</h4>
        </div>
        <div class="core1-info-grid">
            <div class="core1-info-item">
                <h3>Name</h3>
                <p>{{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}</p>
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
                <h3>Status</h3>
                <span class="core1-badge core1-badge-inactive">PRE REGISTERED</span>
            </div>
        </div>
    </div>

    {{-- Completion form --}}
    <div class="core1-card core1-card-compact">
        <form action="{{ route('core1.patients.do-complete-registration', $patient) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2 mt-4">

                <div class="col-span-2">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">
                        Complete Demographics
                    </h4>
                </div>

                {{-- First Name --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="first_name" id="first_name"
                           value="{{ old('first_name', $patient->first_name) }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="First Name" required>
                    <label for="first_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">First Name *</label>
                    @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Middle Name --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="middle_name" id="middle_name"
                           value="{{ old('middle_name', $patient->middle_name) }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Middle Name">
                    <label for="middle_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Middle Name</label>
                </div>

                {{-- Last Name --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="last_name" id="last_name"
                           value="{{ old('last_name', $patient->last_name) }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Last Name" required>
                    <label for="last_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Last Name *</label>
                    @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Date of Birth --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Date of Birth" required>
                    <label for="date_of_birth" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Date of Birth *</label>
                    @error('date_of_birth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Gender --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <select name="gender" id="gender"
                            class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 sm:text-sm bg-transparent" required>
                        <option value="" disabled {{ old('gender', $patient->gender) ? '' : 'selected' }}>Select Gender</option>
                        <option value="male"   {{ old('gender', $patient->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender', $patient->gender) == 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                    <label for="gender" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Gender *</label>
                    @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Phone (readonly — from booking) --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="tel" name="phone" id="phone"
                           value="{{ $patient->phone }}"
                           class="peer block w-full rounded-lg bg-gray-50 border-gray-300 px-3 pt-5 pb-2 text-gray-500 sm:text-sm"
                           placeholder="Phone" readonly>
                    <label for="phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300">Phone (from booking)</label>
                </div>

                {{-- Email (readonly — from booking) --}}
                <div class="relative col-span-2 sm:col-span-1">
                    <input type="email" name="email" id="email"
                           value="{{ $patient->email }}"
                           class="peer block w-full rounded-lg bg-gray-50 border-gray-300 px-3 pt-5 pb-2 text-gray-500 sm:text-sm"
                           placeholder="Email" readonly>
                    <label for="email" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300">Email (from booking)</label>
                </div>

                {{-- Address --}}
                <div class="relative col-span-2">
                    <input type="text" name="address" id="address"
                           value="{{ old('address', $patient->address) }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Complete Address">
                    <label for="address" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Complete Address</label>
                </div>

                {{-- 3. Emergency Information --}}
                <div class="col-span-2 mt-4">
                    <h4 class="text-sm font-bold text-orange-700 uppercase tracking-wider mb-2 border-b pb-1">
                        3. Emergency Information
                    </h4>
                </div>

                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                           value="{{ old('emergency_contact_name') }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Contact Name">
                    <label for="emergency_contact_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Contact Name</label>
                </div>

                <div class="relative col-span-2 sm:col-span-1">
                    <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone"
                           value="{{ old('emergency_contact_phone') }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Contact Phone">
                    <label for="emergency_contact_phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Contact Phone</label>
                </div>

                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="emergency_contact_relation" id="emergency_contact_relation"
                           value="{{ old('emergency_contact_relation') }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Relationship">
                    <label for="emergency_contact_relation" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Relationship (e.g. Spouse)</label>
                </div>

                {{-- 4. Insurance Information --}}
                <div class="col-span-2 mt-4">
                    <h4 class="text-sm font-bold text-purple-700 uppercase tracking-wider mb-2 border-b pb-1">
                        4. Insurance Information
                    </h4>
                </div>

                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="insurance_provider" id="insurance_provider"
                           value="{{ old('insurance_provider') }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Insurance Provider">
                    <label for="insurance_provider" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Insurance Provider</label>
                </div>

                <div class="relative col-span-2 sm:col-span-1">
                    <input type="text" name="policy_number" id="policy_number"
                           value="{{ old('policy_number') }}"
                           class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm"
                           placeholder="Policy Number">
                    <label for="policy_number" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Policy Number</label>
                </div>

                {{-- MRN Notice --}}
                <div class="col-span-2">
                    <div class="rounded-lg p-3 d-flex items-center gap-3" style="background-color:#eff6ff;border:1px solid #bfdbfe;">
                        <i class="fas fa-id-card" style="color:#2563eb;"></i>
                        <p class="text-sm" style="color:#1e40af;">
                            A unique MRN will be automatically generated upon completing this registration.
                        </p>
                    </div>
                </div>

            </div>

            <div class="core1-form-actions mt-8 pt-4 border-t border-gray-200">
                <button type="submit" class="core1-btn core1-btn-primary" style="background-color:#059669;color:white;">
                    <i class="fas fa-user-check"></i>
                    <span class="pl-20">Complete Registration & Generate MRN</span>
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
