<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Welcome, Dr. {{ auth()->user()->name }}</h1>
            <p class="core1-subtitle">Your clinical dashboard overview</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Today's Appointments</p>
                <p class="core1-title text-blue">{{ $stats['today_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Scheduled for today</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Upcoming Appointments</p>
                <p class="core1-title text-orange">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">In the next 7 days</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-calendar-week"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Total Patients</p>
                <p class="core1-title text-green">{{ $stats['total_patients'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Under your care</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-person-heart"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Records This Week</p>
                <p class="core1-title text-purple">{{ $stats['recent_records'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Clinical entries</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <i class="bi bi-journal-medical"></i>
            </div>
        </div>
    </div>

    {{-- Dashboard Split --}}
    <div class="core1-dashboard-split">

        {{-- Today's Schedule --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-list-check"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Today's Schedules</h2>
            </div>
            <div class="core1-table-container shadow-none core1-scroll-area">
                <table class="core1-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAppointments as $appointment)
                        <tr>
                            <td><div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div></td>
                            <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                            <td>{{ ucfirst($appointment->type ?? 'N/A') }}</td>
                            <td>
                                @php
                                    $statusClass = 'tag-pending';
                                    if($appointment->status == 'scheduled') $statusClass = 'core1-tag-recovering';
                                    if($appointment->status == 'completed') $statusClass = 'core1-tag-stable';
                                @endphp
                                <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-40">
                                <i class="bi bi-calendar-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No appointments today.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Inpatients Under Care --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-hospital"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Inpatients Under Care</h2>
            </div>
            <div class="core1-table-container shadow-none core1-scroll-area">
                <table class="core1-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Patient Name</th>
                            <th>Bed</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inpatients as $patient)
                        <tr>
                            <td class="text-xs text-gray font-mono">{{ $patient->id }}</td>
                            <td class="font-bold text-blue">{{ $patient->name }}</td>
                            <td><span class="core1-badge-teal">{{ $patient->bed ?? 'N/A' }}</span></td>
                            <td>
                                @php
                                    $statusClass = 'tag-pending';
                                    if($patient->status == 'in consultation') $statusClass = 'core1-tag-recovering';
                                    if($patient->status == 'consulted') $statusClass = 'core1-tag-stable';
                                @endphp
                                <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($patient->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-40">
                                <i class="bi bi-bed" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No inpatients under care.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
