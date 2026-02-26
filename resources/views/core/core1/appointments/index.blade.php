@extends('layouts.core1.layouts.app')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
<style>
    .fc {
        max-width: 100%;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .fc-header-toolbar {
        margin-bottom: 2rem !important;
    }
    .fc-button-primary {
        background-color: var(--primary-color, #2563eb) !important;
        border-color: var(--primary-color, #2563eb) !important;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
        border: none;
    }
    .fc-event-title {
        font-weight: 500;
        font-size: 0.85rem;
    }
    .fc-daygrid-event {
        white-space: normal !important;
    }
</style>
@endpush

@section('title', 'Appointments')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Appointments</h1>
            <p class="core1-subtitle">Manage and schedule appointments</p>
        </div>
        @if(auth()->user()->role !== 'doctor')
        <a href="{{ route('core1.appointments.create') }}" class="core1-btn core1-btn-primary">
            <i class="fas fa-plus"></i>
            <span class="pl-20">Book Appointment</span>
        </a>
        @endif
    </div>

    <!-- Calendar -->
    <div class="core1-card" style="padding: 0; overflow: hidden;">
        <div id="calendar"></div>
    </div>

    <!-- Appointment Table -->
    <div class="core1-card mt-20">
        <h3 class="core1-title">
            {{ auth()->user()->role === 'doctor' ? 'My Appointments' : 'Appointments' }}
        </h3>

        <table class="core1-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Patient</th>
                    @if(auth()->user()->role !== 'doctor')
                        <th>Doctor</th>
                    @endif
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @if(auth()->user()->role !== 'doctor' || auth()->user()->id === $appointment->doctor_id)
                        <tr>
                            <td>
                                <div class="core1-flex-gap-2">
                                    <i class="fas fa-calendar text-gray"></i>
                                    <div>
                                        <div class="text-sm font-medium text-dark">
                                            {{ $appointment->appointment_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="core1-flex-gap-2">
                                    <i class="fas fa-user text-gray"></i>
                                    <div>
                                        <div class="text-sm font-medium text-dark">{{ $appointment->patient->name }}</div>
                                        <div class="text-xs text-gray">{{ $appointment->patient->patient_id }}</div>
                                    </div>
                                </div>
                            </td>
                            @if(auth()->user()->role !== 'doctor')
                            <td>
                                <div class="text-sm text-dark">{{ $appointment->doctor->name }}</div>
                            </td>
                            @endif
                            <td>
                                <div class="text-sm text-dark">{{ $appointment->type }}</div>
                            </td>

                            @php
                                $displayStatus = match($appointment->status) {
                                    'pending', 'scheduled', 'confirmed', 'completed', 'cancelled', 'declined', 'no-show' => $appointment->status,
                                    'waiting', 'in_consultation', 'consulted', 'triaged' => 'scheduled',
                                    default => 'scheduled',
                                };
                                $badgeClass = match($displayStatus) {
                                    'scheduled', 'pending', 'confirmed', 'completed' => 'core1-badge-active',
                                    'cancelled', 'no-show' => 'core1-badge-inactive',
                                    'declined' => 'core1-badge-warning',
                                    default => 'core1-badge-active',
                                };
                            @endphp

                            <td>
                                <span class="core1-badge {{ $badgeClass }}">
                                    <span class="pl-20">{{ ucfirst($displayStatus) }}</span>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex items-center justify-center gap-2">
                                    @if(auth()->user()->role === 'doctor' && $appointment->status === 'pending')
                                        <form action="{{ route('core1.appointments.accept', $appointment) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="core1-btn core1-btn-primary">Accept</button>
                                        </form>

                                        <form action="{{ route('core1.appointments.decline', $appointment) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="core1-btn core1-btn-outline">Decline</button>
                                        </form>
                                    @else
                                        <a href="{{ route('core1.appointments.show', $appointment) }}" class="btn-icon-action text-blue-500" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->status !== 'declined')
                                        <form action="{{ route('core1.appointments.destroy', $appointment) }}" method="POST" class="d-flex m-0 bg-transparent">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-action text-red-500" title="Cancel" onclick="return confirm('Cancel appointment?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endif
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-40 text-gray">
                            No appointments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const viewMap = { month: 'dayGridMonth', week: 'timeGridWeek', day: 'timeGridDay' };
    const currentView = '{{ $view }}';
    const initialView = viewMap[currentView] || 'dayGridMonth';
    const initialDate = '{{ $currentDate }}';

    const events = [
        @foreach($appointments as $appointment)
            @if(auth()->user()->role !== 'doctor' || $appointment->doctor_id == auth()->user()->id)
            @php
                $displayStatus = in_array($appointment->status, ['pending','scheduled','confirmed','completed','cancelled','declined','no-show'])
                    ? $appointment->status
                    : 'scheduled';
                $titleExtra = ' (' . $displayStatus . ')';
                $title = auth()->user()->role === 'doctor' 
                    ? $appointment->patient->name . ' - ' . $appointment->type . $titleExtra
                    : $appointment->patient->name . ' - ' . ($appointment->doctor ? $appointment->doctor->name : 'No Doctor') . $titleExtra;
            @endphp
        {
            id: '{{ $appointment->id }}',
            title: '{{ $title }}',
            start: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ \Carbon\Carbon::parse($appointment->appointment_time)->format("H:i:s") }}',
            end: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ \Carbon\Carbon::parse($appointment->appointment_time)->addMinutes(30)->format("H:i:s") }}',
            backgroundColor: '{{ 
                $displayStatus === "scheduled" ? "#10b981" : 
                ($displayStatus === "declined" ? "#ef4444" : "#facc15")
            }}',
            borderColor: 'transparent'
        },
            @endif
        @endforeach
    ];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        initialDate: initialDate.includes('-') && initialDate.split('-').length === 2 ? initialDate + '-01' : initialDate,
        headerToolbar: false,
        themeSystem: 'standard',
        events: events,
        eventClick: function(info) {
            window.location.href = `{{ route('core1.appointments.index') }}/${info.event.id}`;
        },
        height: 'auto',
        allDaySlot: false,
        slotMinTime: '08:00:00',
        slotMaxTime: '19:00:00',
    });

    calendar.render();
});
</script>
@endpush
