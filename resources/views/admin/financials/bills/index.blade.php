@extends('admin.financials.layouts.app')

@section('title', 'Bills Collection')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Bills Collection</h2>
        <div>
            <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-download"></i> Export</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade show" role="alert">
            {{ session('success') }}
            </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Invoices</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted uppercase">
                        <tr>
                            <th class="ps-4">Bill Number</th>
                            <th>Patient / Encounter</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $bill->bill_number }}</td>
                           <td>
                           <div class="small fw-bold">
                                    {{ $bill->first_name ?? '' }} {{ $bill->last_name ?? '' }} (PID: {{ $bill->patient_id }})
                                </div>
                                <div class="text-muted small">EID: {{ $bill->encounter_id }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('M d, Y') }}</td>
                            <td class="fw-bold text-dark">${{ number_format($bill->total, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $bill->status == 'paid' ? 'bg-light text-success border border-success' : 'bg-light text-warning border border-warning' }}">
                                    {{ ucfirst($bill->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('financials.bills.show', $bill->id) }}" class="btn btn-outline-primary btn-sm">
                                        View
                                    </a>
                                    @if($bill->status != 'paid')
                                    <form action="{{ route('financials.bills.pay', $bill->id) }}" method="POST" class="d-inline ml-1">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="cash">
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Confirm payment?')">
                                            Pay
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No bills found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection