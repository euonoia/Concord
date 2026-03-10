@extends('core.core1.layouts.app')

@section('title', 'Medical Records')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Medical Records</h1>
            <p class="core1-subtitle">View and manage patient health records</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
            <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-file-earmark-medical"></i>
            </div>
            <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Patient Health Records</h2>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Record Type</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $patient)
                        @php
                            $latestRecord = $patient->medicalRecords->first();
                            $latestAppointment = $patient->appointments->first();
                        @endphp
                        <tr>
                            <td class="font-bold text-blue">{{ $patient->name }}</td>
                            <td>
                                @if($latestRecord)
                                    {{ $latestRecord->record_type }}
                                @elseif($latestAppointment)
                                    {{ ucfirst($latestAppointment->type ?? 'N/A') }}
                                @else
                                    <span class="text-xs text-gray">N/A</span>
                                @endif
                            </td>
                            <td style="font-size: 12px; color: var(--text-gray);">
                                @if($latestRecord)
                                    <i class="bi bi-calendar3" style="margin-right: 4px;"></i>
                                    {{ $latestRecord->record_date->format('M d, Y') }}
                                @elseif($latestAppointment)
                                    <i class="bi bi-calendar3" style="margin-right: 4px;"></i>
                                    {{ \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <button type="button" onclick="openRecordModal('{{ route('core1.medical-records.show', $patient->id) }}')"
                                    class="core1-btn-sm core1-btn-outline" title="View Record Details">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-40">
                                <i class="bi bi-file-earmark-x" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Medical Record Modal --}}
<div id="medicalRecordModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1060; align-items:flex-start; justify-content:center; padding-top: 40px;" role="dialog" aria-modal="true">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 900px; padding:0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh;">

        {{-- Modal Header --}}
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 9px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                    <i class="bi bi-file-earmark-medical-fill"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-dark);">Medical Record Details</h3>
                    <p style="margin: 0; font-size: 12px; color: var(--text-gray);">Full patient health record</p>
                </div>
            </div>
            <button type="button" onclick="closeRecordModal()" style="background: var(--bg); border: 1px solid var(--border-color); border-radius: 7px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-gray);">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="flex: 1; overflow-y: auto; padding: 24px;" id="modalContentWrapper">
            <div id="modalLoader" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding: 40px 0;">
                <i class="bi bi-arrow-clockwise" style="font-size: 2rem; color: var(--primary); animation: spin 1s linear infinite; display: block; margin-bottom: 10px;"></i>
                <p style="font-size: 13px; color: var(--text-gray); margin: 0;">Loading record details...</p>
            </div>
            <div id="modalContentInner" class="w-full"></div>
        </div>
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
    function openRecordModal(url) {
        const modal = document.getElementById('medicalRecordModal');
        const loader = document.getElementById('modalLoader');
        const content = document.getElementById('modalContentInner');

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        loader.style.display = 'flex';
        content.innerHTML = '';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const titles = doc.querySelectorAll('h1');
            titles.forEach(h => {
                if (h.innerText.includes('Medical Record Details')) {
                    const row = h.closest('.flex.justify-between');
                    if(row) row.remove();
                }
            });
            content.innerHTML = doc.body.innerHTML;
            loader.style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching record:', error);
            content.innerHTML = `<div style="padding:40px;text-align:center;color:var(--danger);font-weight:700;"><i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Failed to load record details. Please try again.</div>`;
            loader.style.display = 'none';
        });
    }

    function closeRecordModal() {
        document.getElementById('medicalRecordModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('medicalRecordModal').addEventListener('click', function(e) {
            if (e.target === this) closeRecordModal();
        });
    });
</script>
@endsection