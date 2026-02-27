<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concord Hospital</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?fam ily=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
        <!-- Theme + Landing Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>
<body x-data="{ 
    open: {{ $errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('service_type') || $errors->has('appointment_date') || $errors->has('appointment_time') || $errors->has('g-recaptcha-response') || session('success') ? 'true' : 'false' }}, 
    submitted: false, 
    showDoctor: {{ old('service_type') ? 'true' : 'false' }},
    doctors: [],
    loadingDoctors: false,
    selectedDoctor: '{{ old('doctor_name') }}',
    selectedSpecialization: '{{ old('specialization') }}',
    showDetails: {{ session('tracked_appointment') ? 'true' : 'false' }},
    showCancelConfirm: false,
    
    // AJAX Tracking State
    trackingReference: '',
    trackedAppointment: @json(session('tracked_appointment')),
    trackingLoading: false,
    trackingError: '',
    cancelLoading: false,
    cancelError: '',
    cancelSuccess: '{{ session('cancel_success') }}',

    async trackAppointment() {
        if (!this.trackingReference) return;
        this.trackingLoading = true;
        this.trackingError = '';
        this.cancelSuccess = '';
        
        try {
            const response = await fetch('{{ route('appointments.lookup') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ appointment_reference: this.trackingReference })
            });

            const data = await response.json();

            if (data.success) {
                this.trackedAppointment = data.appointment;
                this.showDetails = true;
                this.showCancelConfirm = false;
            } else {
                this.trackingError = data.message || 'No appointment found.';
            }
        } catch (error) {
            this.trackingError = 'An error occurred. Please try again.';
        } finally {
            this.trackingLoading = false;
        }
    },

    async cancelAppointment() {
        if (!this.trackedAppointment) return;
        this.cancelLoading = true;
        this.cancelError = '';
        this.cancelSuccess = '';

        const reason = document.getElementById('ajax_cancellation_reason')?.value || '';
        
        try {
            const response = await fetch(`/appointments/track/${this.trackedAppointment.appointment_no}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cancellation_reason: reason })
            });

            const data = await response.json();

            if (data.success) {
                this.trackedAppointment.status = data.appointment.status;
                this.trackedAppointment.cancellation_reason = data.appointment.cancellation_reason;
                this.cancelSuccess = data.message;
                this.showCancelConfirm = false;
            } else {
                this.cancelError = data.message || 'Could not cancel appointment.';
            }
        } catch (error) {
            this.cancelError = 'An error occurred. Please try again.';
        } finally {
            this.cancelLoading = false;
        }
    }
}" @keydown.escape="open = false; showDetails = false; showCancelConfirm = false" class="relative">
<!-- Header -->
<header>
    <div class="container">
        <nav>
            <div class="logo">Concord Hospital</div>
            <ul>
                <li>
                    <a href="#home" class="btn btn-call">
                        <i class="bi bi-telephone-fill"></i> Call Us
                    </a>
                </li>
                <li>
                    <a href="#appointments" @click.prevent="open = true" class="btn btn-book">
                        <i class="bi bi-calendar-check-fill"></i> Book Now
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<!-- Sub Navigation -->
<div class="sub-nav">
    <div class="container">
        <a href="#home" class="sub-link">Home</a>
        <a href="#doctors" class="sub-link">Doctors</a>
        <a href="#appointments" class="sub-link">Appointments</a>
        <a href="#careers" class="sub-link">Careers</a>
    </div>
</div>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container hero-content">
        <div class="hero-text">
            <h1>Trusted Care for Every Patient</h1>
            <p>
                Concord Hospital delivers compassionate, patient-centered
                healthcare with modern facilities and experienced professionals.
            </p>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/achievment.jpeg') }}" alt="Healthcare Professionals">
        </div>
    </div>
</section>


<section id="appointments" class="container appointments-section">

    <div class="appointments-header">
        <h2>Book an Appointment</h2>
        <p>Schedule your consultation with our specialists quickly and easily.</p>
    </div>

    <div class="appointment-lookup-row">
        <button @click="open = true" class="btn btn-book px-8 py-4 text-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <i class="bi bi-calendar-check-fill mr-2"></i> Book Appointment Now
        </button>

        <div class="appointment-lookup-divider">
            <span>or</span>
        </div>
        <div class="appointment-tracking-header">
            <p>Track your appointment or cancel it.</p>
        </div>

        <form @submit.prevent="trackAppointment()" class="appointment-lookup-form">
            <div class="appointment-lookup-input-group">
                <input type="text" x-model="trackingReference" placeholder="Enter your reference number" class="appointment-lookup-input" required>
                <button type="submit" class="btn btn-track" :disabled="trackingLoading">
                    <i x-show="!trackingLoading" class="bi bi-search mr-1"></i>
                    <svg x-show="trackingLoading" style="display: none;" class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Track Appointment
                </button>
            </div>
            <p x-show="trackingError" x-text="trackingError" style="display: none;" class="appointment-lookup-error"></p>
        </form>
    </div>

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
                        <p class="text-gray-500 mb-8">Thank you for booking with us. We have sent a confirmation email to your inbox.</p>
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

                        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6" id="booking-form"
                            @submit.prevent="
                                submitted = true;
                                grecaptcha.ready(function() {
                                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'confirm_booking'}).then(function(token) {
                                        document.getElementById('g-recaptcha-response').value = token;
                                        document.getElementById('booking-form').submit();
                                    }).catch(function() {
                                        submitted = false;
                                        alert('reCAPTCHA failed to load. Please refresh and try again.');
                                    });
                                });
                            ">
                            @csrf
                            
                            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <!-- Section 1: Patient Information -->
                                <div class="col-span-2">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">1. Patient Information</h4>
                                </div>

                                <!-- Name -->
                                <div class="relative col-span-2">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Full Name" required>
                                    <label for="name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Full Name</label>
                                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date of Birth" required>
                                    <label for="date_of_birth" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Date of Birth</label>
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
                                    <label for="gender" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Gender</label>
                                    @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Email -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Email Address" required>
                                    <label for="email" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Email Address</label>
                                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Phone -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Phone Number" required>
                                    <label for="phone" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Phone Number</label>
                                    @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Address Street -->
                                <div class="relative col-span-2">
                                    <input type="text" name="address_street" id="address_street" value="{{ old('address_street') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Street Address" required>
                                    <label for="address_street" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Street Address</label>
                                    @error('address_street') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Address City -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="text" name="address_city" id="address_city" value="{{ old('address_city') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="City" required>
                                    <label for="address_city" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">City</label>
                                    @error('address_city') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Address Zip -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="text" name="address_zip" id="address_zip" value="{{ old('address_zip') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Zip Code" required>
                                    <label for="address_zip" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Zip Code</label>
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
                                            showDoctor = $event.target.value !== '';
                                            selectedDoctor = '';
                                            selectedSpecialization = '';
                                            loadingDoctors = true;
                                            fetch('/api/doctors/by-service-type?service_type=' + $event.target.value)
                                                .then(res => res.json())
                                                .then(data => {
                                                    doctors = data.doctors || [];
                                                    loadingDoctors = false;
                                                })
                                                .catch(err => {
                                                    console.error('Error fetching doctors:', err);
                                                    doctors = [];
                                                    loadingDoctors = false;
                                                });
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
                                    <label for="service_type" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Service Type</label>
                                    @error('service_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Doctor Selection (Conditional) -->
                                <div x-show="showDoctor" x-transition class="relative col-span-2 sm:col-span-1">
                                    <select name="doctor_name" id="doctor_name" 
                                        :disabled="!showDoctor || loadingDoctors"
                                        x-model="selectedDoctor"
                                        @change="
                                            const selectedOption = $event.target.selectedOptions[0];
                                            selectedSpecialization = selectedOption ? selectedOption.dataset.specialization : '';
                                        "
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
                                            <option :value="doctor.name" :data-specialization="doctor.specialization" x-text="doctor.name + ' - ' + doctor.specialization"></option>
                                        </template>
                                    </select>
                                    <!-- Hidden field for specialization -->
                                    <input type="hidden" name="specialization" :value="selectedSpecialization">
                                    <label for="doctor_name" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">
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
                                    <input type="date" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Date" required>
                                    <label for="appointment_date" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Appointment Date</label>
                                    @error('appointment_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Time -->
                                <div class="relative col-span-2 sm:col-span-1">
                                    <input type="time" name="appointment_time" id="appointment_time" value="{{ old('appointment_time') }}" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Time" required>
                                    <label for="appointment_time" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Preferred Time</label>
                                    @error('appointment_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Reason for Visit -->
                                <div class="relative col-span-2">
                                    <textarea name="reason_for_visit" id="reason_for_visit" rows="3" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Reason for Visit/Symptoms" required>{{ old('reason_for_visit') }}</textarea>
                                    <label for="reason_for_visit" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Reason for Visit/Symptoms</label>
                                    @error('reason_for_visit') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Section 3: Medical History & Insurance -->
                                <div class="col-span-2 mt-4">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">3. Medical History & Insurance</h4>
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

                                <!-- Medical History Summary -->
                                <div class="relative col-span-2">
                                    <textarea name="medical_history_summary" id="medical_history_summary" rows="3" class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-blue-600 focus:ring-blue-600 placeholder-transparent sm:text-sm" placeholder="Medical History Summary (Allergies, Medications, etc.)">{{ old('medical_history_summary') }}</textarea>
                                    <label for="medical_history_summary" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-gray-500 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-blue-600">Medical History Summary</label>
                                    @error('medical_history_summary') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <!-- Section 4: Policies -->
                                <div class="col-span-2 mt-4">
                                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2 border-b pb-1">4. Administrative</h4>
                                </div>

                                <div class="col-span-2">
                                    <div class="flex items-start">
                                        <div class="flex h-5 items-center">
                                            <input id="terms" name="terms" type="checkbox" 
                                                class="h-5 w-5 rounded text-blue-600 focus:ring-blue-500 cursor-pointer" 
                                                style="border: 2px solid #1e293b !important; appearance: checkbox !important; -webkit-appearance: checkbox !important; opacity: 1 !important; visibility: visible !important;"
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

                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                            <div class="mt-8">
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
                                @error('g-recaptcha-response') <p class="mt-2 text-xs text-red-500 text-center">{{ $message }}</p> @enderror
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

    <!-- Appointment Details Modal -->
    <template x-if="trackedAppointment">
    <div x-show="showDetails" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div x-show="showDetails" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showDetails = false; showCancelConfirm = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showDetails" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                <!-- Details View -->
                <div x-show="!showCancelConfirm">
                    <div class="px-6 pt-6 pb-2">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">Appointment Details</h3>
                                <p class="mt-1 text-sm text-gray-500">Reference: <strong x-text="trackedAppointment.appointment_no"></strong></p>
                            </div>
                            <span class="status-badge" :class="'status-' + trackedAppointment.status" x-text="trackedAppointment.status.charAt(0).toUpperCase() + trackedAppointment.status.slice(1)"></span>
                        </div>

                        <div x-show="cancelSuccess" style="display: none;" class="p-3 mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm flex items-center gap-2">
                            <i class="bi bi-check-circle-fill text-green-600"></i>
                            <span x-text="cancelSuccess"></span>
                        </div>

                        <div x-show="cancelError" style="display: none;" class="p-3 mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm flex items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill text-red-600"></i>
                            <span x-text="cancelError"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-person-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Patient Name</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.name"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-calendar-heart"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Date of Birth</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.date_of_birth"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-gender-ambiguous"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Gender</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.gender"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Address</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.address_street + ', ' + trackedAppointment.address_city + ' ' + trackedAppointment.address_zip"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Doctor</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.doctor_name || 'Not assigned'"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-calendar-event-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Date</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.appointment_date"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-clock-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Time</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.appointment_time"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Service Type</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.service_type"></span>
                                </div>
                            </div>
                            <div class="appointment-detail-item col-span-1 sm:col-span-2">
                                <div class="appointment-detail-icon"><i class="bi bi-chat-left-text-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Reason for Visit</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.reason_for_visit"></span>
                                </div>
                            </div>
                            <template x-if="trackedAppointment.insurance_provider">
                                <div class="appointment-detail-item">
                                    <div class="appointment-detail-icon"><i class="bi bi-shield-check"></i></div>
                                    <div>
                                        <span class="appointment-detail-label">Insurance</span>
                                        <span class="appointment-detail-value" x-text="trackedAppointment.insurance_provider + ' (' + trackedAppointment.policy_number + ')'"></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="trackedAppointment.medical_history_summary">
                                <div class="appointment-detail-item col-span-1 sm:col-span-2">
                                    <div class="appointment-detail-icon"><i class="bi bi-file-earmark-medical"></i></div>
                                    <div>
                                        <span class="appointment-detail-label">Medical History Summary</span>
                                        <span class="appointment-detail-value" x-text="trackedAppointment.medical_history_summary"></span>
                                    </div>
                                </div>
                            </template>
                            <div class="appointment-detail-item">
                                <div class="appointment-detail-icon"><i class="bi bi-info-circle-fill"></i></div>
                                <div>
                                    <span class="appointment-detail-label">Status</span>
                                    <span class="appointment-detail-value" x-text="trackedAppointment.status.charAt(0).toUpperCase() + trackedAppointment.status.slice(1)"></span>
                                </div>
                            </div>
                        </div>

                        <div x-show="trackedAppointment.status === 'cancelled' && trackedAppointment.cancellation_reason" style="display: none;" class="p-3 mt-4 rounded-lg bg-gray-50 border border-gray-200">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Cancellation Reason</p>
                            <p class="text-sm text-gray-500" x-text="trackedAppointment.cancellation_reason"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-2xl">
                        <button @click="showDetails = false; showCancelConfirm = false" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                        <template x-if="['pending', 'approved'].includes(trackedAppointment.status)">
                            <button @click="showCancelConfirm = true" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors flex items-center gap-1">
                                <i class="bi bi-x-circle"></i> Cancel Appointment
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Cancel Confirmation Panel -->
                <div x-show="showCancelConfirm" x-transition style="display: none;">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Cancel Appointment</h3>
                                <p class="text-sm text-gray-500">This action cannot be undone.</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Are you sure you want to cancel appointment <strong x-text="trackedAppointment.appointment_no"></strong>?</p>
                        <div>
                            <label for="ajax_cancellation_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for cancellation (optional)</label>
                            <textarea id="ajax_cancellation_reason" rows="3" maxlength="1000" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Let us know why you're cancelling..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="showCancelConfirm = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors" :disabled="cancelLoading">
                            Keep Appointment
                        </button>
                        <button type="button" @click="cancelAppointment()" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors flex items-center gap-1" :disabled="cancelLoading">
                            <svg x-show="cancelLoading" class="animate-spin h-4 w-4 mr-1 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="cancelLoading ? 'Cancelling...' : 'Yes, Cancel Appointment'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </template>

</section>


<section id="doctors" class="container doctors-section">

    <div class="doctors-header">
        <h2>Multi-awarded Doctors</h2>
        <p>Meet our top medical professionals who deliver excellence in healthcare.</p>
    </div>

    <div class="doctors-list">
        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/robert.jpeg') }}" alt="Dr. Rober">
            <h4>Dr. Robert</h4>
            <p>Cardiology</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/gilbert.jpeg') }}" alt="Dr. Kathy">
            <h4>Dr. Kathy</h4>
            <p>Neurology</p>
        </div>

        <!-- Highlighted Doctor in the middle -->
        <div class="doctor-card highlight">
            <img src="{{ asset('images/onboarding/kim.jpeg') }}" alt="Dr. Kim">
            <h4>Dr. Kim</h4>
            <p>Chief Surgeon</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/kubica.jpeg') }}" alt="Dr. Damian">
            <h4>Dr. Damian</h4>
            <p>Pediatrics</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/lebron.jpeg') }}" alt="Dr. Joshua">
            <h4>Dr. Joshua</h4>
            <p>Orthopedics</p>
        </div>
    </div>

</section>


   <section id="careers" class="container careers-section">

    <!-- Section Title -->
    <div class="careers-header">
        <h2>Careers</h2>
        <p>Join our dedicated healthcare team and make a real difference in patients' lives.</p>
    </div>

    <!-- Image + Text -->
    <div class="careers-content">
        <div class="careers-image">
            <img src="{{ asset('images/career.jpeg') }}" alt="Careers at Concord Hospital">
        </div>
        <div class="careers-text">
            <h3>Why Work With Us?</h3>
            <p>At Concord Hospital, we value compassion, excellence, and collaboration. We provide a supportive environment where healthcare professionals can grow, innovate, and deliver the best care to our patients.</p>
            <p>We offer opportunities across clinical, administrative, and support roles, ensuring a fulfilling career path for everyone.</p>
        </div>
    </div>

        <div class="careers-cards">
            <div class="career-card">
                <i class="bi bi-person-badge"></i>
                <h4>Consultancy</h4>
                <p>Deliver compassionate patient care in a supportive environment.</p>
            </div>

        <a href="{{ route('careers.residency') }}" class="text-decoration-none text-dark">
            <div class="career-card hover-card">
                <i class="bi bi-clipboard2-pulse"></i>
                <h4>Residency & Fellowship</h4>
                <p>Apply for our 2026-2027 clinical tracks. Advance your specialization with elite mentorship and research opportunities.</p>
            </div>
        </a>
            <div class="career-card">
                <i class="bi bi-journal-text"></i>
                <h4>Internship</h4>
                <p>Support hospital operations and ensure quality healthcare delivery.</p>
            </div>
        </div>


</section>


<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; 2025 CityCare Hospital. All rights reserved.</p>
        <p>
            <a href="#privacy">Privacy Policy</a> |
            <a href="#terms">Terms of Service</a>
        </p>
    </div>
</footer>

<!-- Sub NavLink Highlight JS -->
<script>
    const sections = document.querySelectorAll("section[id]");
    const subLinks = document.querySelectorAll(".sub-link");

    window.addEventListener("scroll", () => {
        let scrollPos = window.scrollY + 150;
        sections.forEach(section => {
            if(scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight){
                subLinks.forEach(link => {
                    link.classList.remove("active");
                    if(link.getAttribute("href") === "#" + section.id){
                        link.classList.add("active")
                    }
                });
            }
        });
    });
</script>

</body>
</html>
