@extends('admin.financials.layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">Maintenance Payable</h2>
            <p class="text-muted">All unpaid maintenance costs for fleet vehicles.</p>
        </div>
        <div class="badge bg-info text-dark p-2">
            <i class="bi bi-clock-history"></i> Pending Payables: {{ $payables->count() }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Vehicle Plate</th>
                            <th>Repair</th>
                            <th class="text-end pe-4">Amount (₱)</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payables as $item)
                        <tr class="align-middle">
                            <td class="ps-4">{{ \Carbon\Carbon::parse($item->transaction_date)->format('M d, Y') }}</td>
                            <td>{{ $item->vehicle_plate }}</td>
                            <td>{{ $item->repair_type }}</td>
                            <td class="text-end fw-bold">₱{{ number_format($item->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ ucfirst($item->payment_status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                @if($item->payment_status === 'unpaid')
                                <form action="{{ route('financials.apar.maintenance-payable.pay', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Mark as Paid
                                    </button>
                                </form>
                                @else
                                <span class="text-success fw-bold">Paid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No unpaid maintenance entries found.
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