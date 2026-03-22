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
                            <th>Validated By</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingBills as $bill)
                        <tr style="vertical-align: middle;">
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
                            <td class="text-xs">
                                @if($bill->validator)
                                    <span class="font-semibold">{{ $bill->validator->full_name }}</span>
                                    <div class="text-gray">{{ $bill->validated_by }}</div>
                                @else
                                    <span class="text-gray italic">N/A</span>
                                @endif
                            </td>
                            <td class="text-right">
                                {{-- <button type="button" 
                                        data-bill-id="{{ $bill->id }}"
                                        data-bill-number="{{ $bill->bill_number }}"
                                        data-patient-name="{{ $bill->patient->name ?? 'N/A' }}"
                                        data-total="{{ $bill->total }}"
                                        data-subtotal="{{ $bill->subtotal }}"
                                        data-tax="{{ $bill->tax }}"
                                        onclick="initPayment(this)"
                                        class="core1-btn-sm core1-btn-primary" 
                                        style="font-size: 10px; padding: 4px 10px;">
                                    <i class="bi bi-wallet2"></i> Pay Now
                                </button> --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center p-40">
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
                            <th>Validated By</th>
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
                            <td class="text-xs">
                                @if($bill->validator)
                                    <span class="font-semibold">{{ $bill->validator->full_name }}</span>
                                    <div class="text-gray">{{ $bill->validated_by }}</div>
                                @else
                                    <span class="text-gray italic">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-40">No recent transactions.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Payment Processing Modal (Context 7 Style) --}}
    <div id="paymentModal" class="core1-modal" style="display:none; z-index:1100; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items:center; justify-content:center; padding: 20px;">
        <div class="core1-modal-content" style="background:white; border-radius:16px; width:100%; max-width:480px; position:relative; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
            <div class="core1-modal-header" style="padding:22px 24px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between; background:white;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div class="core1-icon-box" style="background:var(--primary-light); color:var(--primary); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="bi bi-wallet-fill"></i>
                    </div>
                    <div>
                        <h3 class="core1-title mb-0" style="font-size:17px; font-weight: 700;">Process Payment</h3>
                        <p class="text-xs text-gray mb-0" id="paymentBillNumber" style="font-family: var(--font-mono);">---</p>
                    </div>
                </div>
                <button type="button" onclick="closePaymentModal()" style="background:none; border:none; font-size:1.6rem; color:var(--text-gray); cursor:pointer; line-height: 1;">&times;</button>
            </div>
            
            <form id="paymentForm" method="POST">
                @csrf
                <div class="core1-modal-body" style="padding:24px;">
                    {{-- Patient & Summary Info --}}
                    <div style="margin-bottom: 24px;">
                        <span class="text-xxs text-gray uppercase tracking-wider font-bold mb-8 d-block">Payer Details</span>
                        <div style="background:var(--bg-light); border:1px solid var(--border-color); border-radius:12px; padding:16px;">
                            <div class="core1-flex-between mb-10">
                                <span class="text-xs text-gray">Patient Name</span>
                                <span class="text-sm font-bold text-blue" id="paymentPatientName">---</span>
                            </div>
                            <div style="border-top: 1px dashed var(--border-color); margin: 12px 0;"></div>
                            <div class="core1-flex-between mb-6">
                                <span class="text-xs text-gray">Subtotal</span>
                                <span class="text-xs font-semibold" id="paymentSubtotal">₱0.00</span>
                            </div>
                            <div class="core1-flex-between mb-12">
                                <span class="text-xs text-gray">VAT (12%)</span>
                                <span class="text-xs font-semibold" id="paymentTax">₱0.00</span>
                            </div>
                            <div class="core1-flex-between" style="padding-top: 4px;">
                                <span class="text-sm font-bold">Total Amount Due</span>
                                <span class="text-xl font-black text-blue" id="paymentTotalAmount">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Itemized Breakdown --}}
                    <div style="margin-bottom: 24px;">
                        <span class="text-xxs text-gray uppercase tracking-wider font-bold mb-8 d-block">Bill Breakdown</span>
                        <div id="paymentBreakdown" style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden;">
                            <div id="breakdownLoading" style="padding: 15px; text-align: center; color: var(--text-gray); font-size: 13px;">
                                <i class="bi bi-arrow-repeat spin d-inline-block"></i> Loading itemized charges...
                            </div>
                            <table id="breakdownTable" style="width: 100%; font-size: 13px; display: none;">
                                <thead style="background: var(--bg-light); border-bottom: 1px solid var(--border-color);">
                                    <tr>
                                        <th style="padding: 10px 14px; text-align: left; font-weight: 600;">Service/Item</th>
                                        <th style="padding: 10px 14px; text-align: right; font-weight: 600;">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="breakdownItems">
                                    {{-- Dynamic --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="core1-label" style="font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block;">Amount to Pay</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: bold; color: var(--text-gray);">₱</span>
                            <input type="number" name="amount" id="paymentAmountInput" step="0.01" class="core1-input" required style="padding-left: 32px; font-weight: 700; font-size: 1.1rem; color: var(--primary);">
                        </div>
                    </div>

                    <div class="mb-20">
                        <label class="core1-label" style="font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block;">Payment Method</label>
                        <select name="payment_method" class="core1-input" required style="cursor: pointer; font-weight: 500;">
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="G-Cash">G-Cash (E-Wallet)</option>
                            <option value="Maya">PayMaya (E-Wallet)</option>
                            <option value="Insurance">Insurance Settlement</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="core1-label" style="font-weight: 600; color: var(--text-dark); margin-bottom: 8px; display: block;">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="core1-input" placeholder="OR Number, Transaction ID, etc." style="font-family: var(--font-mono); font-size: 0.85rem;">
                    </div>
                </div>

                <div class="core1-modal-footer" style="padding:20px 24px; background:var(--bg-light); border-top:1px solid var(--border-color); display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closePaymentModal()" style="font-weight: 600;">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary" style="padding: 10px 24px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-shield-check"></i> Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function initPayment(btn) {
        const d = btn.dataset;
        openPaymentModal(d.billId, d.billNumber, d.patientName, d.total, d.subtotal, d.tax);
    }

    function openPaymentModal(billId, billNumber, patientName, total, subtotal, tax) {
        document.getElementById('paymentBillNumber').innerText = 'Bill #: ' + billNumber;
        document.getElementById('paymentPatientName').innerText = patientName;
        
        const formatMoney = (val) => '₱' + parseFloat(val || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        document.getElementById('paymentSubtotal').innerText = formatMoney(subtotal);
        document.getElementById('paymentTax').innerText = formatMoney(tax);
        document.getElementById('paymentTotalAmount').innerText = formatMoney(total);
        
        document.getElementById('paymentAmountInput').value = total;
        document.getElementById('paymentForm').action = '/core/billing/pay/' + billId;
        
        // Breakdown fetching
        const breakdownTable = document.getElementById('breakdownTable');
        const breakdownLoading = document.getElementById('breakdownLoading');
        const breakdownItems = document.getElementById('breakdownItems');
        
        breakdownTable.style.display = 'none';
        breakdownLoading.style.display = 'block';
        breakdownItems.innerHTML = '';

        fetch(`/core/billing/${billId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const items = data.items || [];
            if(items.length > 0) {
                items.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid var(--border-color)';
                    tr.innerHTML = `
                        <td style="padding: 10px 14px; color: var(--text-dark);">${item.desc}</td>
                        <td style="padding: 10px 14px; text-align: right; font-weight: 600;">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    `;
                    breakdownItems.appendChild(tr);
                });
                breakdownLoading.style.display = 'none';
                breakdownTable.style.display = 'table';
            } else {
                breakdownLoading.innerHTML = 'No itemized charges found.';
            }
        })
        .catch(err => {
            console.error('Breakdown fetch error:', err);
            breakdownLoading.innerHTML = '<span class="text-danger">Failed to load breakdown.</span>';
        });

        document.getElementById('paymentModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
        document.body.style.overflow = 'auto';
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
        // No longer using row click since we added explicit Pay Now button
        // This prevents accidental modal opens
    });
</script>
