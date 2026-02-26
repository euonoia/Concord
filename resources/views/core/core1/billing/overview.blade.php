<div class="core1-header">
    <h2 class="core1-title">Billing & Financial Overview</h2>
    <p class="core1-subtitle">Monitor revenue, pending invoices, and payment statuses</p>
</div>

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Revenue</h3>
            <p class="core1-title text-green">â‚±{{ number_format($stats['today_revenue'], 2) }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Monthly Revenue</h3>
            <p class="core1-title text-blue">â‚±{{ number_format($stats['monthly_revenue'], 2) }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Pending Bills</h3>
            <p class="core1-title text-orange">{{ $stats['pending_bills'] }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Overdue Bills</h3>
            <p class="core1-title text-red">{{ $stats['overdue_bills'] }}</p>
        </div>
    </div>
</div>

<div class="core1-dashboard-split">
    <!-- Pending Invoices -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Pending Invoices</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Bill #</th>
                        <th>Patient</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingBills as $bill)
                    <tr>
                        <td class="text-xs font-mono">{{ $bill->bill_number }}</td>
                        <td class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</td>
                        <td class="font-bold">â‚±{{ number_format($bill->total, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</td>
                        <td>
                            <span class="core1-status-tag tag-pending">Pending</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state-cell text-center p-40">No pending invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Recent Transactions</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Bill #</th>
                        <th>Patient</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBills as $bill)
                    <tr>
                        <td class="text-xs font-mono">{{ $bill->bill_number }}</td>
                        <td class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</td>
                        <td class="font-bold">â‚±{{ number_format($bill->total, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('M d, Y') }}</td>
                        <td>
                            @php
                                $statusClass = 'tag-pending';
                                if($bill->status == 'paid') $statusClass = 'core1-tag-stable';
                                if($bill->status == 'overdue') $statusClass = 'tag-red';
                            @endphp
                            <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($bill->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state-cell text-center p-40">No recent transactions.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
