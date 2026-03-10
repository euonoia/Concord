<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Welcome, {{ $patient->name ?? auth()->user()->name }}</h1>
            <p class="core1-subtitle">Your personal health records and appointment history</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Profile Notice --}}
    @if(!isset($patient))
    <div style="display: flex; align-items: center; gap: 12px; padding: 15px 18px; border-radius: 10px; background: var(--info-light); border: 1px solid var(--info); margin-bottom: 20px;">
        <div style="color: var(--info); font-size: 1.3rem; flex-shrink: 0;"><i class="bi bi-info-circle-fill"></i></div>
        <div>
            <p style="font-weight: 700; font-size: 13px; color: var(--text-dark); margin: 0 0 2px 0;">Patient Profile Required</p>
            <p style="font-size: 12px; color: var(--text-gray); margin: 0;">Complete your registration to access medical records and appointments.</p>
        </div>
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Upcoming Visits</p>
                <p class="core1-title text-blue">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Scheduled ahead</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-calendar2-event"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Total Visits</p>
                <p class="core1-title text-green">{{ $stats['total_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Lifetime appointments</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-check2-all"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Medical Records</p>
                <p class="core1-title text-purple">{{ $stats['medical_records'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">In your health file</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <i class="bi bi-file-earmark-medical"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Unpaid Bills</p>
                <p class="core1-title text-orange">{{ $stats['pending_bills'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Needs your attention</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
        </div>
    </div>

    {{-- Appointments + Records Split --}}
    <div class="core1-dashboard-split">

        {{-- Upcoming Appointments --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Upcoming Appointments</h2>
            </div>
            <div class="core1-table-container shadow-none core1-scroll-area">
                <table class="core1-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingAppointments ?? [] as $appointment)
                        <tr>
                            <td><div class="font-bold text-blue">{{ $appointment->doctor->name ?? 'N/A' }}</div></td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                            <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                            <td><span class="core1-status-tag core1-tag-recovering">Scheduled</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-40">
                                <i class="bi bi-calendar-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No upcoming appointments.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Records --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Recent Medical Records</h2>
            </div>
            <div class="core1-table-container shadow-none core1-scroll-area">
                <table class="core1-table">
                    <thead>
                        <tr>
                            <th>Visit Date</th>
                            <th>Diagnosis</th>
                            <th>Doctor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRecords ?? [] as $record)
                        <tr>
                            <td>{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="font-bold">{{ $record->diagnosis }}</td>
                            <td>{{ $record->doctor->name ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center p-40">
                                <i class="bi bi-journal-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No recent medical records.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Outstanding Bills --}}
    <div class="mt-30">
        <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px; max-height: 400px; display: flex; flex-direction: column;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--warning-light-more); color: var(--warning); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Outstanding Bills</h2>
                </div>
            </div>
            <div class="core1-table-container shadow-none" style="flex: 1; overflow-y: auto;">
                <table class="core1-table">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingBills ?? [] as $bill)
                        <tr>
                            <td class="text-xs text-gray font-mono">{{ $bill->bill_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('M d, Y') }}</td>
                            <td>{{ $bill->description ?? 'Medical Services' }}</td>
                            <td class="font-bold text-orange">₱{{ number_format($bill->total, 2) }}</td>
                            <td>
                                <button class="core1-btn-sm core1-btn-primary" style="padding: 5px 12px; font-size: 11px;">Pay Now</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-40">
                                <i class="bi bi-check2-circle" style="font-size: 1.8rem; color: var(--success); display: block; margin-bottom: 6px;"></i>
                                No outstanding bills. You're all caught up!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
