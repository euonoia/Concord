@extends('admin._logistics1.layouts.app')

@section('content')

<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-header h4 { font-size: 1.3rem; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }
    .page-header p  { font-size: 0.82rem; color: #94a3b8; margin: 0; }

    .wh-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 5px; border-radius: 10px; margin-bottom: 1.5rem; width: fit-content; }
    .wh-tab  { padding: 0.45rem 1.1rem; border-radius: 7px; font-size: 0.82rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; white-space: nowrap; }
    .wh-tab:hover  { color: #1e293b; background: #e2e8f0; }
    .wh-tab.active { background: #1e293b; color: #fff; box-shadow: 0 2px 8px rgba(30,41,59,0.18); }

    .tab-desc { font-size: 0.82rem; color: #64748b; margin-bottom: 1.1rem; }

    .filter-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.9rem 1rem; margin-bottom: 1.25rem; }
    .filter-bar .form-control,
    .filter-bar .form-select { border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.82rem; background: #fff; color: #1e293b; padding: 0.4rem 0.75rem; box-shadow: none; }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .btn-filter { background: #1e293b; color: #fff; border: none; border-radius: 7px; font-size: 0.82rem; font-weight: 600; padding: 0.4rem 1.1rem; cursor: pointer; }
    .btn-filter:hover { background: #334155; }
    .btn-add { background: #1e293b; color: #fff; border: none; padding: 0.45rem 1.1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.18s; cursor: pointer; }
    .btn-add:hover { background: #334155; color: #fff; }
    .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header  { background: #1e293b; border-radius: 14px 14px 0 0; padding: 1rem 1.25rem; border: none; }
    .modal-header .modal-title { color: #f8fafc; font-size: 0.95rem; font-weight: 700; }
    .modal-header .btn-close   { filter: invert(1) brightness(2); }
    .modal-body  { padding: 1.25rem; }
    .modal-footer { padding: 0.9rem 1.25rem; border-top: 1px solid #f1f5f9; }
    .modal-body .form-label { font-size: 0.78rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .modal-body .form-select { font-size: 0.83rem; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; padding: 0.45rem 0.75rem; box-shadow: none; }
    .modal-body .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .btn-mc { background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 1rem; cursor: pointer; }
    .btn-mc:hover { background: #e2e8f0; }
    .btn-ms { background: #1e293b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-ms:hover { background: #334155; color: #fff; }
    .btn-mu { background: #f59e0b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-mu:hover { background: #d97706; }
    .btn-act { width: 30px; height: 30px; border-radius: 7px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; transition: all 0.15s; cursor: pointer; vertical-align: middle; }
    .btn-act-edit { background: #fef9c3; color: #a16207; }
    .btn-act-edit:hover { background: #fde68a; }
    .btn-act-delete { background: #fee2e2; color: #b91c1c; }
    .btn-act-delete:hover { background: #fecaca; }
    .btn-act-restock-text { height: 30px; border-radius: 7px; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 5px; font-size: 0.75rem; font-weight: 600; padding: 0 10px; background: #dbeafe; color: #1d4ed8; cursor: pointer; transition: all 0.15s; vertical-align: middle; white-space: nowrap; }
    .btn-act-restock-text:hover { background: #bfdbfe; }
    .po-option-detail { font-size: 0.75rem; color: #94a3b8; }

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

    .qty-ok       { font-weight: 700; color: #1e293b; }
    .qty-critical { font-weight: 700; color: #b91c1c; }

    .expiry-ok      { font-size: 0.78rem; color: #475569; }
    .expiry-soon    { font-size: 0.78rem; font-weight: 600; color: #c2410c; }
    .expiry-expired { font-size: 0.78rem; font-weight: 600; color: #b91c1c; }

    .supplier-text { font-size: 0.78rem; color: #94a3b8; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }

    .badge-pill  { border-radius: 20px; padding: 3px 10px; font-size: 0.72rem; font-weight: 600; display: inline-block; }
    .bp-stable   { background: #dcfce7; color: #16a34a; }
    .bp-low      { background: #fef9c3; color: #a16207; }
    .bp-critical { background: #fee2e2; color: #b91c1c; }
    .bp-out      { background: #fee2e2; color: #b91c1c; white-space: nowrap; }
    .bp-new      { background: #dbeafe; color: #1d4ed8; }
    .bp-ordered  { background: #ede9fe; color: #6d28d9; }
    .bp-shipped  { background: #ffedd5; color: #c2410c; }

    .empty-state { padding: 3.5rem 1rem; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2.2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }
</style>

<div class="page-header">
    <h4><i class="bi bi-archive me-2"></i>Warehouse Inventory</h4>
    <p>Real-time stock levels for Logistics 1</p>
</div>

{{-- Tab Navigation --}}
<div class="wh-tabs">
    <a class="wh-tab {{ $activeTab === 'receiving'         ? 'active' : '' }}"
       href="{{ route('admin.logistics1.warehouse.index', ['tab' => 'receiving']) }}">
        <i class="bi bi-box-arrow-in-down"></i> Receiving & Inspection
    </a>
    <a class="wh-tab {{ $activeTab === 'inventory_control' ? 'active' : '' }}"
       href="{{ route('admin.logistics1.warehouse.index', ['tab' => 'inventory_control']) }}">
        <i class="bi bi-clipboard-data"></i> Inventory Control
    </a>
    <a class="wh-tab {{ $activeTab === 'dispatch'          ? 'active' : '' }}"
       href="{{ route('admin.logistics1.warehouse.index', ['tab' => 'dispatch']) }}">
        <i class="bi bi-box-arrow-up-right"></i> Dispatch & Distribution
    </a>
</div>


{{-- ===== TAB 1: RECEIVING & INSPECTION ===== --}}
@if($activeTab === 'receiving')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="tab-desc mb-0">Incoming purchase orders logged in the receiving register.</p>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#receivePoModal">
        <i class="bi bi-box-arrow-in-down me-1"></i> Receive PO
    </button>
</div>
<form action="{{ route('admin.logistics1.warehouse.index') }}" method="GET">
    <input type="hidden" name="tab" value="receiving">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by PO no., drug, or supplier..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="receiving_status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending"   {{ request('receiving_status')==='pending'   ?'selected':'' }}>Pending</option>
                <option value="approved"  {{ request('receiving_status')==='approved'  ?'selected':'' }}>Approved</option>
                <option value="ordered"   {{ request('receiving_status')==='ordered'   ?'selected':'' }}>Ordered</option>
                <option value="shipped"   {{ request('receiving_status')==='shipped'   ?'selected':'' }}>Shipped</option>
                <option value="delivered" {{ request('receiving_status')==='delivered' ?'selected':'' }}>Delivered</option>
                <option value="paid"      {{ request('receiving_status')==='paid'      ?'selected':'' }}>Paid</option>
                <option value="cancelled" {{ request('receiving_status')==='cancelled' ?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100"><i class="bi bi-search me-1"></i> Filter</button>
        </div>
    </div>
</form>
<div class="data-card" style="overflow-x:auto;">
    <table class="table data-table" style="min-width:1000px;">
        <thead>
            <tr>
                <th>#</th>
                <th>PO Number</th>
                <th>Drug</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Model Name</th>
                <th>Actual Qty</th>
                <th>Bad Orders</th>
                <th>Inspector</th>
                <th>Requested Date</th>
                <th>Expected Delivery</th>
                <th>Requested By</th>
                <th>Status</th>
                <th></th>
        </thead>
        <tbody>
            @forelse($receiving as $item)
            @php
                $poStatusMap = [
                    'pending'   => 'bp-low',
                    'approved'  => 'bp-new',
                    'ordered'   => 'bp-ordered',
                    'shipped'   => 'bp-shipped',
                    'delivered' => 'bp-stable',
                    'paid'      => 'bp-stable',
                    'cancelled' => 'bp-out',
                ];
            @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($receiving->currentPage()-1) * $receiving->perPage() }}</span></td>
                <td><span class="code-pill">{{ $item->po_number }}</span></td>
                <td>
                    <div class="row-title">{{ $item->drug_name }}</div>
                    <div class="row-sub">{{ $item->drug_num }}</div>
                </td>
                <td><span class="supplier-text" title="{{ $item->selected_supplier }}">{{ $item->selected_supplier }}</span></td>
                <td><span class="qty-ok">{{ $item->requested_quantity }}</span></td>
                <td style="font-size:0.78rem;">{{ $item->model_name ?? '—' }}</td>
                <td style="font-size:0.78rem;">{{ $item->actual_quantity ?? '—' }}</td>
                <td style="font-size:0.78rem;">{{ $item->bad_orders ?? '—' }}</td>
                <td style="font-size:0.78rem;">{{ $item->inspector ?? '—' }}</td>
                <td style="font-size:0.78rem;color:#64748b;">{{ \Carbon\Carbon::parse($item->requested_date)->format('M d, Y') }}</td>
                <td style="font-size:0.78rem;color:#64748b;">{{ \Carbon\Carbon::parse($item->expected_delivery_date)->format('M d, Y') }}</td>
                <td style="font-size:0.78rem;">{{ $item->req_first_name }} {{ $item->req_last_name }}</td>
                <td><span class="badge-pill {{ $poStatusMap[$item->status] ?? 'bp-low' }}">{{ ucfirst($item->status) }}</span></td>
                <td style="white-space: nowrap;">
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 6px;">
                        <button class="btn-act btn-act-edit" title="Update Status"
                            data-bs-toggle="modal"
                            data-bs-target="#updateReceivingModal"
                            data-id="{{ $item->id }}"
                            data-status="{{ $item->status }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.logistics1.warehouse.receiving_delete', $item->id) }}" style="display:inline-flex; margin:0;" onsubmit="return confirm('Delete this record?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-act btn-act-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="14"><div class="empty-state"><i class="bi bi-box-arrow-in-down"></i><p>No receiving records found.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $receiving->links() }}</div>

{{-- Update Receiving Status Modal --}}
<div class="modal fade" id="updateReceivingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="updateReceivingForm">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="update_status" class="form-select" required onchange="toggleDeliveryFields(this.value)">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="ordered">Ordered</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    {{-- Delivery Details (shown only when status = delivered) --}}
                    <div id="deliveryFields" style="display:none; margin-top:1rem;">
                        <hr style="border-color:#e2e8f0;">
                        <p style="font-size:0.78rem; font-weight:600; color:#475569; margin-bottom:0.75rem;">Delivery Details</p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Actual Quantity Delivered <span class="text-danger">*</span></label>
                                <input type="number" name="actual_quantity" id="actual_quantity" class="form-control" min="0" placeholder="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bad Orders</label>
                                <input type="number" name="bad_orders" id="bad_orders" class="form-control" min="0" placeholder="0" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Inspector <span class="text-danger">*</span></label>
                                <input type="text" name="inspector" id="inspector" class="form-control" placeholder="Inspector name" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-ms"><i class="bi bi-check-lg me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDeliveryFields(status) {
    var fields = document.getElementById('deliveryFields');
    var actualQty = document.getElementById('actual_quantity');
    var inspector = document.getElementById('inspector');
    if (status === 'delivered') {
        fields.style.display = 'block';
        actualQty.required = true;
        inspector.required = true;
    } else {
        fields.style.display = 'none';
        actualQty.required = false;
        inspector.required = false;
    }
}

document.getElementById('updateReceivingModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var form = document.getElementById('updateReceivingForm');
    form.action = '{{ route("admin.logistics1.warehouse.receiving_update", ["id" => "__ID__"]) }}'.replace('__ID__', btn.dataset.id);
    document.getElementById('update_status').value = btn.dataset.status;
    toggleDeliveryFields(btn.dataset.status);
    document.getElementById('actual_quantity').value = '';
    document.getElementById('bad_orders').value = '0';
    document.getElementById('inspector').value = '';
});
</script>
<div class="modal fade" id="receivePoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.warehouse.receive_po') }}">
                @csrf
                <input type="hidden" name="po_source" id="poSource" value="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-down me-2"></i>Receive Purchase Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Approved PO <span class="text-danger">*</span></label>
                        <select name="po_id" class="form-select" required id="poSelect">
                            <option value="">— Choose an approved PO —</option>
                            @forelse($approvedPOs as $po)
                            <option value="{{ $po->id }}"
                                data-source="{{ isset($po->source) && $po->source === 'inventory_control' ? 'warehouse_purchaseorders' : 'purchase_orders' }}"
                                data-drug="{{ $po->drug_name }}"
                                data-num="{{ $po->drug_num }}"
                                data-supplier="{{ $po->selected_supplier }}"
                                data-qty="{{ $po->requested_quantity }}"
                                data-delivery="{{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('M d, Y') }}">
                                {{ $po->po_number }} — {{ $po->drug_name }}
                            </option>
                            @empty
                            <option value="" disabled>No approved POs available</option>
                            @endforelse
                        </select>
                    </div>

                    {{-- PO Details preview --}}
                    <div id="poPreview" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.9rem 1rem;">
                        <div class="row g-2" style="font-size:0.8rem;">
                            <div class="col-6">
                                <div style="color:#94a3b8; font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Drug</div>
                                <div style="font-weight:600; color:#1e293b;" id="preview_drug"></div>
                                <div style="color:#94a3b8; font-size:0.73rem;" id="preview_num"></div>
                            </div>
                            <div class="col-6">
                                <div style="color:#94a3b8; font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Supplier</div>
                                <div style="font-weight:600; color:#1e293b;" id="preview_supplier"></div>
                            </div>
                            <div class="col-6 mt-2">
                                <div style="color:#94a3b8; font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Qty</div>
                                <div style="font-weight:700; color:#1e293b;" id="preview_qty"></div>
                            </div>
                            <div class="col-6 mt-2">
                                <div style="color:#94a3b8; font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Expected Delivery</div>
                                <div style="font-weight:600; color:#1e293b;" id="preview_delivery"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Inspector --}}
                    <div class="mt-3">
                        <label class="form-label">Inspector <span class="text-danger">*</span></label>
                        <input type="text" name="inspector" class="form-control" placeholder="Inspector name" maxlength="255" required>
                    </div>

                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('poSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const preview = document.getElementById('poPreview');
    if (!this.value) { preview.style.display = 'none'; document.getElementById('poSource').value = ''; return; }
    document.getElementById('poSource').value           = opt.dataset.source;
    document.getElementById('preview_drug').textContent     = opt.dataset.drug;
    document.getElementById('preview_num').textContent      = opt.dataset.num;
    document.getElementById('preview_supplier').textContent = opt.dataset.supplier;
    document.getElementById('preview_qty').textContent      = opt.dataset.qty;
    document.getElementById('preview_delivery').textContent = opt.dataset.delivery;
    preview.style.display = 'block';
});
</script>
@endif


{{-- ===== TAB 2: INVENTORY CONTROL ===== --}}
@if($activeTab === 'inventory_control')
<p class="tab-desc">Full stock overview with real-time status tracking.</p>
<form action="{{ route('admin.logistics1.warehouse.index') }}" method="GET">
    <input type="hidden" name="tab" value="inventory_control">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by drug name or SKU..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="Stable"       {{ request('status')==='Stable'       ?'selected':'' }}>Stable</option>
                <option value="Low Stock"    {{ request('status')==='Low Stock'    ?'selected':'' }}>Low Stock</option>
                <option value="Critical"     {{ request('status')==='Critical'     ?'selected':'' }}>Critical</option>
                <option value="Out of Stock" {{ request('status')==='Out of Stock' ?'selected':'' }}>Out of Stock</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100"><i class="bi bi-search me-1"></i> Filter</button>
        </div>
    </div>
</form>
<div class="data-card" style="overflow-x:auto;">
    <table class="table data-table" style="min-width:1000px;">
        <thead>
            <tr>
                <th>#</th><th>SKU / No.</th><th>Drug Name</th><th>Qty</th>
                <th>Expiry Date</th><th>Supplier</th><th>Stock Status</th><th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $item)
            @php
                $expiry      = \Carbon\Carbon::parse($item->expiry_date);
                $daysLeft    = now()->diffInDays($expiry, false);
                $expiryClass = $expiry->isPast() ? 'expiry-expired' : ($daysLeft <= 90 ? 'expiry-soon' : 'expiry-ok');
                $statusClass = match($item->status) { 'Stable'=>'bp-stable','Low Stock'=>'bp-low','Critical'=>'bp-critical','Out of Stock'=>'bp-out',default=>'bp-low' };
            @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($inventory->currentPage()-1) * $inventory->perPage() }}</span></td>
                <td><span class="code-pill">{{ $item->drug_num }}</span></td>
                <td><span class="row-title">{{ $item->drug_name }}</span></td>
                <td><span class="{{ $item->quantity <= 10 ? 'qty-critical' : 'qty-ok' }}">{{ $item->quantity }}</span></td>
                <td>
                    <span class="{{ $expiryClass }}">
                        {{ $expiry->format('M d, Y') }}
                        @if($expiry->isPast())<i class="bi bi-exclamation-triangle-fill ms-1"></i>
                        @elseif($daysLeft <= 90)<i class="bi bi-clock-fill ms-1"></i>@endif
                    </span>
                </td>
                <td><span class="supplier-text" title="{{ $item->supplier }}">{{ $item->supplier ?? '—' }}</span></td>
                <td><span class="badge-pill {{ $statusClass }}" style="white-space:nowrap;">{{ $item->status ?? 'N/A' }}</span></td>
                <td style="white-space: nowrap;">
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 6px;">
                        <button class="btn-act btn-act-edit" title="Edit"
                            data-bs-toggle="modal"
                            data-bs-target="#editInventoryModal"
                            data-id="{{ $item->id }}"
                            data-drug_num="{{ $item->drug_num }}"
                            data-drug_name="{{ $item->drug_name }}"
                            data-quantity="{{ $item->quantity }}"
                            data-status="{{ $item->status }}"
                            data-supplier="{{ $item->supplier }}"
                            data-expiry_date="{{ $item->expiry_date }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.logistics1.warehouse.inventory_delete', $item->id) }}" style="display:inline-flex; margin:0;" onsubmit="return confirm('Delete this inventory item?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-act btn-act-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                        @if(in_array($item->status, ['Low Stock', 'Critical', 'Out of Stock']))
                        <button class="btn-act-restock-text" title="Request Stock"
                            data-bs-toggle="modal"
                            data-bs-target="#requestStockModal"
                            data-drug_num="{{ $item->drug_num }}"
                            data-drug_name="{{ $item->drug_name }}"
                            data-supplier="{{ $item->supplier }}"
                            data-quantity="{{ $item->quantity }}">
                            <i class="bi bi-box-arrow-in-down"></i> Request Stock
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state"><i class="bi bi-clipboard-data"></i><p>No inventory records found.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $inventory->links() }}</div>

{{-- Request Stock Modal --}}
<div class="modal fade" id="requestStockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.warehouse.request_stock') }}">
                @csrf
                <input type="hidden" name="source" value="inventory_control">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-down me-2"></i>Request Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Drug No.</label>
                            <input type="text" name="drug_num" id="rs_drug_num" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drug Name</label>
                            <input type="text" name="drug_name" id="rs_drug_name" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="requested_quantity" id="rs_quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Requested Date</label>
                            <input type="date" name="requested_date" id="rs_requested_date" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date" id="rs_delivery_date" class="form-control" readonly style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Select Vendor <span class="text-danger">*</span></label>
                            <select name="selected_supplier" id="rs_vendor" class="form-select" required>
                                <option value="">— Choose a vendor —</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_name }}">{{ $vendor->vendor_name }}</option>
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
                    <button type="submit" class="btn-ms"><i class="bi bi-file-earmark-check me-1"></i> Create PO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('requestStockModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    document.getElementById('rs_drug_num').value  = btn.dataset.drug_num  || '';
    document.getElementById('rs_drug_name').value = btn.dataset.drug_name || '';
    document.getElementById('rs_quantity').value  = '';
    var today = new Date();
    var toDateStr = function(d) { return d.toISOString().split('T')[0]; };
    document.getElementById('rs_requested_date').value = toDateStr(today);
    var delivery = new Date(today);
    delivery.setMonth(delivery.getMonth() + 1);
    document.getElementById('rs_delivery_date').value = toDateStr(delivery);
    document.getElementById('rs_vendor').value = btn.dataset.supplier || '';
});

document.getElementById('editInventoryModal').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var form = document.getElementById('editInventoryForm');
    form.action = '{{ route("admin.logistics1.warehouse.inventory_update", ["id" => "__ID__"]) }}'.replace('__ID__', btn.dataset.id);
    document.getElementById('ei_drug_num').value    = btn.dataset.drug_num   || '';
    document.getElementById('ei_drug_name').value   = btn.dataset.drug_name  || '';
    document.getElementById('ei_quantity').value    = btn.dataset.quantity    || '';
    document.getElementById('ei_status').value      = btn.dataset.status      || '';
    document.getElementById('ei_supplier').value    = btn.dataset.supplier    || '';
    document.getElementById('ei_expiry_date').value = btn.dataset.expiry_date || '';
});
</script>

{{-- Edit Inventory Modal --}}
<div class="modal fade" id="editInventoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editInventoryForm">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Drug No.</label>
                            <input type="text" id="ei_drug_num" class="form-control" disabled style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Drug Name</label>
                            <input type="text" id="ei_drug_name" class="form-control" disabled style="background:#f8fafc;color:#64748b;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="ei_quantity" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="ei_status" class="form-select" required>
                                <option value="Stable">Stable</option>
                                <option value="Low Stock">Low Stock</option>
                                <option value="Critical">Critical</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" id="ei_expiry_date" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Supplier</label>
                            <input type="text" name="supplier" id="ei_supplier" class="form-control" maxlength="255">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-mu"><i class="bi bi-check-lg me-1"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


{{-- ===== TAB 3: DISPATCH & DISTRIBUTION ===== --}}
@if($activeTab === 'dispatch')
<p class="tab-desc">Items with Low Stock, Critical, or Out of Stock status that need dispatching or replenishment.</p>
<form action="{{ route('admin.logistics1.warehouse.index') }}" method="GET">
    <input type="hidden" name="tab" value="dispatch">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by drug name or SKU..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100"><i class="bi bi-search me-1"></i> Search</button>
        </div>
    </div>
</form>
<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th><th>SKU / No.</th><th>Drug Name</th><th>Qty</th>
                <th>Supplier</th><th>Expiry Date</th><th>Stock Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dispatch as $item)
            @php
                $expiry      = \Carbon\Carbon::parse($item->expiry_date);
                $daysLeft    = now()->diffInDays($expiry, false);
                $expiryClass = $expiry->isPast() ? 'expiry-expired' : ($daysLeft <= 90 ? 'expiry-soon' : 'expiry-ok');
                $statusClass = match($item->status) { 'Low Stock'=>'bp-low','Critical'=>'bp-critical','Out of Stock'=>'bp-out',default=>'bp-low' };
            @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($dispatch->currentPage()-1) * $dispatch->perPage() }}</span></td>
                <td><span class="code-pill">{{ $item->drug_num }}</span></td>
                <td><span class="row-title">{{ $item->drug_name }}</span></td>
                <td><span class="qty-critical">{{ $item->quantity }}</span></td>
                <td><span class="supplier-text" title="{{ $item->supplier }}">{{ $item->supplier ?? '—' }}</span></td>
                <td>
                    <span class="{{ $expiryClass }}">
                        {{ $expiry->format('M d, Y') }}
                        @if($expiry->isPast())<i class="bi bi-exclamation-triangle-fill ms-1"></i>
                        @elseif($daysLeft <= 90)<i class="bi bi-clock-fill ms-1"></i>@endif
                    </span>
                </td>
                <td><span class="badge-pill {{ $statusClass }}">{{ strtoupper($item->status ?? 'N/A') }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="empty-state"><i class="bi bi-box-arrow-up-right"></i><p>No items require dispatch or replenishment.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $dispatch->links() }}</div>
@endif

@endsection