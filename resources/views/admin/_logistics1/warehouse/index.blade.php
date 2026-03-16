@extends('admin._logistics1.layouts.app') 

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Warehouse Inventory</h2>
        <form action="{{ route('admin.logistics1.warehouse.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search drug..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Drug Num</th>
                        <th>Drug Name</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Supplier</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventory as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td><span class="badge bg-secondary">{{ $item->drug_num }}</span></td>
                        <td><strong>{{ $item->drug_name }}</strong></td>
                        <td>
                            @if($item->quantity <= 0)
                                <span class="text-danger fw-bold">Out of Stock</span>
                            @else
                                {{ $item->quantity }}
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') }}</td>
                        <td><small>{{ $item->supplier }}</small></td>
                        <td>
                            @if($item->quantity > 20)
                                <span class="badge bg-success">Stable</span>
                            @elseif($item->quantity > 0)
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            @else
                                <span class="badge bg-danger">Critical</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $inventory->links() }}
        </div>
    </div>
</div>
@endsection