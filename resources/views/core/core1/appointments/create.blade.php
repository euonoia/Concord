@extends('layouts.core1.layouts.app')

@section('title', 'Book Appointment')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-header">
        <h1 class="core1-title">Book Appointment</h1>
        <p class="core1-subtitle">Schedule a new appointment</p>
    </div>

    <div class="core1-card core1-card-compact">
        <form action="{{ route('core1.appointments.store') }}" method="POST">
            @csrf
            
            <div class="core1-form-grid">
               <div class="core1-form-group core1-col-span-2">
    <label for="patient_id" class="core1-label">Patient *</label>
    <select id="patient_id" name="patient_id" required class="core1-input">
        <option value="">Select Patient</option>
        @foreach($patients as $patient)
            <option value="{{ $patient->id }}"
                {{ old('patient_id') == $patient->id ? 'selected' : '' }}
                @if($patient->hasUpcomingAppointment) disabled @endif
            >
                {{ $patient->name }} ({{ $patient->patient_id }})
                @if($patient->hasUpcomingAppointment) - Already Booked @endif
            </option>
        @endforeach
    </select>
</div>


                <div class="core1-form-group core1-col-span-2">
                    <label for="doctor_id" class="core1-label">Doctor *</label>
                    <select id="doctor_id" name="doctor_id" required
                            class="core1-input">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }} @if($doctor->specialization)({{ $doctor->specialization }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="core1-form-group">
                    <label for="appointment_date" class="core1-label">Date *</label>
                    <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required
                           class="core1-input">
                </div>

                <div class="core1-form-group">
                    <label for="appointment_time" class="core1-label">Time *</label>
                    <select id="appointment_time" name="appointment_time" required
                            class="core1-input">
                        <option value="">Select Date & Doctor First</option>
                    </select>
                    <p id="availability-msg" class="core1-error-text" style="color: var(--text-light);"></p>
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="type" class="core1-label">Appointment Type *</label>
                    <select id="type" name="type" required
                            class="core1-input">
                        <option value="">Select Type</option>
                        <option value="consultation" {{ old('type') === 'consultation' ? 'selected' : '' }}>Consultation</option>
<option value="follow-up" {{ old('type') === 'follow-up' ? 'selected' : '' }}>Follow-up</option>
<option value="check-up" {{ old('type') === 'check-up' ? 'selected' : '' }}>Check-up</option>
<option value="emergency" {{ old('type') === 'emergency' ? 'selected' : '' }}>Emergency</option>

                    </select>
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="reason" class="core1-label">Reason</label>
                    <textarea id="reason" name="reason" rows="3"
                              class="core1-input">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="core1-form-actions">
                <button type="submit" class="core1-btn core1-btn-primary">
                    Book Appointment
                </button>
                <a href="{{ route('core1.appointments.index') }}" class="core1-btn core1-btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const msg = document.getElementById('availability-msg');

    function checkAvailability() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            timeSelect.innerHTML = '<option value="">Select Date & Doctor First</option>';
            return;
        }

        msg.textContent = 'Checking availability...';
        timeSelect.disabled = true;

        fetch(`{{ route('core1.appointments.check-availability') }}?doctor_id=${doctorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = `${slot.time} (${slot.status})`;
                        if (slot.status === 'booked') {
                            option.disabled = true;
                            option.classList.add('bg-gray-100', 'text-gray-400');
                        }
                        timeSelect.appendChild(option);
                    });
                    msg.textContent = 'Slots updated.';
                } else {
                    timeSelect.innerHTML = '<option value="">No slots available</option>';
                    msg.textContent = 'No slots available for this date.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                msg.textContent = 'Error checking availability.';
            })
            .finally(() => {
                timeSelect.disabled = false;
            });
    }

    doctorSelect.addEventListener('change', checkAvailability);
    dateInput.addEventListener('change', checkAvailability);
    
    // Check initial state if old values exist
    if (doctorSelect.value && dateInput.value) {
        checkAvailability();
    }
});
</script>
@endpush

