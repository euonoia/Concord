@extends('core.core1.layouts.app')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
<style>
    /* Premium FullCalendar Overrides */
    .fc {
        max-width: 100%;
        background: var(--card-bg, #ffffff);
        padding: 24px;
        border-radius: 16px;
        font-family: inherit;
        color: var(--text-dark, #1f2937);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: var(--border-color, #e5e7eb);
    }

    /* Enhanced Header */
    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700;
        color: var(--text-dark, #1f2937);
    }
    
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color, #e5e7eb);
    }

    /* Premium Buttons */
    .fc .fc-button-primary {
        background-color: var(--bg, #f8fafc) !important;
        border-color: var(--border-color, #e5e7eb) !important;
        color: var(--text-dark, #4b5563) !important;
        text-transform: capitalize;
        font-weight: 500;
        box-shadow: none !important;
        transition: all 0.2s ease;
        padding: 0.4rem 1rem;
        border-radius: 6px;
    }
    .fc .fc-button-primary:hover {
        background-color: var(--bg-hover, #f1f5f9) !important;
        color: var(--primary, #0ea5e9) !important;
        transform: translateY(-1px);
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background-color: var(--primary-light, #e0f2fe) !important;
        border-color: var(--primary, #0ea5e9) !important;
        color: var(--primary-dark, #0369a1) !important;
    }
    .fc .fc-button-primary:focus {
        box-shadow: 0 0 0 2px var(--primary-light, #bae6fd) !important;
    }

    /* Day Headers */
    .fc-col-header-cell {
        background: var(--bg, #f8fafc);
        padding: 12px 0 !important;
    }
    .fc-col-header-cell-cushion {
        font-weight: 600 !important;
        color: var(--text-gray, #6b7280) !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* Day Cells */
    .fc-daygrid-day-number {
        font-weight: 500;
        color: var(--text-dark, #374151) !important;
        padding: 8px !important;
    }
    .fc-day-today {
        background-color: var(--primary-light-more, #f0f9ff) !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        color: var(--primary, #0ea5e9) !important;
        font-weight: 700;
    }

    /* Events Styling */
    .fc-event {
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 6px;
        border: none;
        margin-bottom: 3px !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 5;
    }
    .fc-event-title {
        font-weight: 600;
        font-size: 0.75rem;
        line-height: 1.2;
    }
    .fc-event-time {
        font-size: 0.7rem;
        opacity: 0.8;
        font-weight: 500;
    }
    .fc-daygrid-event {
        white-space: normal !important;
    }
    
    /* Time Grid specifics */
    .fc-timegrid-slot-label-cushion {
        font-size: 0.75rem;
        color: var(--text-gray, #6b7280);
        font-weight: 500;
    }
    .fc-timegrid-event {
        border-left-width: 3px !important;
    }
</style>
@endpush

@section('title', 'Appointments')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Appointments</h1>
            <p class="core1-subtitle">Manage and schedule patient appointments</p>
        </div>
        <div style="display:flex; align-items:center; gap: 10px;">
            <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-clock" style="color: var(--primary);"></i>
                <span>{{ now()->format('l, F j, Y') }}</span>
            </div>
            @if(auth()->user()->role !== 'doctor')
            <a href="{{ route('core1.appointments.create') }}" class="core1-btn core1-btn-primary">
                <i class="bi bi-plus-lg"></i> Book Appointment
            </a>
            @endif
        </div>
    </div>

    {{-- Calendar --}}
    <div class="core1-card" style="padding: 0; overflow: hidden; border-radius: 12px; margin-bottom: 20px;">
        <div class="core1-card-header" style="padding: 16px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-calendar3"></i>
            </div>
            <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Appointment Calendar</h2>
        </div>
        <div style="padding: 20px;">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- Appointments Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-list-check"></i>
            </div>
            <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">
                {{ auth()->user()->role === 'doctor' ? 'My Appointments' : 'All Appointments' }}
            </h2>
        </div>

        <div class="core1-table-container shadow-none">
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
                                <div style="display:flex; align-items:center; gap: 8px;">
                                    <i class="bi bi-calendar-event" style="color: var(--primary); font-size: 0.9rem;"></i>
                                    <div>
                                        <div class="font-bold" style="font-size: 13px;">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="font-bold text-blue" style="font-size: 13px;">{{ $appointment->patient->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray">{{ $appointment->patient->mrn ?? 'N/A' }}</div>
                                </div>
                            </td>
                            @if(auth()->user()->role !== 'doctor')
                            <td>{{ $appointment->doctor->name ?? 'No Doctor Assigned' }}</td>
                            @endif
                            <td>{{ $appointment->type }}</td>

                            @php
                                $displayStatus = match($appointment->status) {
                                    'pending', 'scheduled', 'confirmed', 'completed', 'cancelled', 'declined', 'no-show' => $appointment->status,
                                    'waiting', 'in_consultation', 'consulted', 'triaged' => 'scheduled',
                                    default => 'scheduled',
                                };
                                $badgeClass = match($displayStatus) {
                                    'scheduled', 'pending', 'confirmed' => 'core1-tag-recovering',
                                    'completed' => 'core1-tag-stable',
                                    'cancelled', 'no-show' => 'tag-pending',
                                    'declined' => 'tag-red',
                                    default => 'core1-tag-recovering',
                                };
                            @endphp

                            <td>
                                <span class="core1-status-tag {{ $badgeClass }}">{{ ucfirst($displayStatus) }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; justify-content:center; gap: 8px;">
                                    @if(auth()->user()->role === 'doctor' && $appointment->status === 'pending')
                                        <form action="{{ route('core1.appointments.accept', $appointment) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="core1-btn-sm core1-btn-primary" style="padding: 5px 12px; font-size:11px;">
                                                <i class="bi bi-check-lg"></i> Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('core1.appointments.decline', $appointment) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="core1-btn-sm core1-btn-outline" style="padding: 5px 12px; font-size:11px;">
                                                <i class="bi bi-x-lg"></i> Decline
                                            </button>
                                        </form>
                                    @elseif(auth()->user()->role === 'receptionist' && $appointment->status === 'pending')
                                        <form action="{{ route('core1.receptionist.online-appointments.approve', $appointment->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Approve this appointment?');">
                                            @csrf
                                            <button type="submit" class="core1-btn-sm core1-btn-primary" style="padding: 5px 10px; font-size:11px;" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('core1.receptionist.online-appointments.reject', $appointment->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Reject this appointment?');">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" value="Rejected by receptionist from calendar view">
                                            <button type="submit" style="padding: 5px 10px; font-size:11px; background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); border-radius: 7px; cursor: pointer;" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('core1.appointments.show', $appointment) }}" class="core1-btn-sm core1-btn-outline" style="padding: 5px 10px; font-size:11px;" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->status !== 'declined')
                                        <form action="{{ route('core1.appointments.destroy', $appointment) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 5px 10px; font-size:11px; background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); border-radius: 7px; cursor: pointer;" title="Cancel" onclick="return confirm('Cancel appointment?')">
                                                <i class="bi bi-x-lg"></i>
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
                            <td colspan="6" class="text-center p-40">
                                <i class="bi bi-calendar-x" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No appointments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                    ? ($appointment->patient->name ?? 'Unknown') . ' - ' . $appointment->type . $titleExtra
                    : ($appointment->patient->name ?? 'Unknown') . ' - ' . ($appointment->doctor ? $appointment->doctor->name : 'No Doctor') . $titleExtra;
            @endphp
        {
            id: '{{ $appointment->id }}',
            title: '{{ $title }}',
            start: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ \Carbon\Carbon::parse($appointment->appointment_time)->format("H:i:s") }}',
            end: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ \Carbon\Carbon::parse($appointment->appointment_time)->addMinutes(30)->format("H:i:s") }}',
            backgroundColor: '{{ $displayStatus === "scheduled" ? "#10b981" : ($displayStatus === "declined" ? "#ef4444" : "#facc15") }}',
            borderColor: 'transparent'
        },
            @endif
        @endforeach
    ];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        initialDate: initialDate.includes('-') && initialDate.split('-').length === 2 ? initialDate + '-01' : initialDate,
        headerToolbar: {
            left: 'title',
            right: 'prev,next today dayGridMonth,timeGridWeek,timeGridDay'
        },
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
