<div class="core1-header">
    <h2 class="core1-title">Nurse Overview</h2>
    <p class="core1-subtitle">Monitor patient care and daily schedules</p>
</div>

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Appointments</h3>
            <p class="core1-title text-blue">{{ $stats['today_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">My Assigned Patients</h3>
            <p class="core1-title text-green">{{ $stats['assigned_patients'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Registrations</h3>
            <p class="core1-title text-purple">{{ $stats['today_registrations'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Weekly Records</h3>
            <p class="core1-title text-orange">{{ $stats['recent_records'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Today's Appointments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Today's Appointments</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $appointment)
                    <tr>
                        <td>
                            <div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
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
                        <td colspan="4" class="empty-state-cell text-center p-40">No appointments scheduled for today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- My Assigned Patients -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">My Assigned Patients</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Last Visit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignedPatients as $patient)
                    <tr>
                        <td class="font-bold text-blue">
                            <a href="{{ route('core1.patients.show', $patient) }}" class="text-blue">{{ $patient->name }}</a>
                        </td>
                        <td>{{ ucfirst($patient->gender) }}</td>
                        <td>{{ $patient->last_visit ? $patient->last_visit->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty-state-cell text-center p-40">You have no patients assigned to you yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
