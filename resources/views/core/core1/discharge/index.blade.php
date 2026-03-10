@extends('core.core1.layouts.app')

@section('title', 'Discharge Management')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Discharge Management</h1>
            <p class="core1-subtitle">Review and process patient discharge clearances</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Patients Pending Discharge --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Patients Ready for Discharge</h2>
            </div>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Patient ID</th>
                        <th>Doctor</th>
                        <th>Assigned Nurse</th>
                        <th>Care Type</th>
                        <th>Appointment Date</th>
                        <th>Last Diagnosis</th>
                        <th>Billing Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        @php
                            $patient = $appointment->patient;
                            $latestRecord = $patient->medicalRecords()->latest('record_date')->first();
                            $latestBill = $patient->bills()->latest('bill_date')->first();
                        @endphp
                        <tr>
                            <td class="font-bold text-blue">{{ $patient->name ?? 'N/A' }}</td>
                            <td class="font-mono text-sm" style="color: var(--primary);">{{ $patient->patient_id ?? 'N/A' }}</td>
                            <td>{{ optional($appointment->doctor)->name ?? 'N/A' }}</td>
                            <td>{{ optional($patient->assignedNurse)->name ?? 'N/A' }}</td>
                            <td>{{ $patient->care_type ?? 'N/A' }}</td>
                            <td style="font-size: 12px; color: var(--text-gray);">
                                <i class="bi bi-calendar3" style="margin-right: 4px;"></i>
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                            </td>
                            <td>{{ $latestRecord->diagnosis ?? 'N/A' }}</td>
                            <td>
                                @if($latestBill)
                                    <span class="core1-status-tag {{ $latestBill->status === 'paid' ? 'core1-tag-stable' : 'tag-red' }}">
                                        {{ ucfirst($latestBill->status) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray">N/A</span>
                                @endif
                            </td>
                            <td>
                                <button class="core1-btn-sm core1-btn-primary" style="font-size: 11px;">
                                    <i class="bi bi-check-circle"></i> Mark Discharged
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-40">
                                <i class="bi bi-inbox" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No completed patients ready for discharge.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection