@extends('admin._logistics1.layouts.app')

@section('content')

<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-header h4 { font-size: 1.3rem; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }
    .page-header p  { font-size: 0.82rem; color: #94a3b8; margin: 0; }

    .btn-add { background: #1e293b; color: #fff; border: none; padding: 0.45rem 1.1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.18s; cursor: pointer; }
    .btn-add:hover { background: #334155; color: #fff; }

    /* Tabs */
    .proc-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 5px; border-radius: 10px; margin-bottom: 1.5rem; width: fit-content; }
    .proc-tab  { padding: 0.45rem 1rem; border-radius: 7px; font-size: 0.82rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; white-space: nowrap; }
    .proc-tab:hover  { color: #1e293b; background: #e2e8f0; }
    .proc-tab.active { background: #1e293b; color: #fff; box-shadow: 0 2px 8px rgba(30,41,59,0.18); }

    .tab-desc { font-size: 0.82rem; color: #64748b; margin-bottom: 1.1rem; }

    .filter-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.9rem 1rem; margin-bottom: 1.25rem; }
    .filter-bar .form-control,
    .filter-bar .form-select { border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.82rem; background: #fff; color: #1e293b; padding: 0.4rem 0.75rem; box-shadow: none; }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .btn-filter { background: #1e293b; color: #fff; border: none; border-radius: 7px; font-size: 0.82rem; font-weight: 600; padding: 0.4rem 1rem; cursor: pointer; }
    .btn-filter:hover { background: #334155; }

    .data-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
    .data-table { margin: 0; font-size: 0.82rem; }
    .data-table thead tr { background: #1e293b; }
    .data-table thead th { color: #94a3b8; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; padding: 0.85rem 1rem; border: none; white-space: nowrap; }
    .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody td { padding: 0.75rem 1rem; color: #334155; border: none; vertical-align: middle; }
    .row-num   { color: #94a3b8; font-size: 0.75rem; }
    .code-pill { background: #f1f5f9; color: #475569; border-radius: 5px; padding: 2px 8px; font-size: 0.73rem; font-family: monospace; font-weight: 600; }
    .row-title { font-weight: 600; color: #1e293b; }
    .row-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .badge-pill { border-radius: 20px; padding: 3px 10px; font-size: 0.72rem; font-weight: 600; display: inline-block; }
    .bp-pending   { background: #fef9c3; color: #a16207; }
    .bp-approved  { background: #dbeafe; color: #1d4ed8; }
    .bp-ordered   { background: #ede9fe; color: #6d28d9; }
    .bp-shipped   { background: #ffedd5; color: #c2410c; }
    .bp-delivered { background: #dcfce7; color: #16a34a; }
    .bp-paid      { background: #dcfce7; color: #16a34a; }
    .bp-inactive  { background: #f1f5f9; color: #475569; }
    .bp-Low\ Stock     { background: #fef9c3; color: #a16207; }
    .bp-Critical       { background: #fee2e2; color: #b91c1c; }
    .bp-Out\ of\ Stock { background: #f1f5f9; color: #1e293b; }

    .btn-tbl { border: none; border-radius: 7px; font-size: 0.75rem; font-weight: 600; padding: 5px 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s; }
    .btn-tbl-approve { background: #dcfce7; color: #16a34a; }
    .btn-tbl-approve:hover { background: #bbf7d0; }
    .btn-tbl-order   { background: #dbeafe; color: #1d4ed8; }
    .btn-tbl-order:hover   { background: #bfdbfe; }
    .btn-tbl-ship    { background: #ffedd5; color: #c2410c; }
    .btn-tbl-ship:hover    { background: #fed7aa; }
    .btn-tbl-deliver { background: #dcfce7; color: #16a34a; }
    .btn-tbl-deliver:hover { background: #bbf7d0; }
    .btn-tbl-pay     { background: #dcfce7; color: #16a34a; }
    .btn-tbl-pay:hover     { background: #bbf7d0; }
    .btn-tbl-restock { background: #1e293b; color: #fff; }
    .btn-tbl-restock:hover { background: #334155; }

    .vendor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
    .vendor-card {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 1.25rem 1rem; text-align: center; cursor: pointer;
        transition: all 0.18s; position: relative; overflow: hidden;
    }
    .vendor-card:hover { border-color: #1e293b; box-shadow: 0 6px 20px rgba(30,41,59,0.1); transform: translateY(-2px); }
    .vendor-card .vc-icon { width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.6rem; font-size: 1.2rem; color: #475569; }
    .vendor-card:hover .vc-icon { background: #1e293b; color: #fff; }
    .vendor-card .vc-name { font-size: 0.83rem; font-weight: 700; color: #1e293b; line-height: 1.3; word-break: break-word; }
    .vendor-card .vc-cat  { font-size: 0.72rem; color: #94a3b8; margin-top: 3px; }
    .vendor-card .vc-badge { margin-top: 8px; }

    .empty-state { padding: 3.5rem 1rem; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2.2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }

    .paid-label { color: #16a34a; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }

    .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header  { background: #1e293b; border-radius: 14px 14px 0 0; padding: 1rem 1.25rem; border: none; }
    .modal-header .modal-title { color: #f8fafc; font-size: 0.95rem; font-weight: 700; }
    .modal-header .btn-close   { filter: invert(1) brightness(2); }
    .modal-body  { padding: 1.25rem; }
    .modal-footer { padding: 0.9rem 1.25rem; border-top: 1px solid #f1f5f9; }
    .modal-body .form-label { font-size: 0.78rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .modal-body .form-control,
    .modal-body .form-select { font-size: 0.83rem; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; padding: 0.45rem 0.75rem; box-shadow: none; }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .modal-body .form-control:disabled { background: #f8fafc; color: #64748b; }
    .btn-mc { background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 1rem; cursor: pointer; }
    .btn-mc:hover { background: #e2e8f0; }
    .btn-ms { background: #1e293b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-ms:hover { background: #334155; color: #fff; }
</style>

{{-- Page Header --}}
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-truck me-2"></i>Procurement & Suppliers</h4>
        <p>Manage inventory requests, vendors, orders and payments</p>
    </div>
</div>

{{-- Tab Navigation --}}
<div class="proc-tabs">
    <a class="proc-tab {{ $activeTab === 'needs_assessment'   ? 'active' : '' }}" href="{{ route('admin.logistics1.procurement.index', ['tab' => 'needs_assessment']) }}">
        <i class="bi bi-clipboard-check"></i> Needs Assessment
    </a>
    <a class="proc-tab {{ $activeTab === 'vendor_selection'   ? 'active' : '' }}" href="{{ route('admin.logistics1.procurement.index', ['tab' => 'vendor_selection']) }}">
        <i class="bi bi-shop"></i> Vendor Selection
    </a>
    <a class="proc-tab {{ $activeTab === 'purchase_orders'    ? 'active' : '' }}" href="{{ route('admin.logistics1.procurement.index', ['tab' => 'purchase_orders']) }}">
        <i class="bi bi-receipt"></i> Purchase Orders
    </a>
    <a class="proc-tab {{ $activeTab === 'payment_processing' ? 'active' : '' }}" href="{{ route('admin.logistics1.procurement.index', ['tab' => 'payment_processing']) }}">
        <i class="bi bi-credit-card"></i> Payment Processing
    </a>
</div>


{{-- ===== TAB 1: NEEDS ASSESSMENT ===== --}}
@if($activeTab === 'needs_assessment')

<p class="tab-desc">Items currently at Low Stock, Critical, or Out of Stock status.</p>

<form method="GET" action="{{ route('admin.logistics1.procurement.index') }}">
    <input type="hidden" name="tab" value="needs_assessment">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by drug name or number..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Search</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Drug No.</th>
                <th>Drug Name</th>
                <th>Current Stock</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $item)
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($inventory->currentPage()-1) * $inventory->perPage() }}</span></td>
                <td><span class="code-pill">{{ $item->drug_num }}</span></td>
                <td><span class="row-title">{{ $item->drug_name }}</span></td>
                <td>{{ $item->quantity ?? 0 }}</td>
                <td>
                    <button class="btn-tbl btn-tbl-restock"
                        data-bs-toggle="modal" data-bs-target="#addRequestModal"
                        data-drug_num="{{ $item->drug_num }}"
                        data-drug_name="{{ $item->drug_name }}"
                        data-quantity="{{ $item->quantity }}">
                        <i class="bi bi-file-earmark-text me-1"></i> Convert to PO
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><i class="bi bi-check-circle"></i><p>All inventory levels are sufficient.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $inventory->withQueryString()->links() }}</div>

{{-- Convert to PO Modal --}}
<div class="modal fade" id="addRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.procurement.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Convert to Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Drug No.</label>
                            <input type="text" name="drug_num" id="modal_drug_num" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drug Name</label>
                            <input type="text" name="drug_name" id="modal_drug_name" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="requested_quantity" id="modal_quantity" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Requested Date</label>
                            <input type="date" name="requested_date" id="modal_requested_date" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date" id="modal_delivery_date" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Select Vendor <span class="text-danger">*</span></label>
                            <select name="selected_supplier" id="modal_vendor" class="form-select" required>
                                <option value="">— Choose a vendor —</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_name }}">
                                    {{ $vendor->vendor_name }}
                                    @if($vendor->category) — {{ $vendor->category }} @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Delivered By</label>
                            <input type="text" name="delivered_by" class="form-control" placeholder="e.g. John Doe" maxlength="255">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Delivery address" maxlength="255">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-ms">
                        <i class="bi bi-file-earmark-check me-1"></i> Create PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('addRequestModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (!btn) return;

    document.getElementById('modal_drug_num').value  = btn.dataset.drug_num  ?? '';
    document.getElementById('modal_drug_name').value = btn.dataset.drug_name ?? '';
    document.getElementById('modal_quantity').value  = btn.dataset.quantity  ?? '';

    const today = new Date();
    const toDateStr = d => d.toISOString().split('T')[0];
    document.getElementById('modal_requested_date').value = toDateStr(today);

    const delivery = new Date(today);
    delivery.setMonth(delivery.getMonth() + 1);
    document.getElementById('modal_delivery_date').value = toDateStr(delivery);

    document.getElementById('modal_vendor').value = '';
});
</script>
@endif


{{-- ===== TAB 2: VENDOR SELECTION ===== --}}
@if($activeTab === 'vendor_selection')
<p class="tab-desc">Click on a vendor to view their details.</p>

@if($vendors->isEmpty())
    <div class="data-card"><div class="empty-state"><i class="bi bi-shop"></i><p>No vendors found in the vendor portal.</p></div></div>
@else
<div class="vendor-grid">
    @foreach($vendors as $vendor)
    <div class="vendor-card"
        data-bs-toggle="modal"
        data-bs-target="#vendorDetailModal"
        data-name="{{ $vendor->vendor_name }}"
        data-category="{{ $vendor->category ?? '—' }}"
        data-status="{{ ucfirst(str_replace('_', ' ', $vendor->status)) }}"
        data-email="{{ $vendor->email ?? '—' }}"
        data-phone="{{ $vendor->phone ?? '—' }}"
        data-address="{{ $vendor->address ?? '—' }}"
        title="View {{ $vendor->vendor_name }}">
        <div class="vc-icon"><i class="bi bi-building"></i></div>
        <div class="vc-name">{{ $vendor->vendor_name }}</div>
        @if($vendor->category)<div class="vc-cat">{{ $vendor->category }}</div>@endif
        <div class="vc-badge">
            <span class="badge-pill {{ $vendor->status === 'active' ? 'bp-delivered' : 'bp-pending' }}" style="font-size:0.68rem;">
                {{ ucfirst(str_replace('_', ' ', $vendor->status)) }}
            </span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Vendor Detail Modal --}}
<div class="modal fade" id="vendorDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Vendor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Vendor Info --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" id="detail_name" class="form-control" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <input type="text" id="detail_category" class="form-control" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <input type="text" id="detail_status" class="form-control" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" id="detail_email" class="form-control" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" id="detail_phone" class="form-control" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" id="detail_address" class="form-control" disabled>
                    </div>
                </div>

                {{-- Goods Receipt Section --}}
                <hr style="border-color:#e2e8f0;">
                <p style="font-size:0.85rem; font-weight:700; color:#1e293b; margin-bottom:0.75rem;">
                    <i class="bi bi-receipt me-1"></i> Goods Receipt
                    <span style="font-size:0.75rem; font-weight:400; color:#94a3b8; margin-left:6px;">Paid purchase orders from this vendor</span>
                </p>
                <div id="goodsReceiptLoading" style="display:none; text-align:center; padding:1.5rem; color:#94a3b8; font-size:0.82rem;">
                    <i class="bi bi-arrow-repeat"></i> Loading...
                </div>
                <div id="goodsReceiptEmpty" style="display:none; text-align:center; padding:1.5rem; color:#94a3b8; font-size:0.82rem;">
                    <i class="bi bi-inbox" style="font-size:1.5rem; display:block; margin-bottom:0.4rem; opacity:0.4;"></i>
                    No paid orders found for this vendor.
                </div>
                <div id="goodsReceiptTable" style="display:none; overflow-x:auto;">
                    <table class="table data-table" style="font-size:0.8rem; margin:0;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>PO Number</th>
                                <th>Drug</th>
                                <th>Qty</th>
                                <th>Delivered By</th>
                                <th>Delivery Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="goodsReceiptBody"></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-mc" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('vendorDetailModal').addEventListener('show.bs.modal', function (e) {
    const card = e.relatedTarget;
    const vendorName = card.dataset.name ?? '—';

    document.getElementById('detail_name').value     = vendorName;
    document.getElementById('detail_category').value = card.dataset.category ?? '—';
    document.getElementById('detail_status').value   = card.dataset.status   ?? '—';
    document.getElementById('detail_email').value    = card.dataset.email    ?? '—';
    document.getElementById('detail_phone').value    = card.dataset.phone    ?? '—';
    document.getElementById('detail_address').value  = card.dataset.address  ?? '—';

    // Reset goods receipt section
    document.getElementById('goodsReceiptLoading').style.display = 'block';
    document.getElementById('goodsReceiptEmpty').style.display   = 'none';
    document.getElementById('goodsReceiptTable').style.display   = 'none';
    document.getElementById('goodsReceiptBody').innerHTML        = '';

    // Fetch paid POs for this vendor
    fetch('{{ route("admin.logistics1.procurement.goods_receipt") }}?vendor=' + encodeURIComponent(vendorName))
        .then(res => res.json())
        .then(data => {
            document.getElementById('goodsReceiptLoading').style.display = 'none';
            if (!data || data.length === 0) {
                document.getElementById('goodsReceiptEmpty').style.display = 'block';
                return;
            }
            const tbody = document.getElementById('goodsReceiptBody');
            data.forEach((row, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td><span class="row-num">${i + 1}</span></td>
                        <td><span class="code-pill">${row.invoice ?? '—'}</span></td>
                        <td><span class="code-pill">${row.po_number ?? '—'}</span></td>
                        <td>
                            <div class="row-title">${row.drug_name ?? '—'}</div>
                            <div class="row-sub">${row.drug_num ?? '—'}</div>
                        </td>
                        <td>${row.requested_quantity ?? '—'}</td>
                        <td>${row.delivered_by ?? '—'}</td>
                        <td style="font-size:0.78rem;">${row.delivery_date ?? '—'}</td>
                        <td style="font-weight:600; color:#16a34a;">₱${row.amount ?? '0.00'}</td>
                    </tr>`;
            });
            document.getElementById('goodsReceiptTable').style.display = 'block';
        })
        .catch(() => {
            document.getElementById('goodsReceiptLoading').style.display = 'none';
            document.getElementById('goodsReceiptEmpty').style.display   = 'block';
        });
});
</script>
@endif


{{-- ===== TAB 3: PURCHASE ORDERS ===== --}}
@if($activeTab === 'purchase_orders')
<p class="tab-desc">Purchase orders created from the Needs Assessment tab.</p>

<form method="GET" action="{{ route('admin.logistics1.procurement.index') }}">
    <input type="hidden" name="tab" value="purchase_orders">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by PO no., drug, or supplier..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="po_status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('po_status')==='pending'   ?'selected':'' }}>Pending</option>
                <option value="approved"  {{ request('po_status')==='approved'  ?'selected':'' }}>Approved</option>
                <option value="ordered"   {{ request('po_status')==='ordered'   ?'selected':'' }}>Ordered</option>
                <option value="shipped"   {{ request('po_status')==='shipped'   ?'selected':'' }}>Shipped</option>
                <option value="delivered" {{ request('po_status')==='delivered' ?'selected':'' }}>Delivered</option>
                <option value="paid"      {{ request('po_status')==='paid'      ?'selected':'' }}>Paid</option>
                <option value="cancelled" {{ request('po_status')==='cancelled' ?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>PO Number</th>
                <th>Drug</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Delivered By</th>
                <th>Address</th>
                <th>Requested Date</th>
                <th>Expected Delivery</th>
                <th>Requested By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $order)
            @php
                $poStatusMap = [
                    'pending'   => 'bp-pending',
                    'approved'  => 'bp-approved',
                    'ordered'   => 'bp-ordered',
                    'shipped'   => 'bp-shipped',
                    'delivered' => 'bp-delivered',
                    'paid'      => 'bp-paid',
                    'cancelled' => 'bp-inactive',
                ];
            @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($purchaseOrders->currentPage()-1) * $purchaseOrders->perPage() }}</span></td>
                <td><span class="code-pill">{{ $order->po_number }}</span></td>
                <td>
                    <div class="row-title">{{ $order->drug_name }}</div>
                    <div class="row-sub">{{ $order->drug_num }}</div>
                </td>
                <td>{{ $order->selected_supplier }}</td>
                <td>{{ $order->requested_quantity }}</td>
                <td><span class="badge-pill {{ $poStatusMap[$order->status] ?? 'bp-pending' }}">{{ ucfirst($order->status) }}</span></td>
                <td>{{ $order->delivered_by ?? '—' }}</td>
                <td>{{ $order->address ?? '—' }}</td>
                <td style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($order->requested_date)->format('M d, Y') }}</td>
                <td style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('M d, Y') }}</td>
                <td style="font-size:0.78rem;">{{ $order->req_first_name }} {{ $order->req_last_name }}</td>
                <td>
                    @if($order->status === 'pending')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-tbl btn-tbl-approve"><i class="bi bi-check-lg"></i> Approve</button>
                    </form>
                    @elseif($order->status === 'approved')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf <input type="hidden" name="status" value="ordered">
                        <button type="submit" class="btn-tbl btn-tbl-order"><i class="bi bi-bag-check"></i> Mark Ordered</button>
                    </form>
                    @elseif($order->status === 'ordered')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf <input type="hidden" name="status" value="shipped">
                        <button type="submit" class="btn-tbl btn-tbl-ship"><i class="bi bi-truck"></i> Mark Shipped</button>
                    </form>
                    @elseif($order->status === 'shipped')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn-tbl btn-tbl-deliver"><i class="bi bi-box-seam"></i> Mark Delivered</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="12"><div class="empty-state"><i class="bi bi-receipt"></i><p>No purchase orders yet. Convert a Needs Assessment item to create one.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $purchaseOrders->withQueryString()->links() }}</div>
@endif


{{-- ===== TAB 4: PAYMENT PROCESSING ===== --}}
@if($activeTab === 'payment_processing')
<p class="tab-desc">Delivered orders awaiting payment confirmation or already settled.</p>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>PO Number</th>
                <th>Drug</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Delivered By</th>
                <th>Address</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($payments->currentPage()-1) * $payments->perPage() }}</span></td>
                <td><span class="code-pill">{{ $payment->po_number }}</span></td>
                <td>
                    <div class="row-title">{{ $payment->drug_name }}</div>
                    <div class="row-sub">{{ $payment->drug_num }}</div>
                </td>
                <td>{{ $payment->selected_supplier }}</td>
                <td>{{ $payment->requested_quantity }}</td>
                <td>{{ $payment->delivered_by ?? '—' }}</td>
                <td>{{ $payment->address ?? '—' }}</td>
                <td style="font-size:0.78rem;">{{ $payment->req_first_name }} {{ $payment->req_last_name }}</td>
                <td><span class="badge-pill bp-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                <td style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                <td>
                    @if($payment->status === 'delivered')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $payment->id) }}" style="display:inline;" onsubmit="return confirm('Mark this order as paid?')">
                        @csrf <input type="hidden" name="status" value="paid">
                        <button type="submit" class="btn-tbl btn-tbl-pay"><i class="bi bi-check-circle"></i> Mark Paid</button>
                    </form>
                    @else
                        <span class="paid-label"><i class="bi bi-check-circle-fill"></i> Paid</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="11"><div class="empty-state"><i class="bi bi-credit-card"></i><p>No payments to process.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $payments->withQueryString()->links() }}</div>
@endif

@endsection