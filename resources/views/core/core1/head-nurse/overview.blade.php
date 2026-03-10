<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Head Nurse Overview</h1>
            <p class="core1-subtitle">Monitor nursing activities and institutional care</p>
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
                <p class="text-sm text-gray mb-5">Total Nursing Staff</p>
                <p class="core1-title text-blue">{{ $stats['total_nurses'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">On the team</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Active Patients</p>
                <p class="core1-title text-green">{{ $stats['active_patients'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Currently admitted</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Today's Appointments</p>
                <p class="core1-title text-purple">{{ $stats['today_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Scheduled today</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Pending Tasks</p>
                <p class="core1-title text-orange">{{ $stats['pending_tasks'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Requires action</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
        </div>
    </div>

    {{-- Dashboard Split --}}
    <div class="core1-dashboard-split">

        {{-- Nursing Station Summary --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Nursing Station Summary</h2>
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
                            <td><div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div></td>
                            <td>
                                <span class="core1-status-tag {{ $appointment->status == 'completed' ? 'core1-tag-stable' : 'core1-tag-recovering' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>{{ $appointment->patient->assignedNurse->name ?? 'Pending' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-40">
                                <i class="bi bi-clipboard-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No active nursing tasks.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recently Admitted --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Recently Admitted Patients</h2>
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
                            <td colspan="3" class="text-center p-40">
                                <i class="bi bi-person-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No recent admissions.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
