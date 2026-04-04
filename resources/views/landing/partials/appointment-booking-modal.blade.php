<!-- Appointment Modal -->
<div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <!-- Panel -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="open" x-trap.noscroll="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

            @if(session('success'))
                <div class="p-10 text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 mb-6">
                        <i class="bi bi-check-lg text-4xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Appointment Confirmed!</h3>
                    <p class="text-gray-700 font-medium mb-4">{{ session('success') }}</p>
                    <p class="text-gray-500 mb-8">We have sent a confirmation email to your inbox.</p>
                    <button @click="open = false" class="w-full rounded-lg bg-slate-900 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800 transition-colors">
                        Close
                    </button>
                </div>
            @else
                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold leading-6 text-gray-900" id="modal-title">Book an Appointment</h3>
                        <p class="mt-2 text-sm text-gray-500">Please fill out the form below to schedule your visit.</p>
                    </div>

                    @if($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6" id="booking-form"
                        @submit="submitted = true">
                        @csrf

                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                            <!-- Section 1: Patient Information -->
                            <div class="col-span-2">
                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">1. Patient Information</h4>
                            </div>

                            <!-- First Name -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="First Name" required>
                                <label for="first_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">First Name</label>
                                @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Middle Name -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Middle Name (Optional)">
                                <label for="middle_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Middle Name (Optional)</label>
                                @error('middle_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Last Name" required>
                                <label for="last_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Last Name</label>
                                @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date of Birth" required>
                                <label for="date_of_birth" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Date of Birth</label>
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
                                <label for="gender" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Gender</label>
                                @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Email Address" required>
                                <label for="email" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Email Address</label>
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Phone Number" required>
                                <label for="phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Phone Number</label>
                                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Address Street -->
                            <div class="relative col-span-2">
                                <input type="text" name="address_street" id="address_street" value="{{ old('address_street') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Street Address" required>
                                <label for="address_street" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Street Address</label>
                                @error('address_street') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Address City -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="address_city" id="address_city" value="{{ old('address_city') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="City" required>
                                <label for="address_city" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">City</label>
                                @error('address_city') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Address Zip -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="address_zip" id="address_zip" value="{{ old('address_zip') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Zip Code" required>
                                <label for="address_zip" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Zip Code</label>
                                @error('address_zip') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Section 2: Appointment Details -->
                            <div class="col-span-2 mt-4">
                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">2. Appointment Details</h4>
                            </div>

                            <!-- Service Type -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <select name="service_type" id="service_type"
                                    @change="
                                        selectedDoctor = '';
                                        selectedSpecialization = '';
                                        fetchDoctors($event.target.value);
                                    "
                                    class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent" required>
                                    <option value="" disabled {{ old('service_type') ? '' : 'selected' }} class="text-gray-500">Select Service Type</option>
                                    <option value="general_consultation" {{ old('service_type') == 'general_consultation' ? 'selected' : '' }}>General Checkup</option>
                                    <option value="acute_care" {{ old('service_type') == 'acute_care' ? 'selected' : '' }}>Sick Visit</option>
                                    <option value="well_child" {{ old('service_type') == 'well_child' ? 'selected' : '' }}>Pedia / Baby Check</option>
                                    <option value="followup" {{ old('service_type') == 'followup' ? 'selected' : '' }}>Follow-up</option>
                                    <option value="prescription_refill" {{ old('service_type') == 'prescription_refill' ? 'selected' : '' }}>Refill</option>
                                    <option value="diagnostic" {{ old('service_type') == 'diagnostic' ? 'selected' : '' }}>Lab / Test</option>
                                    <option value="mental_health" {{ old('service_type') == 'mental_health' ? 'selected' : '' }}>Talk Therapy / Mental Health</option>
                                </select>
                                <label for="service_type" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">Service Type</label>
                                @error('service_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Doctor Selection (Conditional) -->
                            <div x-show="showDoctor" x-transition class="relative col-span-2 sm:col-span-1">
                                <select name="doctor_name" id="doctor_name"
                                    :disabled="!showDoctor || loadingDoctors"
                                    x-model="selectedDoctor"
                                    @change="updateSpecialization($event)"
                                    class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="loadingDoctors">
                                        <option value="" selected>Loading doctors...</option>
                                    </template>
                                    <template x-if="!loadingDoctors && doctors.length === 0">
                                        <option value="" selected>No doctors available for this service</option>
                                    </template>
                                    <template x-if="!loadingDoctors && doctors.length > 0">
                                        <option value="" selected>Select available doctor (Optional)</option>
                                    </template>
                                    <template x-for="doctor in doctors" :key="doctor.id">
                                        <option :value="doctor.id" :data-specialization="doctor.specialization" x-text="doctor.name + ' - ' + doctor.specialization"></option>
                                    </template>
                                </select>
                                <!-- Hidden field for specialization -->
                                <input type="hidden" name="specialization" :value="selectedSpecialization">
                                <label for="doctor_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">
                                    <span x-show="!loadingDoctors">Select Doctor</span>
                                    <span x-show="loadingDoctors" class="flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Loading...
                                    </span>
                                </label>
                                @error('doctor_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="date" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}"
                                    @change="fetchSlots()"
                                    class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date" required>
                                <label for="appointment_date" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Appointment Date</label>
                                @error('appointment_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Time -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <select name="appointment_time" id="appointment_time" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm bg-transparent" required>
                                    <option value="" disabled selected x-text="slots.length === 0 ? 'Select Date & Doctor First' : 'Select Time Slot'"></option>
                                    <template x-for="slot in slots" :key="slot.time">
                                        <option :value="slot.time" :disabled="slot.status === 'booked'" x-text="slot.time + ' (' + slot.status + ')'"></option>
                                    </template>
                                </select>
                                <label for="appointment_time" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-focus:text-blue-600">
                                    <span x-show="!loadingSlots">Preferred Time</span>
                                    <span x-show="loadingSlots" class="flex items-center gap-1">Checking...</span>
                                </label>
                                <p x-show="slotsMsg" x-text="slotsMsg" class="mt-1 text-xs text-blue-600"></p>
                                @error('appointment_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Reason for Visit -->
                            <div class="relative col-span-2">
                                <textarea name="reason_for_visit" id="reason_for_visit" rows="3" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Reason for Visit/Symptoms" required>{{ old('reason_for_visit') }}</textarea>
                                <label for="reason_for_visit" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Reason for Visit/Symptoms</label>
                                @error('reason_for_visit') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Section 3: Medical History & Insurance -->
                            <div class="col-span-2 mt-4">
                                <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">3. Medical History & Insurance</h4>
                            </div>

                            <!-- Insurance Provider -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="insurance_provider" id="insurance_provider" value="{{ old('insurance_provider') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Insurance Provider">
                                <label for="insurance_provider" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Insurance Provider</label>
                                @error('insurance_provider') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Policy Number -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <input type="text" name="policy_number" id="policy_number" value="{{ old('policy_number') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Policy/Member Number">
                                <label for="policy_number" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Policy/Member Number</label>
                                @error('policy_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Medical History Summary -->
                            <div class="relative col-span-2">
                                <textarea name="medical_history_summary" id="medical_history_summary" rows="3" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Medical History Summary (Allergies, Medications, etc.)">{{ old('medical_history_summary') }}</textarea>
                                <label for="medical_history_summary" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-base text-gray-500 bg-white px-1 duration-300 peer-placeholder-shown:top-3.5 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-focus:top-1 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:bg-white peer-focus:px-1">Medical History Summary</label>
                                @error('medical_history_summary') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>



                            <div class="col-span-2">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input id="terms" name="terms" type="checkbox"
                                            class="h-5 w-5 rounded text-blue-600 focus:ring-blue-500 cursor-pointer"
                                            style="border: 2px solid #1e293b !important; appearance: checkbox !important; -webkit-appearance: checkbox !important; opacity: 1 !important; visibility: visible !important;"
                                            x-model="agreedToTerms"
                                            required>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="font-medium text-gray-700">I agree to the cancellation policies and privacy notices.</label>
                                        <p class="text-gray-500">By booking this appointment, you agree to our terms of service.</p>
                                    </div>
                                </div>
                                @error('terms') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div x-show="agreedToTerms" x-transition>
                            <div class="mt-8 flex justify-center">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            </div>
                            @error('g-recaptcha-response') <p class="mt-2 text-xs text-red-500 text-center">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="w-full rounded-lg bg-[#1a3a5a] px-5 py-3 text-center text-sm font-semibold text-white shadow-md hover:bg-[#142d45] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed" :disabled="submitted">
                                <span x-show="!submitted">Confirm My Booking</span>
                                <span x-show="submitted" style="display: none;" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 rounded-b-2xl">
                    <p class="text-xs text-center text-gray-500 flex items-center justify-center gap-1">
                        <i class="bi bi-lock-fill"></i> Your data is secure and encrypted.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>