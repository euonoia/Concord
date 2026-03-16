@extends('admin._logistics1.layouts.app') 

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Procurement & Supplier Management</h2>
        <span class="badge bg-primary px-3 py-2">Logistics 1 Control</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($suppliers as $item)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">SKU: {{ $item->drug_num }}</div>
                    <h5 class="fw-bold text-gray-800 mb-2">{{ $item->drug_name }}</h5>
                    
                    <div class="mb-3">
                        @foreach(explode(',', $item->supplier) as $s)
                            <span class="badge rounded-pill bg-light text-dark border mr-1" style="font-size: 0.7rem;">{{ trim($s) }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-3">
                    <button class="btn btn-outline-primary btn-sm w-100 shadow-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#restockModal{{ $item->id }}">
                        <i class="bi bi-plus-circle me-1"></i> Request Restock
                    </button>
                </div>
            </div>
        </div>

        {{-- Restock Modal --}}
        <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.logistics1.procurement.store') }}" method="POST">
                    @csrf
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">New Procurement Request</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-2 mb-3 bg-light rounded text-center">
                                <h4 class="mb-0 text-dark">{{ $item->drug_name }}</h4>
                                <small class="text-muted">Part of Logistics 2 Inventory Sync</small>
                            </div>

                            {{-- CRITICAL: Passing these hidden ensures data consistency in L2 and Vehicle Reservation --}}
                            <input type="hidden" name="drug_num" value="{{ $item->drug_num }}">
                            <input type="hidden" name="drug_name" value="{{ $item->drug_name }}">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Supplier</label>
                                <select name="selected_supplier" class="form-select border-primary-subtle" required>
                                    <option value="" selected disabled>Choose a provider...</option>
                                    @foreach(explode(',', $item->supplier) as $s)
                                        <option value="{{ trim($s) }}">{{ trim($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Quantity to Request</label>
                                <div class="input-group">
                                    <input type="number" name="requested_quantity" class="form-control border-primary-subtle" min="1" placeholder="Enter amount" required>
                                    <span class="input-group-text">Units</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">Submit to Logistics 2</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- History Table --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Activity Feed</h6>
            <i class="bi bi-clock-history text-muted"></i>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date/Time</th>
                            <th>Item Requested</th>
                            <th>Target Supplier</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end pe-4">Current Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, g:i A') }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $log->drug_name }}</div>
                                <div class="text-xs text-muted">{{ $log->drug_num }}</div>
                            </td>
                            <td>{{ $log->selected_supplier }}</td>
                            <td class="text-center font-monospace">{{ $log->requested_quantity }}</td>
                            <td class="text-end pe-4">
                                @if($log->status == 'pending')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> PENDING L2</span>
                                @elseif($log->status == 'approved')
                                    <span class="badge bg-info"><i class="bi bi-check2-square me-1"></i> DISPATCHING</span>
                                @elseif($log->status == 'shipped')
                                    <span class="badge bg-primary"><i class="bi bi-truck me-1"></i> IN TRANSIT</span>
                                @elseif($log->status == 'received')
                                    <span class="badge bg-success"><i class="bi bi-house-check me-1"></i> COMPLETED</span>
                                @else
                                    <span class="badge bg-secondary">{{ strtoupper($log->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No procurement history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .text-xs { font-size: 0.75rem; }
</style>
@endsection