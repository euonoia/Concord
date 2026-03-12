<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Admin Hospital Overview</h1>
            <p class="core1-subtitle">Monitor comprehensive hospital operations and system health</p>
        </div>
        <div class="d-flex items-center gap-3">
            <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-clock" style="color: var(--primary);"></i>
                <span>{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Total Patients</p>
                <p class="core1-title text-blue">{{ $stats['total_patients'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Registered in system</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Today's Appointments</p>
                <p class="core1-title text-green">{{ $stats['today_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Scheduled for today</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Bed Occupancy</p>
                <p class="core1-title text-purple">{{ $stats['bed_occupancy']['percentage'] }}%</p>
                <p class="text-xs text-gray mt-5">{{ $stats['bed_occupancy']['occupied'] }} / {{ $stats['bed_occupancy']['total'] }} beds</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2.5h8V2a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1H2v1a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-2zm1 8.5h12V7H2v3.5zm11-5.5H4V4H2v1h12V5h-2z"/>
                </svg>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Monthly Revenue</p>
                <p class="core1-title text-orange">₱{{ number_format($stats['monthly_revenue'], 0) }}</p>
                <p class="text-xs text-gray mt-5">Billed this month</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Inpatients</p>
                <p class="core1-title" style="color: #0ea5e9;">{{ $stats['inpatient_count'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Currently admitted</p>
            </div>
            <div class="core1-icon-box" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0ea5e9;">
                <i class="bi bi-hospital"></i>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Outpatients</p>
                <p class="core1-title" style="color: #ec4899;">{{ $stats['outpatient_count'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Active outpatients</p>
            </div>
            <div class="core1-icon-box" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: #ec4899;">
                <i class="bi bi-person-walking"></i>
            </div>
        </div>
    </div>

    {{-- Dashboard Split: Alerts + Activity --}}
    <div class="core1-dashboard-split">

        {{-- System Alerts Panel --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--danger-light); color: var(--danger); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">System Alerts</h2>
                </div>
                <span style="font-size: 11px; font-weight: 700; background: var(--danger-light); color: var(--danger); padding: 3px 9px; border-radius: 999px;">
                    {{ count($alerts) }} Active
                </span>
            </div>
            <div class="core1-scroll-area" style="padding: 18px 22px; display: flex; flex-direction: column; gap: 10px;">
                @forelse($alerts as $alert)
                    @php
                        $type = $alert['type'] ?? 'info';
                        $colorMap = [
                            'critical' => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'icon' => 'bi-exclamation-octagon-fill', 'border' => '#fca5a5'],
                            'warning'  => ['bg' => 'var(--warning-light-more)', 'color' => 'var(--warning)', 'icon' => 'bi-exclamation-triangle-fill', 'border' => '#fcd34d'],
                            'info'     => ['bg' => 'var(--info-light)', 'color' => 'var(--info)', 'icon' => 'bi-info-circle-fill', 'border' => '#93c5fd'],
                            'success'  => ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'icon' => 'bi-check-circle-fill', 'border' => '#86efac'],
                        ];
                        $c = $colorMap[$type] ?? $colorMap['info'];
                    @endphp
                    <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border-radius: 10px; border: 1px solid {{ $c['border'] }}; background: {{ $c['bg'] }};">
                        <div style="width: 32px; height: 32px; border-radius: 7px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: {{ $c['color'] }}; flex-shrink: 0;">
                            <i class="bi {{ $c['icon'] }}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="margin: 0 0 2px 0; font-weight: 700; font-size: 13px; color: var(--text-dark);">{{ $alert['message'] }}</p>
                            <p style="margin: 0; font-size: 11px; color: {{ $c['color'] }}; font-weight: 600; text-transform: uppercase;">{{ ucfirst($alert['priority']) }} Priority</p>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 30px 0;">
                        <i class="bi bi-shield-check" style="font-size: 2rem; color: var(--success); display: block; margin-bottom: 8px;"></i>
                        <p style="font-size: 13px; color: var(--text-gray); margin: 0;">All systems operational</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity Panel --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-activity"></i>
                    </div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Recent Activity Log</h2>
                </div>
                <span style="font-size: 11px; font-weight: 700; background: var(--bg); color: var(--text-gray); padding: 3px 9px; border-radius: 999px; border: 1px solid var(--border-color);">
                    Live
                </span>
            </div>
            <div class="core1-scroll-area" style="padding: 18px 22px; display: flex; flex-direction: column; gap: 0;">
                @forelse($recentActivities as $activity)
                    <div style="display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                        <div class="core1-avatar" style="background: var(--bg); color: var(--primary); width:36px; height:36px; border-radius:50%; font-size:0.9rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; border: 1px solid var(--border-color);">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="margin: 0 0 2px 0; font-weight: 700; font-size: 13px; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $activity['action'] }}</p>
                            <p style="margin: 0; font-size: 11px; color: var(--text-gray);">Patient: <span style="font-weight: 600; color: var(--primary);">{{ $activity['patient'] }}</span></p>
                        </div>
                        <div style="font-size: 11px; color: var(--text-light); white-space: nowrap; display: flex; align-items: center; gap: 4px;">
                            <i class="bi bi-clock"></i>
                            {{ $activity['time'] }}
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 30px 0;">
                        <i class="bi bi-journal-x" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                        <p style="font-size: 13px; color: var(--text-gray); margin: 0;">No recent activity recorded</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
