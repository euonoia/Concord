@extends('core.core1.layouts.app')

@section('title', 'Billing & Payments')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Billing & Payments</h1>
            <p class="core1-subtitle">Manage patient invoices and payment records</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-receipt"></i>
            </div>
            <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">All Bills</h2>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Bill Number</th>
                        <th>Patient</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td class="font-mono text-sm" style="color: var(--primary);">{{ $bill->bill_number }}</td>
                            <td class="font-bold text-blue">{{ $bill->patient->name }}</td>
                            <td class="font-bold">₱{{ number_format($bill->total, 2) }}</td>
                            <td>
                                @php
                                    $statusClass = $bill->status === 'paid' ? 'core1-tag-stable' : ($bill->status === 'overdue' ? 'tag-red' : 'tag-pending');
                                @endphp
                                <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($bill->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('billing.show', $bill) }}" class="core1-btn-sm core1-btn-outline">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-40">
                                <i class="bi bi-receipt-cutoff" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No bills found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
