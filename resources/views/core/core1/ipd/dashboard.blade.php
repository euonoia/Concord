@extends('core.core1.layouts.app')

@section('title', 'IPD Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Inpatient Department (IPD)</h1>
            <p class="core1-subtitle">Manage admitted patients, wards, and bed assignments</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="core1-alert core1-alert-success">{{ session('success') }}</div>
    @endif

    {{-- Admitted Patients Card --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-hospital-fill"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Currently Admitted Patients</h2>
            </div>
            <span style="font-size: 11px; font-weight: 700; background: var(--info-light); color: var(--info); padding: 3px 10px; border-radius: 999px;">
                {{ $admissions->count() }} Admitted
            </span>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>MRN</th>
                        <th>Patient Name</th>
                        <th>Ward</th>
                        <th>Room</th>
                        <th>Bed</th>
                        <th>Admission Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admissions as $admission)
                        <tr>
                            <td class="font-mono text-sm" style="color: var(--primary);">{{ $admission->encounter->patient->mrn }}</td>
                            <td class="font-bold">{{ $admission->encounter->patient->first_name }} {{ $admission->encounter->patient->last_name }}</td>
                            <td>{{ $admission->bed->room->ward->name }}</td>
                            <td>Room {{ $admission->bed->room->room_number }} <span class="text-xs text-gray">({{ $admission->bed->room->room_type }})</span></td>
                            <td><span class="core1-badge-teal">Bed {{ $admission->bed->bed_number }}</span></td>
                            <td style="font-size: 12px; color: var(--text-gray);">
                                <i class="bi bi-calendar3" style="margin-right: 4px;"></i>
                                {{ $admission->admission_date->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('core1.patients.show', $admission->encounter->patient_id) }}" class="core1-btn-sm core1-btn-outline" title="View Patient">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button type="button" class="core1-btn-sm core1-btn-primary"
                                            onclick="openDischargeModal({{ $admission->id }}, '{{ $admission->encounter->patient->first_name }} {{ $admission->encounter->patient->last_name }}')"
                                            title="Discharge Patient">
                                        <i class="bi bi-box-arrow-right"></i> Discharge
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-40">
                                <i class="bi bi-bed" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No patients are currently admitted in the IPD.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Discharge Modal --}}
    <div id="dischargeModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:520px; max-width:92%; padding:0; border-radius:14px; overflow:hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 9px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-dark);">Patient Discharge</h3>
                    <p style="margin: 0; font-size: 12px; color: var(--text-gray);">Complete discharge for <span id="dischargePatientName" style="font-weight: 700; color: var(--text-dark);"></span></p>
                </div>
            </div>
            <form id="dischargeForm" method="POST">
                @csrf
                <div style="padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 6px;">Final Diagnosis <span style="color: var(--danger);">*</span></label>
                        <textarea name="final_diagnosis" rows="3" required placeholder="Enter final clinical diagnosis..."
                            style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); background: var(--bg); resize: vertical; font-family: inherit;"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 6px;">Discharge Summary <span style="color: var(--danger);">*</span></label>
                        <textarea name="discharge_summary" rows="4" required placeholder="Summary of treatment and follow-up instructions..."
                            style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); background: var(--bg); resize: vertical; font-family: inherit;"></textarea>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeDischargeModal()">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Confirm Discharge</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function openDischargeModal(admissionId, patientName) {
    document.getElementById('dischargePatientName').innerText = patientName;
    document.getElementById('dischargeForm').action = "/core/admissions/" + admissionId + "/discharge";
    document.getElementById('dischargeModal').style.display = 'flex';
}

function closeDischargeModal() {
    document.getElementById('dischargeModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dischargeModal').style.display = 'none';

    document.getElementById('dischargeModal').addEventListener('click', function(e) {
        if (e.target === this) closeDischargeModal();
    });
});
</script>
@endsection
