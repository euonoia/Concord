@extends('admin.financials.layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">Bills Ledger</h2>
            <p class="text-muted">All paid bills recorded in the General Ledger.</p>
        </div>
        <div class="badge bg-info text-dark p-2">
            <i class="bi bi-journal-text"></i> Total Entries: {{ $ledgerEntries->count() }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Bill #</th>
                            <th>Patient Name</th>
                            <th>Bill Date</th>
                            <th>Paid At</th>
                            <th>Total</th>
                            <th>Payment Method</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgerEntries as $entry)
                        <tr class="align-middle">
                            <td class="ps-4 fw-bold text-secondary">{{ $entry->bill_number }}</td>
                            <td>{{ $entry->patient_first_name }} {{ $entry->patient_last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->bill_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->paid_at)->format('M d, Y H:i') }}</td>
                            <td class="fw-bold text-dark">₱{{ number_format($entry->total, 2) }}</td>
                            <td class="text-capitalize">{{ $entry->payment_method }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('financials.bills-ledger.show', $entry->id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No ledger entries found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .table tbody td { font-size: 0.95rem; }
    .card { border-radius: 12px; overflow: hidden; }
</style>
@endsection