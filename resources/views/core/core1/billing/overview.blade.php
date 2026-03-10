<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Billing & Financial Overview</h1>
            <p class="core1-subtitle">Monitor revenue, pending invoices, and payment statuses</p>
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
                <p class="text-sm text-gray mb-5">Today's Revenue</p>
                <p class="core1-title text-green">₱{{ number_format($stats['today_revenue'], 2) }}</p>
                <p class="text-xs text-gray mt-5">Collected today</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Monthly Revenue</p>
                <p class="core1-title text-blue">₱{{ number_format($stats['monthly_revenue'], 2) }}</p>
                <p class="text-xs text-gray mt-5">Billed this month</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Pending Bills</p>
                <p class="core1-title text-orange">{{ $stats['pending_bills'] }}</p>
                <p class="text-xs text-gray mt-5">Awaiting payment</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Overdue Bills</p>
                <p class="core1-title" style="color: var(--danger);">{{ $stats['overdue_bills'] }}</p>
                <p class="text-xs text-gray mt-5">Requires follow-up</p>
            </div>
            <div class="core1-icon-box" style="background: var(--danger-light); color: var(--danger);">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
        </div>
    </div>

    {{-- Dashboard Split --}}
    <div class="core1-dashboard-split">

        {{-- Pending Invoices --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--warning-light-more); color: var(--warning); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Pending Invoices</h2>
                </div>
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
                            <td class="text-xs font-mono" style="color: var(--primary);">{{ $bill->bill_number }}</td>
                            <td class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</td>
                            <td><span class="core1-status-tag tag-pending">Pending</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-40">
                                <i class="bi bi-check2-circle" style="font-size: 1.8rem; color: var(--success); display: block; margin-bottom: 6px;"></i>
                                No pending invoices.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Recent Transactions</h2>
                </div>
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
                            <td class="text-xs font-mono" style="color: var(--primary);">{{ $bill->bill_number }}</td>
                            <td class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
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
                            <td colspan="5" class="text-center p-40">No recent transactions.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
