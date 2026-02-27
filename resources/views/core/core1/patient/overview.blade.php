<div class="core1-header">
    <h2 class="core1-title">Welcome, {{ $patient->name ?? auth()->user()->name }}</h2>
    <p class="core1-subtitle">Your personal health records and appointment history</p>
</div>

@if(!isset($patient))
    <div class="alert alert-info">
        <div class="d-flex items-center gap-3">
            <i class="fas fa-info-circle"></i>
            <div>
                <p class="m-0 font-bold">Patient Profile Required</p>
                <p class="m-0 text-xs">Please complete your registration to access medical records and appointments.</p>
            </div>
        </div>
    </div>
@endif

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Upcoming Visits</h3>
            <p class="core1-title text-blue">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Total Visits</h3>
            <p class="core1-title text-green">{{ $stats['total_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Medical Records</h3>
            <p class="core1-title text-purple">{{ $stats['medical_records'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Unpaid Bills</h3>
            <p class="core1-title text-orange">{{ $stats['pending_bills'] ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Scheduled Appointments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Upcoming Appointments</h2>
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
                        <td>
                            <div class="font-bold text-blue">{{ $appointment->doctor->name ?? 'N/A' }}</div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                        <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                        <td>
                            <span class="core1-status-tag core1-tag-recovering">Scheduled</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state-cell text-center p-40">No upcoming appointments.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Medical History -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Recent Records</h2>
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
                        <td colspan="3" class="empty-state-cell text-center p-40">No recent medical records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-30">
    <div class="core1-card no-hover has-header overflow-hidden" style="max-height: 400px; display: flex; flex-direction: column;">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Outstanding Bills</h2>
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
                        <td class="font-bold text-orange">â‚±{{ number_format($bill->total, 2) }}</td>
                        <td>
                            <button class="core1-btn-small core1-btn-blue">Pay Now</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state-cell text-center p-40">No outstanding bills. You're all caught up!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
