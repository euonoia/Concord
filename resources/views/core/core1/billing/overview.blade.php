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
                        <tr data-bill-id="{{ $bill->id }}" 
                            data-bill-number="{{ $bill->bill_number }}" 
                            data-patient-name="{{ $bill->patient->name ?? 'N/A' }}" 
                            data-total="{{ $bill->total }}"
                            style="cursor: pointer;">
                            <td class="text-xs font-mono" style="color: var(--primary);">{{ $bill->bill_number }}</td>
                            <td>
                                <div class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</div>
                                @if($bill->patient->mrn)
                                    <div class="text-xs font-mono font-bold mt-1" style="color:#1a3a5a;">
                                        <i class="bi bi-person-badge text-xxs"></i> {{ $bill->patient->mrn }}
                                    </div>
                                @endif
                            </td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') }}</td>
                            <td><span class="core1-status-tag core1-tag-neutral">Pending</span></td>
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
                            <td>
                                <div class="font-bold text-blue">{{ $bill->patient->name ?? 'N/A' }}</div>
                                @if($bill->patient->mrn)
                                    <div class="text-xs font-mono font-bold mt-1" style="color:#1a3a5a;">
                                        <i class="bi bi-person-badge text-xxs"></i> {{ $bill->patient->mrn }}
                                    </div>
                                @endif
                            </td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $statusClass = 'core1-tag-neutral';
                                    if($bill->status == 'paid') $statusClass = 'core1-tag-stable';
                                    if($bill->status == 'partial') $statusClass = 'core1-tag-recovering';
                                    if($bill->status == 'overdue') $statusClass = 'core1-tag-critical';
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

    {{-- Payment Processing Modal (Context 7 Style) --}}
    <div id="paymentModal" class="core1-modal" style="display:none; z-index:1050; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="core1-modal-content" style="background:white; border-radius:12px; width:450px; max-width:90%; position:relative; overflow:hidden; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div class="core1-modal-header" style="padding:20px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between; background:var(--bg);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="core1-icon-box" style="background:var(--primary-light); color:var(--primary);">
                        <i class="bi bi-credit-card-fill"></i>
                    </div>
                    <div>
                        <h3 class="core1-title mb-0" style="font-size:16px;">Process Payment</h3>
                        <p class="text-xs text-gray mb-0" id="paymentBillNumber">---</p>
                    </div>
                </div>
                <button type="button" onclick="closePaymentModal()" style="background:none; border:none; font-size:1.5rem; color:var(--text-gray); cursor:pointer;">&times;</button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="core1-modal-body" style="padding:20px;">
                    <div style="background:var(--bg); border:1px solid var(--border-color); border-radius:8px; padding:12px; margin-bottom:20px;">
                        <div class="core1-flex-between mb-5">
                            <span class="text-xs text-gray">Patient</span>
                            <span class="text-sm font-bold" id="paymentPatientName">---</span>
                        </div>
                        <div class="core1-flex-between">
                            <span class="text-xs text-gray">Total Amount Due</span>
                            <span class="text-lg font-bold text-blue" id="paymentTotalAmount">---</span>
                        </div>
                    </div>

                    <div class="mb-15">
                        <label class="core1-label">Amount to Pay</label>
                        <input type="number" name="amount" id="paymentAmountInput" step="0.01" class="core1-input" required>
                    </div>

                    <div class="mb-15">
                        <label class="core1-label">Payment Method</label>
                        <select name="payment_method" class="core1-input" required>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="G-Cash">G-Cash</option>
                            <option value="Insurance">Insurance Settlement</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="core1-label">Reference Number (Optional)</label>
                        <input type="text" name="transaction_reference" class="core1-input" placeholder="OR #, Trace ID, etc.">
                    </div>
                </div>
                <div class="core1-modal-footer" style="padding:15px 20px; background:var(--bg); border-top:1px solid var(--border-color); display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openPaymentModal(billId, billNumber, patientName, total) {
        document.getElementById('paymentBillNumber').innerText = 'Bill #: ' + billNumber;
        document.getElementById('paymentPatientName').innerText = patientName;
        document.getElementById('paymentTotalAmount').innerText = '₱' + parseFloat(total).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('paymentAmountInput').value = total;
        document.getElementById('paymentForm').action = '/core/billing/pay/' + billId;
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    // Context 7: Real-time Polling for billing updates
    function pollBillingUpdates() {
        fetch('{{ route("core1.billing.dashboard.updates") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.requires_refresh) {
                // For now, simpler to reload or we can patch the table
                // console.log("New billing data detected, refreshing...");
                // location.reload(); 
            }
        })
        .catch(err => console.error('Polling error:', err));
    }

    // Check for updates every 30 seconds
    setInterval(pollBillingUpdates, 30000);

    // Initial call to attach listeners if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Add click listeners to pending bill rows for quick payment
        const table = document.querySelector('.core1-table');
        if (table) {
            table.addEventListener('click', function(e) {
                const tr = e.target.closest('tr');
                if (tr && tr.dataset.billId) {
                    openPaymentModal(
                        tr.dataset.billId,
                        tr.dataset.billNumber,
                        tr.dataset.patientName,
                        tr.dataset.total
                    );
                }
            });
        }
    });
</script>
