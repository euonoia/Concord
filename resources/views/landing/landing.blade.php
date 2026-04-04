@extends('layouts.app')

@section('title', 'Concord Hospital')

@section('app_nav')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landing.css') }}">
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@section('body_class', 'bg-gray-50 text-gray-800 font-sans antialiased')


@section('content')
<div id="landing-app" data-page="landing/index" x-data="appointmentForm({ 
    open: @json($errors->any() || session('success')),
    showDoctor: {{ old('service_type') ? 'true' : 'false' }},
    selectedDoctor: '{{ old('doctor_name', '') }}',
    selectedSpecialization: '{{ old('specialization', '') }}',
    trackedAppointment: @json(session('tracked_appointment', null)),
    cancelSuccess: '{{ session('cancel_success', '') }}',
    lookupUrl: '{{ route('appointments.lookup') }}',
    cancelUrlFormat: '{{ route('appointments.cancel', ':id') }}',
    doctorsUrl: '{{ route('api.doctors.byServiceType') }}',
    checkAvailabilityUrl: '{{ route('api.appointments.checkAvailability') }}',
    csrfToken: '{{ csrf_token() }}'
})">
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

<div class="sub-nav">
    <div class="container">
        <a href="#home" class="sub-link">Home</a>
        <a href="#doctors" class="sub-link">Doctors</a>
        <a href="#appointments" class="sub-link">Appointments</a>
        <a href="#careers" class="sub-link">Careers</a>
    </div>
</div>

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
        
        @include('landing.partials.appointment-tracking-form')
        
    </div>

    @include('landing.partials.appointment-booking-modal')

    @include('landing.partials.appointment-details-modal')
</section>

<section id="doctors" class="container doctors-section">
    <div class="doctors-header">
        <h2>Multi-awarded Doctors</h2>
        <p>Meet our top medical professionals who deliver excellence in healthcare.</p>
    </div>

    <div class="doctors-list">
        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/robert.jpeg') }}" alt="Dr. Robert">
            <h4>Dr. Robert</h4>
            <p>Cardiology</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/gilbert.jpeg') }}" alt="Dr. Kathy">
            <h4>Dr. Kathy</h4>
            <p>Neurology</p>
        </div>

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
    <div class="careers-header">
        <h2>Careers</h2>
        <p>Join our dedicated healthcare team and make a real difference in patients' lives.</p>
    </div>

    <div class="careers-content">
        <div class="careers-image">
            <img src="{{ asset('images/career.jpeg') }}" alt="Careers at Concord Hospital">
        </div>
        <div class="careers-text">
            <h3>Why Work With Us?</h3>
            <p>At Concord Hospital, we value compassion, excellence, and collaboration. We provide a supportive environment where healthcare professionals can grow and innovate.</p>
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
                <p>Apply for our 2026-2027 clinical tracks. Advance your specialization with elite mentorship.</p>
            </div>
        </a>

        <div class="career-card">
            <i class="bi bi-journal-text"></i>
            <h4>Internship</h4>
            <p>Support hospital operations and ensure quality healthcare delivery.</p>
        </div>
    </div>
</section>

<div id="chatbot-wrapper">
    <button id="chat-trigger" type="button" aria-label="Open chat">
        <i class="bi bi-chat-dots-fill"></i>
    </button>

    <div id="chat-window" class="chat-hidden" role="dialog" aria-label="Concord chatbot">
        <div class="chat-header">
            <div class="header-info">
                <i class="bi bi-person-circle"></i>
                <span>Concord Assistant</span>
            </div>
            <button id="close-chat" aria-label="Close chat"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="chat-body" id="chat-messages">
            <div class="message bot">
                Hi there! I’m here to help. Ask me anything about appointments, doctors, or careers.
            </div>
            <div id="chat-suggestions" class="chat-suggestions"></div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="Type your message...">
            <button id="send-btn"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2026 Concord Hospital. All rights reserved.</p>
        <p>
            <a href="#privacy">Privacy Policy</a> |
            <a href="#terms">Terms of Service</a>
        </p>
    </div>
</footer>
</div>
@endsection