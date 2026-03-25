@extends('admin.financials.layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">Accounts Receivable (AR)</h2>
            <p class="text-muted">Validate pending invoices from Core 1 before moving to Collections.</p>
        </div>
        <div class="badge bg-info text-dark p-2">
            <i class="bi bi-clock-history"></i> Pending Validation: {{ $receivables->count() }}
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade show" role="alert">
            {{ session('success') }}
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
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivables as $item)
                        <tr class="align-middle">
                            <td class="ps-4 fw-bold text-secondary">{{ $item->bill_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2 bg-soft-primary text-primary rounded-circle text-center" style="width: 32px; height: 32px; line-height: 32px; background-color: #e7f1ff;">
                                        {{ substr($item->first_name, 0, 1) }}{{ substr($item->last_name, 0, 1) }}
                                    </div>
                                    <span>{{ $item->first_name }} {{ $item->last_name }}</span>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->bill_date)->format('M d, Y') }}</td>
                            <td>
                                @php
                                    $isOverdue = \Carbon\Carbon::parse($item->due_date)->isPast();
                                @endphp
                                <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($item->due_date)->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="fw-bold text-dark">
                                ₱{{ number_format($item->total, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">Pending Review</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    {{-- The Approval Action --}}
                                    <form action="{{ route('financials.apar.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Validate this invoice for collection?')">
                                            <i class="bi bi-check-circle me-1"></i> Approve
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No pending receivables found.
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
    .bg-soft-primary { background-color: #e7f1ff; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .table tbody td { font-size: 0.95rem; }
    .card { border-radius: 12px; overflow: hidden; }
</style>
@endsection