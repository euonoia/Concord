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
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Active Admissions Ready for Clearance</h2>
            </div>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Ward/Bed</th>
                        <th>Doctor</th>
                        <th>Admission Date</th>
                        <th>Billing Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissions as $admission)
                        @php
                            $encounter = $admission->encounter;
                            $patient = $encounter->patient;
                            $latestBill = $patient->bills()
                                ->where('encounter_id', $encounter->id)
                                ->latest()
                                ->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex items-center gap-2">
                                    <div class="core1-avatar" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($patient->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-blue">{{ $patient->name ?? 'N/A' }}</div>
                                        <div class="text-xxs text-gray font-mono">{{ $patient->mrn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($admission->bed && $admission->bed->room && $admission->bed->room->ward)
                                    <div class="text-sm font-medium">
                                        {{ $admission->bed->room->ward->name }}
                                    </div>
                                    <div class="text-xs text-gray">
                                        Room {{ $admission->bed->room->room_number }} - Bed {{ $admission->bed->bed_number }}
                                    </div>
                                @else
                                    <div class="text-sm font-medium text-gray">N/A</div>
                                    <div class="text-xs text-light">No Bed Assigned</div>
                                @endif
                            </td>
                            <td>{{ optional($encounter->doctor)->name ?? 'N/A' }}</td>
                            <td style="font-size: 12px; color: var(--text-gray);">
                                <i class="bi bi-calendar3" style="margin-right: 4px;"></i>
                                {{ $admission->admission_date->format('M d, Y') }}
                            </td>
                            <td>
                                @if($latestBill)
                                    <span class="core1-status-tag {{ $latestBill->status === 'paid' ? 'core1-tag-stable' : 'tag-red' }}">
                                        {{ strtoupper($latestBill->status) }}
                                    </span>
                                @else
                                    <span class="core1-status-tag tag-gray">NO BILL</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" 
                                        onclick="openDischargeModal('{{ $admission->id }}', '{{ $patient->name }}', '{{ $patient->mrn }}')"
                                        class="core1-btn-sm core1-btn-primary" 
                                        style="font-size: 11px;">
                                    <i class="bi bi-clipboard-check"></i> Process Clearance
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-40">
                                <i class="bi bi-inbox" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No active admissions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admissions->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $admissions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Discharge Clearance Modal --}}
<div id="dischargeModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:1100; align-items:center; justify-content:center; padding: 20px;">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 600px; padding:0; border-radius: 16px; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background: white;">
            <div class="d-flex items-center gap-3">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--success-light); color: var(--success); display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-size: 16px; font-weight:700;">Clinical Discharge Clearance</h3>
                    <p id="modalPatientName" style="margin:0; font-size: 13px; color: var(--text-gray);"></p>
                </div>
            </div>
            <button onclick="closeDischargeModal()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color: var(--text-gray);">&times;</button>
        </div>

        <form action="{{ route('core1.discharge.store') }}" method="POST" style="padding: 24px; margin:0;">
            @csrf
            <input type="hidden" name="admission_id" id="modalAdmissionId">
            
            <div class="core1-form-group mb-20">
                <label class="core1-form-label">Final Diagnosis <span class="text-red">*</span></label>
                <input type="text" name="final_diagnosis" class="core1-input" required placeholder="e.g. Community-Acquired Pneumonia">
            </div>

            <div class="core1-form-group mb-20">
                <label class="core1-form-label">Discharge Summary <span class="text-red">*</span></label>
                <textarea name="discharge_summary" class="core1-input" rows="4" required style="resize:none;" placeholder="Outline treatment course, medications, and follow-up instructions..."></textarea>
            </div>

            <div style="background: var(--bg-light); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 20px;">
                <div class="d-flex gap-3">
                    <i class="bi bi-info-circle" style="color: var(--primary);"></i>
                    <p style="margin:0; font-size: 12px; color: var(--text-dark); line-height: 1.5;">
                        <strong>Note:</strong> Discharging the patient will release the bed and automatically trigger the <strong>Billing Ledger</strong> to aggregate all final charges. The encounter will transition to <em>Pending Payment</em>.
                    </p>
                </div>
            </div>

            <div class="d-flex justify-end gap-3" style="padding-top: 10px;">
                <button type="button" onclick="closeDischargeModal()" class="core1-btn core1-btn-outline">Cancel</button>
                <button type="submit" class="core1-btn core1-btn-primary">
                    <i class="bi bi-check-circle"></i> Complete Discharge
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDischargeModal(id, name, mrn) {
        document.getElementById('modalAdmissionId').value = id;
        document.getElementById('modalPatientName').innerText = name + ' (' + mrn + ')';
        document.getElementById('dischargeModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDischargeModal() {
        document.getElementById('dischargeModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on overlay click
    document.getElementById('dischargeModal').addEventListener('click', function(e) {
        if (e.target === this) closeDischargeModal();
    });
</script>
@endsection