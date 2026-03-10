@extends('core.core1.layouts.app')

@section('title', 'IPD Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Inpatient Department (IPD)</h2>
            <p class="core1-subtitle">Manage admitted patients, wards, and beds.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="core1-alert core1-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="core1-card">
        <h3 class="core1-title text-lg mb-4">Currently Admitted Patients</h3>
        <div style="overflow-x:auto;">
            <table class="w-full text-left" style="border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">MRN</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Patient Name</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Ward</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Room</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Bed</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Admission Date</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admissions as $admission)
                        <tr class="border-b" style="transition: background-color 0.2s; cursor:pointer;" onmouseover="this.style.backgroundColor='#f1f5f9';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="p-3 font-mono text-sm" style="color:#1a3a5a;">{{ $admission->encounter->patient->mrn }}</td>
                            <td class="p-3 font-bold">{{ $admission->encounter->patient->first_name }} {{ $admission->encounter->patient->last_name }}</td>
                            <td class="p-3">{{ $admission->bed->room->ward->name }}</td>
                            <td class="p-3">Room {{ $admission->bed->room->room_number }} <span class="text-xstext-gray-500">({{ $admission->bed->room->room_type }})</span></td>
                            <td class="p-3">Bed {{ $admission->bed->bed_number }}</td>
                            <td class="p-3">{{ $admission->admission_date->format('M d, Y h:i A') }}</td>
                            <td class="p-3">
                                <div class="core1-flex-gap-2">
                                    <a href="{{ route('core1.patients.show', $admission->encounter->patient_id) }}" class="core1-btn-sm core1-btn-outline" title="View Patient">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button type="button" class="core1-btn-sm core1-btn-primary" 
                                            onclick="openDischargeModal({{ $admission->id }}, '{{ $admission->encounter->patient->name }}')"
                                            title="Discharge Patient">
                                        <i class="bi bi-box-arrow-right"></i> Discharge
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 italic">No patients are currently admitted in the IPD.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    <div id="dischargeModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:500px; max-width:90%;">
            <div class="core1-header border-bottom mb-20 pb-10">
                <h3 class="core1-title">Patient Discharge</h3>
                <p class="core1-subtitle">Complete discharge for <span id="dischargePatientName" class="font-bold text-dark"></span></p>
            </div>
            <form id="dischargeForm" method="POST">
                @csrf
                <div class="mb-15">
                    <label class="font-bold block mb-5">Final Diagnosis</label>
                    <textarea name="final_diagnosis" class="w-full p-10 border rounded" rows="3" required placeholder="Enter final clinical diagnosis..."></textarea>
                </div>
                <div class="mb-20">
                    <label class="font-bold block mb-5">Discharge Summary</label>
                    <textarea name="discharge_summary" class="w-full p-10 border rounded" rows="4" required placeholder="Enter brief summary of treatment and follow-up instructions..."></textarea>
                </div>
                <div class="core1-flex-gap-2 justify-end pt-10 border-top">
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
    document.getElementById('dischargeForm').action = "/core1/admissions/" + admissionId + "/discharge";
    document.getElementById('dischargeModal').style.display = 'flex';
}

function closeDischargeModal() {
    document.getElementById('dischargeModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dischargeModal').style.display = 'none';
});
</script>
@endsection
