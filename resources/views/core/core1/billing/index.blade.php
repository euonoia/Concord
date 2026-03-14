@extends('core.core1.layouts.app')

@section('title', 'Billing & Payments')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Billing & Payments</h1>
            <p class="core1-subtitle">Manage patient invoices and payment records</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-receipt"></i>
            </div>
            <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">All Bills</h2>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Bill Number</th>
                        <th>Patient</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td class="font-mono text-sm" style="color: var(--primary);">{{ $bill->bill_number }}</td>
                            <td class="font-bold text-blue">{{ $bill->patient->name }}</td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
                            <td>
                                @php
                                    $statusClass = $bill->status === 'paid' ? 'core1-tag-stable' : ($bill->status === 'overdue' ? 'tag-red' : 'tag-pending');
                                @endphp
                                <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($bill->status) }}</span>
                            </td>
                            <td>
                                <button type="button" onclick="viewBillDetails({{ $bill->id }})" class="core1-btn-sm core1-btn-outline">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-40">
                                <i class="bi bi-receipt-cutoff" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No bills found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $bills->links() }}
        </div>
        @endif
    </div>

    {{-- Bill Details Modal --}}
    <div id="billModal" class="core1-modal" style="display:none; z-index:1050; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="core1-modal-content" style="background:white; border-radius:12px; width:650px; max-width:95%; position:relative; overflow:hidden; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div class="core1-modal-header" style="padding:20px; border-bottom:1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between; background:var(--bg);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="core1-icon-box" style="background:var(--primary-light); color:var(--primary);">
                        <i class="bi bi-file-earmark-medical"></i>
                    </div>
                    <div>
                        <h3 class="core1-title mb-0" style="font-size:16px;">Medical Invoice Details</h3>
                        <p class="text-xs text-gray mb-0" id="modalBillNumber">Loading...</p>
                    </div>
                </div>
                <button type="button" onclick="closeBillModal()" style="background:none; border:none; font-size:1.5rem; color:var(--text-gray); cursor:pointer;">&times;</button>
            </div>
            
            <div id="modalLoading" style="padding: 60px; text-align: center; color: var(--text-gray);">
                <i class="bi bi-arrow-repeat spin" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                Retrieving clinical ledger...
            </div>

            <div id="modalContent" style="display:none;">
                <div class="core1-modal-body" style="padding:24px;">
                    {{-- Patient & Status Header --}}
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px dashed var(--border-color);">
                        <div>
                            <span class="text-xs text-gray uppercase block mb-5" style="letter-spacing:0.5px;">Patient Information</span>
                            <h4 class="text-lg font-bold text-blue mb-0" id="modalPatientName">---</h4>
                            <p class="text-xs font-mono text-gray mt-2" id="modalPatientMRN">MRN: ---</p>
                        </div>
                        <div style="text-align: right;">
                            <span class="text-xs text-gray uppercase block mb-5" style="letter-spacing:0.5px;">Billing Status</span>
                            <div id="modalStatusBadge">---</div>
                            <p class="text-xs text-gray mt-5" id="modalBillDate">Issued: ---</p>
                        </div>
                    </div>

                    {{-- Invoice Items --}}
                    <div class="mb-24">
                        <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--bg); text-align: left;">
                                    <th style="padding: 10px 0; color: var(--text-gray); font-weight: 600;">Service Description</th>
                                    <th style="padding: 10px 0; text-align: right; color: var(--text-gray); font-weight: 600;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsList">
                                {{-- Dynamic --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals Section --}}
                    <div style="background:var(--bg); border-radius:8px; padding:20px; margin-left: auto; width: 280px;">
                        <div class="core1-flex-between mb-8">
                            <span class="text-sm text-gray">Subtotal</span>
                            <span class="text-sm font-semibold" id="modalSubtotal">₱0.00</span>
                        </div>
                        <div class="core1-flex-between mb-8">
                            <span class="text-sm text-gray">VAT (12%)</span>
                            <span class="text-sm font-semibold" id="modalTax">₱0.00</span>
                        </div>
                        <div class="core1-flex-between pt-8" style="border-top: 1px solid var(--border-color);">
                            <span class="text-base font-bold">Total Amount</span>
                            <span class="text-lg font-bold text-blue" id="modalTotal">₱0.00</span>
                        </div>
                    </div>
                </div>
                <div class="core1-modal-footer" style="padding:15px 20px; background:var(--bg); border-top:1px solid var(--border-color); display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Statement
                    </button>
                    <button type="button" class="core1-btn core1-btn-primary" onclick="closeBillModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewBillDetails(billId) {
        const modal = document.getElementById('billModal');
        const loading = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        
        modal.style.display = 'flex';
        loading.style.display = 'block';
        content.style.display = 'none';

        fetch(`/core/billing/${billId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const bill = data.bill;
            const patient = data.patient;
            
            document.getElementById('modalBillNumber').innerText = 'Bill #: ' + bill.bill_number;
            document.getElementById('modalPatientName').innerText = patient.name;
            document.getElementById('modalPatientMRN').innerText = 'MRN: ' + (patient.mrn || 'N/A');
            document.getElementById('modalBillDate').innerText = 'Issued: ' + new Date(bill.bill_date).toLocaleDateString();
            
            // Status Badge
            const statusBadge = document.getElementById('modalStatusBadge');
            let badgeClass = 'core1-status-tag core1-tag-neutral';
            if(bill.status === 'paid') badgeClass = 'core1-status-tag core1-tag-stable';
            if(bill.status === 'partial') badgeClass = 'core1-status-tag core1-tag-recovering';
            if(bill.status === 'overdue') badgeClass = 'core1-status-tag core1-tag-critical';
            statusBadge.innerHTML = `<span class="${badgeClass}">${bill.status.toUpperCase()}</span>`;

            // Items
            const itemsList = document.getElementById('modalItemsList');
            itemsList.innerHTML = '';
            const items = data.items || [];
            
            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid var(--bg)';
                tr.innerHTML = `
                    <td style="padding: 12px 0; color: var(--text-dark);">${item.desc}</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 500;">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                `;
                itemsList.appendChild(tr);
            });

            if(items.length === 0) {
                itemsList.innerHTML = '<tr><td colspan="2" style="padding: 24px; text-align: center; color: var(--text-gray);">No itemized charges found for this encounter.</td></tr>';
            }

            // Totals
            document.getElementById('modalSubtotal').innerText = '₱' + parseFloat(bill.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('modalTax').innerText = '₱' + parseFloat(bill.tax).toLocaleString(undefined, {minimumFractionDigits: 2});
            document.getElementById('modalTotal').innerText = '₱' + parseFloat(bill.total).toLocaleString(undefined, {minimumFractionDigits: 2});

            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(err => {
            console.error('Error fetching bill details:', err);
            alert('Failed to load bill details. Please check connection and retry.');
            closeBillModal();
        });
    }

    function closeBillModal() {
        document.getElementById('billModal').style.display = 'none';
    }

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('billModal');
        if (event.target == modal) {
            closeBillModal();
        }
    }
</script>

<style>
    .spin {
        animation: rotate 2s linear infinite;
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
