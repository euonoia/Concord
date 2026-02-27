<div class="core1-header">
    <h2 class="core1-title">Admin Hospital Overview</h2>
    <p class="core1-subtitle">Monitor comprehensive hospital operations and financial health</p>
</div>

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Total Patients</h3>
            <p class="core1-title text-blue">{{ $stats['total_patients'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Appointments</h3>
            <p class="core1-title text-green">{{ $stats['today_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Bed Occupancy</h3>
            <p class="core1-title text-purple">{{ $stats['bed_occupancy']['percentage'] }}%</p>
            <p class="text-xs text-gray">{{ $stats['bed_occupancy']['occupied'] }} / {{ $stats['bed_occupancy']['total'] }} Beds</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Monthly Revenue</h3>
            <p class="core1-title text-orange">â‚±{{ number_format($stats['monthly_revenue'], 2) }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Critical Alerts -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">System Alerts & Notifications</h2>
        </div>
        <div class="core1-scroll-area p-20">
            <div class="d-flex flex-col gap-3">
                @foreach($alerts as $alert)
                    @php
                        $alertClass = 'alert-success';
                        if($alert['type'] == 'warning') $alertClass = 'alert-warning';
                        if($alert['type'] == 'critical') $alertClass = 'alert-error';
                        if($alert['type'] == 'info') $alertClass = 'alert-info';
                    @endphp
                    <div class="alert {{ $alertClass }} m-0">
                        <div class="d-flex items-center gap-3">
                            <i class="fas fa-{{ $alert['type'] == 'critical' ? 'exclamation-triangle' : ($alert['type'] == 'warning' ? 'exclamation-circle' : 'info-circle') }}"></i>
                            <div>
                                <p class="m-0 font-bold">{{ $alert['message'] }}</p>
                                <p class="m-0 text-xs">Priority: {{ ucfirst($alert['priority']) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Recent Activity Log</h2>
        </div>
        <div class="core1-scroll-area p-20">
            <div class="d-flex flex-col gap-4">
                @foreach($recentActivities as $activity)
                    <div class="d-flex items-center gap-3 border-bottom pb-15">
                        <div class="core1-avatar" style="background: var(--bg); color: var(--accent);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div style="flex: 1;">
                            <p class="m-0 font-bold text-sm">{{ $activity['action'] }}</p>
                            <p class="m-0 text-xs text-gray">Patient: {{ $activity['patient'] }}</p>
                        </div>
                        <div class="text-xs text-gray">
                            {{ $activity['time'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
