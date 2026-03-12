@extends('core.core1.layouts.app')

@section('title', 'Inpatient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@php
    $zoneConfig = [
        'ICU'  => ['icon' => 'bi-heart-pulse-fill', 'label' => 'Intensive Care Unit',   'cls' => 'core1-zone-icu'],
        'ER'   => ['icon' => 'bi-lightning-fill',    'label' => 'Emergency Room',         'cls' => 'core1-zone-er'],
        'WARD' => ['icon' => 'bi-hospital-fill',     'label' => 'General Ward',           'cls' => 'core1-zone-ward'],
        'OR'   => ['icon' => 'bi-scissors',          'label' => 'Operating Room',         'cls' => 'core1-zone-or'],
    ];
@endphp

<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Inpatient Management</h1>
            <p class="core1-subtitle">Manage admitted patients and bed allocation</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding:12px 16px; margin-bottom:20px; border-radius:8px; background:#d1fae5; color:#065f46; border-left:4px solid #10b981; display:flex; align-items:center; gap:10px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="padding:12px 16px; margin-bottom:20px; border-radius:8px; background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; display:flex; align-items:center; gap:10px;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Stats Section --}}
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-door-closed text-blue mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['current_inpatients'] }}</p>
                <p class="text-xs text-gray">Current Inpatients</p>
            </div>
        </div>
        
        {{-- Zone Occupancies --}}
        @foreach(['ICU' => 'bi-heart-pulse-fill', 'ER' => 'bi-lightning-fill', 'WARD' => 'bi-hospital-fill', 'OR' => 'bi-scissors'] as $zoneKey => $icon)
            <div class="core1-stat-card">
                <div class="d-flex flex-col">
                    <i class="bi {{ $icon }} text-red mb-10 core1-icon-stats"></i>
                    <p class="core1-title">{{ $floorMap[$zoneKey]['occ'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-gray);">/ {{ $floorMap[$zoneKey]['total'] }}</span></p>
                    <p class="text-xs text-gray">{{ $zoneKey }} Occupancy</p>
                </div>
            </div>
        @endforeach

        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-bed-front text-green mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['discharges_today'] }}</p>
                <p class="text-xs text-gray">Discharges Today</p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-end mt-15">
        @if(auth()->user()->role !== 'doctor')
            <a href="{{ route('core1.patients.create') }}" class="core1-btn core1-btn-primary">
                <i class="bi bi-plus"></i>
                <span class="ml-10">Admit Patient</span>
            </a>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="core1-card no-hover p-0 overflow-hidden mt-30">
        <div class="core1-tabs-header border-bottom">
            <button class="core1-tab-btn active" onclick="switchTab(event, 'inpatient-list')">
                <i class="bi bi-person-lines-fill mr-5"></i> Inpatient List
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'bed-allocation')">
                <i class="bi bi-grid-3x3-gap-fill mr-5"></i> 2D Floor Map
            </button>
        </div>

        <div class="tab-content p-25">

            {{-- ─── Inpatient List Tab ─────────────────────────────────────────────── --}}
            <div id="inpatient-list" class="core1-tab-pane active">
                <h3 class="mb-20 text-sm font-bold">Admitted Patients</h3>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>Inpatient ID</th>
                                <th>Patient</th>
                                <th>Bed</th>
                                <th>Admission Date</th>
                                <th>Doctor</th>
                                <th>Nurse</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inpatients as $admission)
                                <tr>
                                    <td>{{ $admission->id }}</td>
                                    <td>
                                        <div class="font-bold">{{ $admission->encounter->patient->name }}</div>
                                        <div class="text-xs text-gray-500">MRN: {{ $admission->encounter->patient->mrn }}</div>
                                    </td>
                                    <td>
                                        <div class="core1-badge-teal">
                                            {{ $admission->bed?->room?->ward?->name ?? 'N/A' }} - {{ $admission->bed?->bed_number ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">Room {{ $admission->bed?->room?->room_number ?? 'N/A' }} ({{ $admission->bed?->room?->room_type ?? 'N/A' }})</div>
                                    </td>
                                    <td>
                                        {{ $admission->admission_date->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $admission->admission_date->format('h:i A') }}</div>
                                    </td>
                                    <td>{{ $admission->encounter->doctor?->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="text-sm">
                                            {{ $admission->encounter->patient->assignedNurse?->name ?? 'Unassigned' }}
                                        </div>
                                    </td>
                                    <td>{{ $admission->encounter->chief_complaint ?? 'N/A' }}</td>
                                    <td>
                                        <span class="core1-status-tag core1-tag-occupied">
                                            {{ $admission->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="{{ route('core1.patients.show', $admission->encounter->patient_id) }}" class="core1-btn-sm core1-btn-outline" title="View Patient">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="core1-btn-sm core1-btn-primary"
                                                    onclick="openDischargeModal({{ $admission->id }}, '{{ $admission->encounter->patient->name }}')"
                                                    title="Discharge Patient">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center" style="padding: 40px; color: var(--text-light);">
                                        <i class="bi bi-bed" style="font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                                        No patients currently admitted.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ─── 2D Floor Map Tab ───────────────────────────────────────────────── --}}
            <div id="bed-allocation" class="core1-tab-pane">

                {{-- Top bar: title + legend + total summary --}}
                <div class="fm-topbar">
                    <div class="fm-topbar-left">
                        <i class="bi bi-building fm-topbar-icon"></i>
                        <div>
                            <div class="fm-topbar-title">Hospital Floor Plan</div>
                            <div class="fm-topbar-sub">Live bed occupancy across all clinical zones</div>
                        </div>
                    </div>
                    <div class="fm-legend">
                        <div class="fm-legend-item"><span class="fm-dot fm-dot-available"></span>Available</div>
                        <div class="fm-legend-item"><span class="fm-dot fm-dot-occupied"></span>Occupied</div>
                        <div class="fm-legend-item"><span class="fm-dot fm-dot-cleaning"></span>Cleaning</div>
                    </div>
                </div>

                {{-- Floor canvas with 4 zones --}}
                <div class="fm-canvas">
                    <div class="fm-grid">

                        @foreach(['ICU','ER','WARD','OR'] as $zoneKey)
                        @php
                            $zone = $floorMap[$zoneKey];
                            $cfg  = $zoneConfig[$zoneKey];
                            $pct  = $zone['total'] > 0 ? round($zone['occ'] / $zone['total'] * 100) : 0;
                            $hasContent = !empty($zone['wards']);
                        @endphp

                        <div class="fm-zone fm-zone-{{ strtolower($zoneKey) }}">

                            {{-- Zone Header --}}
                            <div class="fm-zone-hdr">
                                <div class="fm-zone-hdr-left">
                                    <div class="fm-zone-emblem">
                                        <i class="bi {{ $cfg['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <div class="fm-zone-name">{{ $zoneKey }}</div>
                                        <div class="fm-zone-full">{{ $cfg['label'] }}</div>
                                    </div>
                                </div>
                                <div class="fm-zone-pills">
                                    <div class="fm-pill fm-pill-occ">
                                        <span>{{ $zone['occ'] }}</span> Occupied
                                    </div>
                                    <div class="fm-pill fm-pill-free">
                                        <span>{{ $zone['avail'] }}</span> Free
                                    </div>
                                    <div class="fm-pill fm-pill-total">
                                        <span>{{ $zone['total'] }}</span> Total
                                    </div>
                                </div>
                            </div>

                            {{-- Occupancy progress bar --}}
                            <div class="fm-progress-wrap">
                                <div class="fm-progress-bar" style="width:{{ $pct }}%"></div>
                            </div>

                            {{-- Zone Body --}}
                            <div class="fm-zone-body">
                                @if(!$hasContent)
                                    <div class="fm-empty">
                                        <i class="bi bi-building-slash"></i>
                                        <span>No wards configured for {{ $zoneKey }}</span>
                                    </div>
                                @else
                                    @foreach($zone['wards'] as $ward)
                                        <div class="fm-ward">
                                            <div class="fm-ward-name">
                                                <i class="bi bi-signpost-2"></i> {{ $ward['name'] }}
                                            </div>
                                            <div class="fm-rooms">
                                                @foreach($ward['rooms'] as $room)
                                                    <div class="fm-room">
                                                        <div class="fm-room-hdr">
                                                            <span class="fm-room-num">{{ $room['room_number'] }}</span>
                                                            @if($room['room_type'])
                                                                <span class="fm-room-type">{{ $room['room_type'] }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="fm-beds">
                                                            @foreach($room['beds'] as $bed)
                                                                @php
                                                                    $bCls = match($bed['status']) {
                                                                        'occupied'  => 'fm-bed-occupied',
                                                                        'available' => 'fm-bed-available',
                                                                        'cleaning'  => 'fm-bed-cleaning',
                                                                        default     => 'fm-bed-unknown',
                                                                    };
                                                                @endphp
                                                                <div class="fm-bed-wrap">
                                                                    <div class="fm-bed {{ $bCls }}">
                                                                        <i class="bi bi-caret-up-fill fm-bed-head"></i>
                                                                        <div class="fm-bed-body"></div>
                                                                        @if($bed['status'] === 'occupied' && $bed['patient'])
                                                                            <div class="fm-bed-patient">
                                                                                <div class="fm-bed-patient-name">{{ $bed['patient'] }}</div>
                                                                                <div class="fm-bed-num" style="max-width: 100%;">{{ $bed['bed_number'] }}</div>
                                                                            </div>
                                                                        @else
                                                                            <div class="fm-bed-num">{{ $bed['bed_number'] }}</div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="fm-bed-tip">
                                                                        <strong>{{ $bed['bed_number'] }}</strong>
                                                                        @if($bed['status'] === 'occupied' && $bed['patient'])
                                                                            <br>{{ $bed['patient'] }}
                                                                            <br><span style="opacity:.7;font-size:10px;">MRN: {{ $bed['mrn'] }}</span>
                                                                        @else
                                                                            <br><span style="opacity:.8;">{{ ucfirst($bed['status']) }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endforeach

                    </div>{{-- /fm-grid --}}
                </div>{{-- /fm-canvas --}}

            </div>{{-- /bed-allocation --}}

        </div>{{-- /tab-content --}}
    </div>{{-- /tabs card --}}
</div>{{-- /core1-container --}}

{{-- Discharge Modal --}}
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

<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.core1-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.core1-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function openDischargeModal(admissionId, patientName) {
    document.getElementById('dischargePatientName').innerText = patientName;
    document.getElementById('dischargeForm').action = '/core1/admissions/' + admissionId + '/discharge';
    document.getElementById('dischargeModal').style.display = 'flex';
}

function closeDischargeModal() {
    document.getElementById('dischargeModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('dischargeModal').style.display = 'none';
    document.getElementById('dischargeModal').addEventListener('click', function (e) {
        if (e.target === this) closeDischargeModal();
    });
});
</script>
@endsection
