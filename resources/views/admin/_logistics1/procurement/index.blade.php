@extends('admin._logistics1.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="l1-inventory-wrapper">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="#">Logistics 1</a></li>
                    <li class="breadcrumb-item small active">Procurement</li>
                </ol>
            </nav>
            <h2 class="l1-procure-main-title">Restock Procurement</h2>
            <p class="text-muted mb-0">Items marked as Low Stock or Critical requiring replenishment.</p>
        </div>
        
        <div class="l1-search-container">
            <form action="{{ route('admin.logistics1.procurement.index') }}" method="GET">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                           placeholder="Search drug or SKU..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="l1-mini-card shadow-sm border-start border-danger border-4">
                <small class="text-muted d-block fw-bold">Items Needing Restock</small>
                <span class="h4 fw-bold text-danger">{{ $inventory->total() }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
    @endif

    <div class="card l1-procure-card border-0 shadow-sm">
        <div class="l1-procure-card-head d-flex justify-content-between align-items-center">
            <span><i class="bi bi-cart-plus me-2"></i> Pending Replenishment List</span>
        </div>
        
        <div class="table-responsive">
            <table class="table l1-procure-main-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Product Details</th>
                        <th class="text-center">Current Stock</th>
                        <th>Supplier Info</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    <tr>
                        <td class="ps-4">
                            <span class="l1-procure-sku-code">{{ $item->drug_num }}</span>
                            <span class="l1-procure-drug-label">{{ $item->drug_name }}</span>
                        </td>
                        <td class="text-center">
                            <div class="l1-procure-stock-pill shadow-sm">
                                <span class="l1-procure-qty-val {{ $item->quantity <= 10 ? 'text-danger' : '' }}">
                                    {{ number_format($item->quantity) }}
                                </span>
                                <span class="l1-procure-qty-unit">units</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="supplier-avatar me-2">{{ substr($item->supplier ?? 'S', 0, 1) }}</div>
                                <span class="text-muted small text-truncate" style="max-width: 150px;">
                                    {{ $item->supplier }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = match($item->status) {
                                    'Low Stock' => 'l1-status-low',
                                    'Critical' => 'l1-status-critical',
                                    'Out of Stock' => 'l1-status-out',
                                    default => 'l1-status-default'
                                };
                            @endphp
                            <span class="l1-status-badge {{ $statusClass }}">
                                {{ strtoupper($item->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" 
                                class="btn btn-dark btn-sm fw-bold px-3 restock-trigger shadow-sm"
                                data-sku="{{ $item->drug_num }}"
                                data-name="{{ $item->drug_name }}"
                                data-stock="{{ $item->quantity }}"
                                data-supplier="{{ $item->supplier }}">
                                <i class="bi bi-plus-lg me-1"></i> Restock
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-check2-circle text-success display-4"></i>
                            <p class="mt-2 text-muted">All stock levels are currently stable.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-4">
            <div class="d-flex justify-content-center">
                {{ $inventory->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- PROCUREMENT HISTORY --}}
<div class="card l1-procure-card border-0 shadow-sm mt-4">
    <div class="l1-procure-card-head d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i> Recent Requests History</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Item</th>
                    <th>Supplier</th>
                    <th class="text-center">Qty</th>
                    <th>Status</th>
                    <th>Requested By</th>
                    <th>Delivered By</th>
                    <th class="text-end pe-4">Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $log->drug_name }}</div>
                        <small class="text-muted">{{ $log->drug_num }}</small>
                    </td>

                    <td>
                        <small class="text-muted">{{ $log->selected_supplier }}</small>
                    </td>

                    <td class="text-center fw-bold">
                        {{ number_format($log->requested_quantity) }}
                    </td>

                    <td>
                        @php
                            $statusClass = match($log->status) {
                                'pending' => 'bg-warning text-dark',
                                'approved' => 'bg-success',
                                'received' => 'bg-info text-dark',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp

                        <span class="badge {{ $statusClass }}">
                            {{ strtoupper($log->status) }}
                        </span>
                    </td>

                    {{-- REQUESTED BY --}}
                    <td>
                        <small class="text-muted">
                            {{ $log->req_first_name ?? 'Unknown' }}
                            {{ $log->req_last_name ?? '' }}
                        </small>
                    </td>

                    {{-- DELIVERED BY --}}
                    <td>
                        <small class="text-muted">
                            {{ $log->del_first_name ?? '—' }}
                            {{ $log->del_last_name ?? '' }}
                        </small>
                    </td>

                    <td class="text-end pe-4">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}
                        </small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No procurement history yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center py-3">
        {{ $logs->links() }}
    </div>
</div>
<div class="modal fade" id="globalRestockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content l1-procure-modal-content">
            <div class="l1-procure-modal-accent"></div>
            <div class="l1-procure-modal-header d-flex align-items-center justify-content-between text-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="l1-procure-modal-icon">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Restock Request</h5>
                        <small class="opacity-75" id="modal-item-name-title">Item Name</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.logistics1.procurement.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="l1-procure-item-id-card mb-4">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <label class="l1-procure-label-sm">CURRENT STOCK</label>
                                <span class="fw-bold text-dark d-block" id="modal-stock-display">0</span>
                            </div>
                            <div class="col-6 ps-3">
                                <label class="l1-procure-label-sm">SUPPLIER</label>
                                <span class="fw-bold text-dark text-truncate d-block" id="modal-supplier-display">Name</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="drug_num" id="modal-input-sku">
                    <input type="hidden" name="drug_name" id="modal-input-name">
                    <input type="hidden" name="selected_supplier" id="modal-input-supplier">

                    <div class="mb-3">
                        <label class="l1-procure-input-label">Requested Quantity</label>
                        <div class="l1-procure-custom-input-group">
                            <input type="number" name="requested_quantity" class="form-control form-control-lg fw-bold" required min="1" placeholder="Enter Amount">
                        </div>
                        <small class="text-muted mt-2 d-block text-center italic">Approved requests update the main inventory automatically.</small>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="l1-procure-btn-confirm w-100 justify-content-center py-3">
                        Send to Logistics 2 <i class="bi bi-send-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('globalRestockModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.restock-trigger').forEach(btn => {
        btn.addEventListener('click', function () {
            // UI Labels
            document.getElementById('modal-item-name-title').innerText = this.dataset.name;
            document.getElementById('modal-stock-display').innerText = this.dataset.stock + ' Units';
            document.getElementById('modal-supplier-display').innerText = this.dataset.supplier;

            // Form Inputs
            document.getElementById('modal-input-sku').value = this.dataset.sku;
            document.getElementById('modal-input-name').value = this.dataset.name;
            document.getElementById('modal-input-supplier').value = this.dataset.supplier;

            modal.show();
        });
    });
});
</script>
@endsection