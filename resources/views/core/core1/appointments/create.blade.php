@extends('core.core1.layouts.app')

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
                {{ $patient->name }} ({{ $patient->mrn }})
                @if($patient->hasUpcomingAppointment) - Already Booked @endif
            </option>
        @endforeach
    </select>
</div>

{{-- Hidden Medical Department (Controlled by Service Type mapping) --}}
<input type="hidden" id="department_id" name="department_id" value="{{ old('department_id') }}">

                {{-- Service Type (Mapped to Department) --}}
                <div class="core1-form-group core1-col-span-2">
                    <label for="type" class="core1-label">Service Type *</label>
                    <select id="type" name="type" required
                            class="core1-input">
                        <option value="">Select Service Type</option>
                        <option value="General Checkup" data-dept="MED-GEN" {{ old('type') === 'General Checkup' ? 'selected' : '' }}>General Checkup</option>
                        <option value="Sick Visit" data-dept="MED-GEN" {{ old('type') === 'Sick Visit' ? 'selected' : '' }}>Sick Visit</option>
                        <option value="Pedia / Baby Check" data-dept="PED-01" {{ old('type') === 'Pedia / Baby Check' ? 'selected' : '' }}>Pedia / Baby Check</option>
                        <option value="Follow-up" data-dept="MED-GEN" {{ old('type') === 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="Refill" data-dept="MED-GEN" {{ old('type') === 'Refill' ? 'selected' : '' }}>Refill</option>
                        <option value="Lab / Test" data-dept="PATH-01" {{ old('type') === 'Lab / Test' ? 'selected' : '' }}>Lab / Test</option>
                        <option value="Talk Therapy / Mental Health" data-dept="PSY-01" {{ old('type') === 'Talk Therapy / Mental Health' ? 'selected' : '' }}>Talk Therapy / Mental Health</option>
                    </select>
                </div>

                <div class="core1-form-group core1-col-span-2">
                    <label for="doctor_id" class="core1-label">Select Doctor *</label>
                    <select id="doctor_id" name="doctor_id" required class="core1-input">
                        <option value="">Select Service Type First</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" 
                                    data-department="{{ $doctor->employee->department_id ?? '' }}"
                                    class="doctor-option"
                                    style="display: none;"
                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ ($doctor->employee && $doctor->employee->first_name) ? $doctor->employee->full_name : $doctor->username }} @if($doctor->employee && $doctor->employee->specialization)({{ $doctor->employee->specialization }})@endif
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
                    msg.style.color = 'var(--text-light)';
                } else {
                    timeSelect.innerHTML = '<option value="">No slots available</option>';
                    msg.textContent = data.message || 'No slots available for this date.';
                    msg.style.color = 'var(--core1-error)';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                msg.textContent = 'Error checking availability.';
                msg.style.color = 'var(--core1-error)';
            })
            .finally(() => {
                timeSelect.disabled = false;
            });
    }

    const serviceTypeSelect = document.getElementById('type');
    const deptInput = document.getElementById('department_id');
    const initialDoctors = Array.from(document.querySelectorAll('.doctor-option')).map(opt => ({
        id: opt.value,
        name: opt.textContent.trim(),
        deptId: (opt.getAttribute('data-department') || '').trim().toUpperCase()
    }));

    function filterDoctors() {
        const selectedOption = serviceTypeSelect.options[serviceTypeSelect.selectedIndex];
        const selectedDept = selectedOption ? (selectedOption.getAttribute('data-dept') || '').trim().toUpperCase() : '';
        const currentDoctorId = doctorSelect.value;
        
        console.log('Filtering for Dept:', selectedDept);
        console.log('Available Doctors:', initialDoctors);
        
        // Update hidden department_id for backend validation
        deptInput.value = selectedDept;
        
        // Clear and add placeholder
        doctorSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        
        if (!selectedDept) {
            placeholder.textContent = 'Select Service Type First';
            doctorSelect.appendChild(placeholder);
            checkAvailability();
            return;
        }

        placeholder.textContent = 'Select Doctor';
        doctorSelect.appendChild(placeholder);

        let matchCount = 0;
        initialDoctors.forEach(doc => {
            console.log(`Checking Doc: ${doc.name}, Dept: ${doc.deptId} vs ${selectedDept}`);
            if (doc.deptId === selectedDept && doc.deptId !== '') {
                const opt = document.createElement('option');
                opt.value = doc.id;
                opt.textContent = doc.name;
                if (doc.id === currentDoctorId) {
                    opt.selected = true;
                }
                doctorSelect.appendChild(opt);
                matchCount++;
            }
        });

        if (matchCount === 0) {
            placeholder.textContent = 'Select Doctor (No direct service match)';
            // Fallback: Show all doctors if no specific match
            initialDoctors.forEach(doc => {
                const opt = document.createElement('option');
                opt.value = doc.id;
                opt.textContent = doc.name + ' (All Services)';
                doctorSelect.appendChild(opt);
            });
        }
        
        checkAvailability();
    }

    serviceTypeSelect.addEventListener('change', filterDoctors);
    doctorSelect.addEventListener('change', checkAvailability);
    dateInput.addEventListener('change', checkAvailability);
    
    // Initial filter if old values exist
    if (deptInput && deptInput.value) {
        filterDoctors();
    }
    
    // Check initial state if date exists
    if (doctorSelect.value && dateInput.value) {
        checkAvailability();
    }
});
</script>
@endpush

