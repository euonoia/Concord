@extends('admin._logistics1.layouts.app') 

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">Warehouse Inventory</h2>
            <p class="text-muted small">Real-time stock levels for Logistics 1</p>
        </div>
        
        <form action="{{ route('admin.logistics1.warehouse.index') }}" method="GET" class="d-flex shadow-sm">
            <div class="input-group">
                <input type="text" name="search" class="form-control border-end-0" placeholder="Search drug name or SKU..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary px-3">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-uppercase text-muted small">
                            <th class="ps-4">ID</th>
                            <th>SKU/Num</th>
                            <th>Drug Name</th>
                            <th class="text-center">Current Qty</th>
                            <th>Expiry Date</th>
                            <th>Supplier</th>
                            <th class="text-end pe-4">Stock Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                        <tr>
                            <td class="ps-4 text-muted">{{ $item->id }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $item->drug_num }}</span></td>
                            <td><strong class="text-dark">{{ $item->drug_name }}</strong></td>
                            <td class="text-center">
                                <span class="fw-bold {{ $item->quantity <= 10 ? 'text-danger' : 'text-dark' }}">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="small">{{ \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 150px;" title="{{ $item->supplier }}">
                                    <small class="text-muted">{{ $item->supplier }}</small>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                {{-- Matching the badges to your SQL categories --}}
                                @php
                                    $badgeColor = match($item->status) {
                                        'Stable' => 'bg-success',
                                        'Low Stock' => 'bg-warning text-dark',
                                        'Critical' => 'bg-danger',
                                        'Out of Stock' => 'bg-dark',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} px-3 py-2" style="min-width: 90px;">
                                    {{ strtoupper($item->status ?? 'N/A') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No drugs found in inventory.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $inventory->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { font-weight: 600; letter-spacing: 0.5px; }
    .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.03); }
</style>
@endsection