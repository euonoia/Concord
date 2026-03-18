@extends('admin.financials.layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Maintenance Ledger</h2>
        <p class="text-muted">All maintenance transactions for the fleet.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Vehicle Plate</th>
                            <th>Repair Type</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgerEntries as $entry)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $entry->vehicle_plate }}</td>
                            <td>{{ $entry->repair_type }}</td>
                            <td>₱{{ number_format($entry->amount, 2) }}</td>
                            <td>
                                @if($entry->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($entry->payment_status == 'unpaid')
                                    <span class="badge bg-danger">Unpaid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No maintenance ledger entries found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection