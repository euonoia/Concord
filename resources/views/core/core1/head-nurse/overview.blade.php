<div class="core1-header">
    <h2 class="core1-title">Head Nurse Overview</h2>
    <p class="core1-subtitle">Monitor nursing activities and institutional care</p>
</div>

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Total Nursing Staff</h3>
            <p class="core1-title text-blue">{{ $stats['total_nurses'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Active Patients</h3>
            <p class="core1-title text-green">{{ $stats['active_patients'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Appointments</h3>
            <p class="core1-title text-purple">{{ $stats['today_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Pending Tasks</h3>
            <p class="core1-title text-orange">{{ $stats['pending_tasks'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Today's Appointments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Nursing Station Summary</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Status</th>
                        <th>Assigned Nurse</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $appointment)
                    <tr>
                        <td>
                            <div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </td>
                         <td>
                            <span class="core1-status-tag {{ $appointment->status == 'completed' ? 'core1-tag-stable' : 'core1-tag-recovering' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td>{{ $appointment->patient->assignedNurse->name ?? 'Pending' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty-state-cell text-center p-40">No active nursing tasks.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Recently Admitted Patients</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Ward/Dept</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPatients as $patient)
                    <tr>
                        <td class="font-bold text-blue">{{ $patient->name }}</td>
                        <td>{{ $patient->department ?? 'General' }}</td>
                        <td>{{ $patient->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty-state-cell text-center p-40">No recent admissions.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
