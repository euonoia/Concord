@extends('admin._logistics1.layouts.app') 

@section('content')
<div class="container-fluid">
    <h2>Procurement & Supplier Management</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mt-4">
        @foreach($suppliers as $item)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $item->drug_name }}</h5>
                    <p class="text-muted small">{{ $item->drug_num }}</p>
                    <div class="mb-3">
                        @foreach(explode(',', $item->supplier) as $s)
                            <span class="badge bg-info text-dark">{{ trim($s) }}</span>
                        @endforeach
                    </div>
                    <button class="btn btn-primary btn-sm w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#restockModal{{ $item->id }}">
                        Request Restock
                    </button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.logistics1.procurement.store') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Request Restock for {{ $item->drug_name }}</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="drug_num" value="{{ $item->drug_num }}">
                            <input type="hidden" name="drug_name" value="{{ $item->drug_name }}">
                            
                            <div class="mb-3">
                                <label>Select Supplier</label>
                                <select name="selected_supplier" class="form-select" required>
                                    @foreach(explode(',', $item->supplier) as $s)
                                        <option value="{{ trim($s) }}">{{ trim($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Quantity to Request</label>
                                <input type="number" name="requested_quantity" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Submit Log</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card mt-5">
        <div class="card-header bg-dark text-white">Recent Procurement Logs</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Drug</th>
                        <th>Supplier</th>
                        <th>Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td>{{ $log->drug_name }}</td>
                        <td>{{ $log->selected_supplier }}</td>
                        <td>{{ $log->requested_quantity }}</td>
                        <td><span class="badge bg-warning">{{ strtoupper($log->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection