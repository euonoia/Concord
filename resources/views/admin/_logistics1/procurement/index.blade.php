@extends('admin._logistics1.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-truck me-2"></i>Procurement & Suppliers</h4>
</div>

{{-- ==================== TAB NAVIGATION ==================== --}}
<ul class="nav nav-tabs mb-4" id="procurementTabs">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'needs_assessment'  ? 'active' : '' }}"
           href="{{ route('admin.logistics1.procurement.index', ['tab' => 'needs_assessment']) }}">
            <i class="bi bi-clipboard-check me-1"></i> Needs Assessment
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'vendor_selection'  ? 'active' : '' }}"
           href="{{ route('admin.logistics1.procurement.index', ['tab' => 'vendor_selection']) }}">
            <i class="bi bi-shop me-1"></i> Vendor Selection
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'purchase_orders'   ? 'active' : '' }}"
           href="{{ route('admin.logistics1.procurement.index', ['tab' => 'purchase_orders']) }}">
            <i class="bi bi-receipt me-1"></i> Purchase Orders
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'payment_processing' ? 'active' : '' }}"
           href="{{ route('admin.logistics1.procurement.index', ['tab' => 'payment_processing']) }}">
            <i class="bi bi-credit-card me-1"></i> Payment Processing
        </a>
    </li>
</ul>


{{-- ==================== TAB 1: NEEDS ASSESSMENT ==================== --}}
@if($activeTab === 'needs_assessment')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Items currently at Low Stock, Critical, or Out of Stock status.</p>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRequestModal">
        <i class="bi bi-plus-lg me-1"></i> New Restock Request
    </button>
</div>

<form method="GET" action="{{ route('admin.logistics1.procurement.index') }}" class="row g-2 mb-3">
    <input type="hidden" name="tab" value="needs_assessment">
    <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search by drug name or number..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary btn-sm w-100">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Drug No.</th>
                <th>Drug Name</th>
                <th>Status</th>
                <th>Current Stock</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventory as $item)
            <tr>
                <td>{{ $loop->iteration + ($inventory->currentPage() - 1) * $inventory->perPage() }}</td>
                <td><code>{{ $item->drug_num }}</code></td>
                <td>{{ $item->drug_name }}</td>
                <td>
                    @php
                        $stockMap = [
                            'Low Stock'    => 'warning',
                            'Critical'     => 'danger',
                            'Out of Stock' => 'dark',
                        ];
                    @endphp
                    <span class="badge bg-{{ $stockMap[$item->status] ?? 'secondary' }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td>{{ $item->quantity ?? '0' }}</td>
                <td>
                    <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addRequestModal"
                        data-drug_num="{{ $item->drug_num }}"
                        data-drug_name="{{ $item->drug_name }}">
                        <i class="bi bi-plus-lg me-1"></i> Request Restock
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-check-circle fs-4 d-block mb-1"></i>
                    All inventory levels are sufficient.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end">{{ $inventory->withQueryString()->links() }}</div>

{{-- Add Restock Request Modal --}}
<div class="modal fade" id="addRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.procurement.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-clipboard-plus me-2"></i>New Restock Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drug No. <span class="text-danger">*</span></label>
                            <input type="text" name="drug_num" id="modal_drug_num" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drug Name <span class="text-danger">*</span></label>
                            <input type="text" name="drug_name" id="modal_drug_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Requested Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="requested_quantity" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Supplier <span class="text-danger">*</span></label>
                            <input type="text" name="selected_supplier" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('addRequestModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (btn && btn.dataset.drug_num) {
        document.getElementById('modal_drug_num').value  = btn.dataset.drug_num;
        document.getElementById('modal_drug_name').value = btn.dataset.drug_name;
    }
});
</script>
@endif


{{-- ==================== TAB 2: VENDOR SELECTION ====================  --}}
@if($activeTab === 'vendor_selection')

<p class="text-muted mb-4">Select a vendor to assign to a procurement request.</p>

@if($vendors->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-shop fs-2 d-block mb-2"></i>
        No suppliers found in inventory records.
    </div>
@else
<div class="row g-3">
    @foreach($vendors as $vendor)
    <div class="col-md-3 col-sm-4 col-6">
        <div class="card h-100 shadow-sm border-0 text-center vendor-card"
             style="cursor:pointer; transition: transform 0.15s, box-shadow 0.15s;"
             onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.12)'"
             onmouseleave="this.style.transform='none';this.style.boxShadow=''"
             onclick="selectVendor('{{ addslashes($vendor->supplier) }}')"
             title="Select {{ $vendor->supplier }}">
            <div class="card-body py-4 px-3">
                <div class="mb-2">
                    <i class="bi bi-building fs-3 text-primary"></i>
                </div>
                <p class="card-text fw-semibold mb-0" style="font-size:0.9rem; word-break:break-word;">
                    {{ $vendor->supplier }}
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Vendor Selected — Assign Modal --}}
<div class="modal fade" id="assignVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.procurement.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shop me-2"></i>Assign Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-shop-window fs-5"></i>
                        <span>Selected Vendor: <strong id="selectedVendorDisplay"></strong></span>
                    </div>
                    <input type="hidden" name="selected_supplier" id="selectedVendorInput">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drug No. <span class="text-danger">*</span></label>
                            <input type="text" name="drug_num" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Drug Name <span class="text-danger">*</span></label>
                            <input type="text" name="drug_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Requested Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="requested_quantity" class="form-control" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectVendor(name) {
    document.getElementById('selectedVendorDisplay').textContent = name;
    document.getElementById('selectedVendorInput').value = name;
    new bootstrap.Modal(document.getElementById('assignVendorModal')).show();
}
</script>
@endif

{{-- ==================== TAB 3: PURCHASE ORDERS ==================== --}}
@if($activeTab === 'purchase_orders')

<p class="text-muted mb-3">Orders that have been placed and are being processed or shipped.</p>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Drug No.</th>
                <th>Drug Name</th>
                <th>Supplier</th>
                <th>Qty Ordered</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $order)
            @php
                $poStatusMap = [
                    'approved' => 'info',
                    'ordered'  => 'primary',
                    'shipped'  => 'warning',
                ];
            @endphp
            <tr>
                <td>{{ $loop->iteration + ($purchaseOrders->currentPage() - 1) * $purchaseOrders->perPage() }}</td>
                <td><code>{{ $order->drug_num }}</code></td>
                <td>{{ $order->drug_name }}</td>
                <td>{{ $order->selected_supplier }}</td>
                <td>{{ $order->requested_quantity }}</td>
                <td>{{ $order->req_first_name }} {{ $order->req_last_name }}</td>
                <td>
                    <span class="badge bg-{{ $poStatusMap[$order->status] ?? 'secondary' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                <td>
                    @if($order->status === 'ordered')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="shipped">
                        <button class="btn btn-warning btn-sm text-dark">
                            <i class="bi bi-truck me-1"></i> Mark Shipped
                        </button>
                    </form>
                    @endif
                    @if($order->status === 'shipped')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $order->id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="delivered">
                        <button class="btn btn-success btn-sm">
                            <i class="bi bi-box-seam me-1"></i> Mark Delivered
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                    No active purchase orders.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end">{{ $purchaseOrders->withQueryString()->links() }}</div>
@endif


{{-- ==================== TAB 4: PAYMENT PROCESSING ==================== --}}
@if($activeTab === 'payment_processing')

<p class="text-muted mb-3">Delivered orders awaiting payment or already paid.</p>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Drug No.</th>
                <th>Drug Name</th>
                <th>Supplier</th>
                <th>Qty</th>
                <th>Requested By</th>
                <th>Delivered By</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                <td><code>{{ $payment->drug_num }}</code></td>
                <td>{{ $payment->drug_name }}</td>
                <td>{{ $payment->selected_supplier }}</td>
                <td>{{ $payment->requested_quantity }}</td>
                <td>{{ $payment->req_first_name }} {{ $payment->req_last_name }}</td>
                <td>
                    @if($payment->del_first_name)
                        {{ $payment->del_first_name }} {{ $payment->del_last_name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'warning text-dark' }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                <td>
                    @if($payment->status === 'delivered')
                    <form method="POST" action="{{ route('admin.logistics1.procurement.update_status', $payment->id) }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="status" value="paid">
                        <button class="btn btn-success btn-sm" onclick="return confirm('Mark this order as paid?')">
                            <i class="bi bi-check-circle me-1"></i> Mark Paid
                        </button>
                    </form>
                    @else
                        <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                    No payments to process.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end">{{ $payments->withQueryString()->links() }}</div>
@endif

@endsection