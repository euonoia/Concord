<div class="core1-header">
    <h2 class="core1-title">Welcome, Dr. {{ auth()->user()->name }}</h2>
    <p class="core1-subtitle">Your clinical dashboard overview</p>
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
            <h3 class="core1-info-item h3 text-center mb-10">Upcoming Appointments</h3>
            <p class="core1-title text-orange">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Total Patients</h3>
            <p class="core1-title text-green">{{ $stats['total_patients'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Records This Week</h3>
            <p class="core1-title text-purple">{{ $stats['recent_records'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Today's Appointments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Today's Schedules</h2>
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
                        <td>
                            <div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </td>
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
                        <td colspan="4" class="empty-state-cell text-center p-40">No appointments scheduled for today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inpatients Under Care -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Inpatients Under Care</h2>
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
        <td colspan="4" class="empty-state-cell text-center p-40">No patients under care found.</td>
    </tr>
    @endforelse
</tbody>

            </table>
        </div>
    </div>
</div>
