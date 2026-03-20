@extends('core.core1.layouts.app')

@section('title', 'Discharge Logs')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <div class="d-flex items-center gap-2 mb-1">
                <a href="{{ route('core1.discharge.index') }}" class="text-gray hover-blue" style="font-size: 13px; text-decoration: none;">
                    <i class="bi bi-arrow-left"></i> Back to Management
                </a>
            </div>
            <h1 class="core1-title">Discharge Logs</h1>
            <p class="core1-subtitle">Historical records of fully discharged patients</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-calendar-event" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Statistics Summary --}}
    <div class="core1-stats-grid mb-25" style="grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div class="core1-card {{ $period === 'today' ? 'border-primary' : '' }}" style="padding: 20px; border-radius: 12px; position: relative; overflow: hidden;{{ $period === 'today' ? 'border: 1px solid var(--primary); background: rgba(var(--primary-rgb), 0.05);' : '' }}">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <p style="font-size: 11px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px;">Today</p>
                    <h3 style="font-size: 24px; font-weight: 800; margin: 0; color: var(--text-dark);">{{ $stats['today'] }}</h3>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>
            <a href="{{ route('core1.discharge.logs', ['period' => 'today']) }}" style="position: absolute; inset: 0; z-index: 1;"></a>
        </div>

        <div class="core1-card {{ $period === 'week' ? 'border-primary' : '' }}" style="padding: 20px; border-radius: 12px; position: relative; overflow: hidden;{{ $period === 'week' ? 'border: 1px solid var(--primary); background: rgba(var(--primary-rgb), 0.05);' : '' }}">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <p style="font-size: 11px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px;">This Week</p>
                    <h3 style="font-size: 24px; font-weight: 800; margin: 0; color: var(--text-dark);">{{ $stats['week'] }}</h3>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
            <a href="{{ route('core1.discharge.logs', ['period' => 'week']) }}" style="position: absolute; inset: 0; z-index: 1;"></a>
        </div>

        <div class="core1-card {{ $period === 'month' ? 'border-primary' : '' }}" style="padding: 20px; border-radius: 12px; position: relative; overflow: hidden;{{ $period === 'month' ? 'border: 1px solid var(--primary); background: rgba(var(--primary-rgb), 0.05);' : '' }}">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <p style="font-size: 11px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 5px;">This Month</p>
                    <h3 style="font-size: 24px; font-weight: 800; margin: 0; color: var(--text-dark);">{{ $stats['month'] }}</h3>
                </div>
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--bg-dark); color: var(--text-gray); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <a href="{{ route('core1.discharge.logs', ['period' => 'month']) }}" style="position: absolute; inset: 0; z-index: 1;"></a>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px;">
        <div class="core1-card-header" style="padding: 18px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--bg-light); color: var(--text-dark); width:36px; height:36px; border-radius:8px; font-size:1.1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Discharge History: {{ ucfirst($period) }}</h2>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('core1.discharge.logs', ['period' => 'today']) }}" class="core1-btn-sm {{ $period === 'today' ? 'core1-btn-primary' : 'core1-btn-outline' }}">Today</a>
                <a href="{{ route('core1.discharge.logs', ['period' => 'week']) }}" class="core1-btn-sm {{ $period === 'week' ? 'core1-btn-primary' : 'core1-btn-outline' }}">Week</a>
                <a href="{{ route('core1.discharge.logs', ['period' => 'month']) }}" class="core1-btn-sm {{ $period === 'month' ? 'core1-btn-primary' : 'core1-btn-outline' }}">Month</a>
            </div>
        </div>

        <div class="core1-table-container shadow-none">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Stay/Location</th>
                        <th>Discharge Date</th>
                        <th>Physician</th>
                        <th>Diagnosis</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($encounters as $encounter)
                        @php $patient = $encounter->patient; @endphp
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
                                <span class="core1-status-tag {{ $encounter->type === 'IPD' ? 'core1-tag-critical' : 'core1-tag-stable' }}" style="width: fit-content; font-size: 10px; padding: 1px 6px;">
                                    {{ $encounter->type }}
                                </span>
                            </td>
                            <td>
                                @if($encounter->type === 'IPD' && $encounter->admission)
                                    <div style="font-size: 12px; font-weight: 500;">
                                        {{ $encounter->admission->bed->room->ward->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xxs text-gray">Bed {{ $encounter->admission->bed->bed_number ?? 'N/A' }}</div>
                                @else
                                    <span style="font-size: 12px; color: var(--text-gray);">Outpatient Vault</span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">
                                <div class="font-bold">{{ $encounter->updated_at->format('M d, Y') }}</div>
                                <div class="text-xxs text-gray">{{ $encounter->updated_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div style="font-size: 12px; font-weight: 600;">Dr. {{ $encounter->discharge->clearingDoctor->name ?? $encounter->doctor->name ?? 'N/A' }}</div>
                                <div class="text-xxs text-gray">Attending</div>
                            </td>
                            <td>
                                <div style="font-size: 11px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $encounter->discharge->final_diagnosis ?? 'No Diagnosis' }}">
                                    {{ $encounter->discharge->final_diagnosis ?? 'No Diagnosis' }}
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" 
                                        onclick="viewDischargeDetails({{ json_encode([
                                            'name' => $patient->name,
                                            'mrn' => $patient->mrn,
                                            'type' => $encounter->type,
                                            'diagnosis' => $encounter->discharge->final_diagnosis ?? 'N/A',
                                            'summary' => $encounter->discharge->discharge_summary ?? 'No summary provided.',
                                            'instructions' => $encounter->discharge->follow_up_instructions ?? 'No specific instructions.',
                                            'date' => $encounter->updated_at->format('M d, Y h:i A')
                                        ]) }})"
                                        class="core1-btn-sm core1-btn-outline" 
                                        style="font-size: 10px;">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-40">
                                <i class="bi bi-journal-x" style="font-size: 2rem; color: var(--text-light); display: block; margin-bottom: 8px;"></i>
                                No discharge records found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div id="detailsModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:1100; align-items:center; justify-content:center; padding: 20px;">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 550px; padding:0; border-radius: 16px; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; background: white;">
            <div class="d-flex items-center gap-3">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--primary-light); color: var(--primary); display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                    <i class="bi bi-file-earmark-medical"></i>
                </div>
                <div>
                    <h3 style="margin:0; font-size: 16px; font-weight:700;">Discharge Summary</h3>
                    <p id="detailPatientInfo" style="margin:0; font-size: 12px; color: var(--text-gray);"></p>
                </div>
            </div>
            <button onclick="closeDetailsModal()" style="background:transparent; border:none; font-size:1.5rem; cursor:pointer; color: var(--text-gray);">&times;</button>
        </div>

        <div style="padding: 24px; background: white;">
            <div class="mb-20">
                <label style="display: block; font-size: 10px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;">Final Diagnosis</label>
                <div id="detailDiagnosis" style="font-size: 14px; font-weight: 700; color: var(--text-dark); padding: 10px; background: var(--bg-light); border-radius: 8px; border-left: 4px solid var(--primary);"></div>
            </div>

            <div class="mb-20">
                <label style="display: block; font-size: 10px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;">Clinical Summary</label>
                <div id="detailSummary" style="font-size: 13px; line-height: 1.6; color: var(--text-dark); max-height: 150px; overflow-y: auto;"></div>
            </div>

            <div style="background: var(--bg-light); padding: 15px; border-radius: 12px; border: 1px dashed var(--border-color);">
                <label style="display: block; font-size: 10px; font-weight: 800; color: var(--text-gray); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 8px;">
                    <i class="bi bi-info-circle" style="color: var(--primary);"></i> Follow-up Instructions
                </label>
                <p id="detailInstructions" style="margin:0; font-size: 12px; color: var(--text-dark); font-style: italic;"></p>
            </div>
        </div>

        <div style="padding: 16px 24px; background: var(--bg-light); border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 11px; color: var(--text-gray);">
                Released on: <span id="detailDate" class="font-bold"></span>
            </div>
            <button onclick="closeDetailsModal()" class="core1-btn-sm core1-btn-primary">Close</button>
        </div>
    </div>
</div>

<script>
    function viewDischargeDetails(data) {
        document.getElementById('detailPatientInfo').innerText = data.name + ' (' + data.mrn + ') • ' + data.type;
        document.getElementById('detailDiagnosis').innerText = data.diagnosis;
        document.getElementById('detailSummary').innerText = data.summary;
        document.getElementById('detailInstructions').innerText = data.instructions;
        document.getElementById('detailDate').innerText = data.date;
        
        document.getElementById('detailsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on overlay click
    document.getElementById('detailsModal').addEventListener('click', function(e) {
        if (e.target === this) closeDetailsModal();
    });
</script>

<style>
    .border-primary {
        border: 1px solid var(--primary) !important;
        background: rgba(var(--primary-rgb), 0.05) !important;
    }
    .hover-blue:hover {
        color: var(--primary) !important;
    }
    .text-xxs {
        font-size: 9px;
    }
</style>
@endsection
