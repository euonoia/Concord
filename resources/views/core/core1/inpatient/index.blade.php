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

    <!--<div class="d-flex justify-end mt-15">
        @if(auth()->user()->role !== 'doctor')
            <a href="{{ route('core1.patients.create') }}" class="core1-btn core1-btn-primary">
                <i class="bi bi-plus"></i>
                <span class="ml-10">Admit Patient</span>
            </a>
        @endif
    </div>-->

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
                    <table class="core1-table" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Patient & ID</th>
                                <th style="width: 170px;">Location</th>
                                <th style="width: 200px;">Latest Vitals</th>
                                <th style="width: 240px;">Active Medications</th>
                                <th style="width: 180px;">Attending Staff</th>
                                <th style="width: 150px; text-align: center;">Status</th>
                                <th class="text-right" style="width: 150px; padding-right: 32px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inpatients as $admission)
                                @php
                                    $patient = $admission->encounter->patient;
                                    $triage = $admission->encounter->triage;
                                    $meds = $admission->encounter->prescriptions;
                                    $nurse = $patient->assignedNurse;
                                    $doctor = $admission->encounter->doctor;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="font-weight: 700; color: var(--text-dark); margin-bottom: 2px; font-size: 15px;">{{ $patient->name }}</div>
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-family: monospace; color: var(--primary); margin-bottom: 4px;">
                                            <i class="bi bi-person-vcard"></i> MRN: {{ $patient->mrn }}
                                        </div>
                                        @if($admission->encounter->chief_complaint)
                                            <div style="font-size: 11px; color: var(--text-dark); background: #fffbeb; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-bottom: 6px; border: 1px solid #fef3c7;">
                                                <i class="bi bi-chat-left-text-fill" style="color: var(--warning); margin-right: 4px;"></i> {{ Str::limit($admission->encounter->chief_complaint, 40) }}
                                            </div>
                                        @endif
                                        <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 4px;">
                                            <i class="bi bi-hash"></i> Adm #: {{ $admission->id }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="background: var(--bg-light); border-radius: 10px; padding: 10px 14px; border: 1px solid var(--border-color); display: inline-flex; flex-direction: column; gap: 4px; min-width: 140px;">
                                            <div style="font-weight: 700; font-size: 14px; color: var(--text-dark); display: flex; align-items: center; gap: 6px;">
                                                <i class="bi bi-hospital" style="color: var(--primary);"></i> {{ $admission->bed?->room?->ward?->name ?? 'N/A' }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 6px;">
                                                <i class="bi bi-door-closed" style="color: var(--info);"></i> Room {{ $admission->bed?->room?->room_number ?? 'N/A' }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 6px;">
                                                <i class="bi bi-bed" style="color: var(--success);"></i> Bed {{ $admission->bed?->bed_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        @if($triage)
                                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                                <div style="font-size: 11px; display: flex; flex-direction: column; align-items: center; background: #fff1f2; color: #be123c; padding: 6px 4px; border-radius: 8px; border: 1px solid #ffe4e6;" title="Blood Pressure">
                                                    <span style="font-size: 9px; text-transform: uppercase; font-weight: 800; opacity: 0.8; margin-bottom: 2px;">BP</span>
                                                    <span style="font-weight: 700; font-family: monospace;">{{ $triage->blood_pressure ?? '--' }}</span>
                                                </div>
                                                <div style="font-size: 11px; display: flex; flex-direction: column; align-items: center; background: #f0fdf4; color: #15803d; padding: 6px 4px; border-radius: 8px; border: 1px solid #dcfce7;" title="Heart Rate">
                                                    <span style="font-size: 9px; text-transform: uppercase; font-weight: 800; opacity: 0.8; margin-bottom: 2px;">HR</span>
                                                    <span style="font-weight: 700; font-family: monospace;">{{ $triage->heart_rate ?? '--' }}</span>
                                                </div>
                                                <div style="font-size: 11px; display: flex; flex-direction: column; align-items: center; background: #eff6ff; color: #1d4ed8; padding: 6px 4px; border-radius: 8px; border: 1px solid #dbeafe;" title="Temperature">
                                                    <span style="font-size: 9px; text-transform: uppercase; font-weight: 800; opacity: 0.8; margin-bottom: 2px;">Temp</span>
                                                    <span style="font-weight: 700; font-family: monospace;">{{ $triage->temperature ?? '--' }}°C</span>
                                                </div>
                                                <div style="font-size: 11px; display: flex; flex-direction: column; align-items: center; background: #fffbeb; color: #b45309; padding: 6px 4px; border-radius: 8px; border: 1px solid #fef3c7;" title="SpO2">
                                                    <span style="font-size: 9px; text-transform: uppercase; font-weight: 800; opacity: 0.8; margin-bottom: 2px;">SpO2</span>
                                                    <span style="font-weight: 700; font-family: monospace;">{{ $triage->spo2 ?? '--' }}%</span>
                                                </div>
                                            </div>
                                            <div style="font-size: 10px; color: var(--text-gray); margin-top: 8px; display: flex; align-items: center; gap: 4px; justify-content: center;">
                                                <i class="bi bi-clock-history"></i> {{ $triage->created_at->diffForHumans() }}
                                            </div>
                                        @else
                                            <div style="text-align: center; padding: 15px; background: var(--bg-light); border-radius: 8px; border: 1px dashed var(--border-color); color: var(--text-gray); font-size: 11px; font-style: italic;">
                                                Vitals not recorded
                                            </div>
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle;">
                                        @forelse($meds as $rx)
                                            <div style="margin-bottom: 8px; padding: 8px; background: white; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                                <div style="font-weight: 700; font-size: 12px; color: var(--text-dark); display: flex; align-items: center; gap: 6px;">
                                                    <i class="bi bi-capsule-pill" style="color: var(--primary);"></i> {{ $rx->medication }} 
                                                    <span style="font-weight: normal; font-size: 11px; color: var(--text-gray);">({{ $rx->dosage }})</span>
                                                </div>
                                                <div style="font-size: 11px; color: var(--text-light); margin-top: 4px; padding-left: 18px; line-height: 1.4;">
                                                    {{ $rx->instructions }}
                                                </div>
                                            </div>
                                        @empty
                                            <div style="color: var(--text-gray); font-size: 11px; font-style: italic; background: var(--bg-light); border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; text-align: center;">
                                                <i class="bi bi-prescription2 mb-4"></i><br>No active prescriptions
                                            </div>
                                        @endforelse
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <div style="display: flex; align-items: center; gap: 10px; background: var(--info-light); padding: 8px 12px; border-radius: 10px; border: 1px solid #dbeafe;">
                                                <div style="width: 30px; height: 30px; border-radius: 50%; background: white; color: var(--info); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; border: 2px solid #3b82f6; line-height: 1;">D</div>
                                                <div style="display: flex; flex-direction: column; justify-content: center;">
                                                    <span style="font-size: 10px; text-transform: uppercase; font-weight: 800; color: var(--info); line-height: 1.2; letter-spacing: 0.5px;">Doctor</span>
                                                    <span style="font-weight: 700; font-size: 14px; color: var(--text-dark); line-height: 1.2;">{{ $doctor?->name ?? 'Unassigned' }}</span>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 10px; background: var(--success-light); padding: 8px 12px; border-radius: 10px; border: 1px solid #d1fae5;">
                                                <div style="width: 30px; height: 30px; border-radius: 50%; background: white; color: var(--success); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; border: 2px solid #10b981; line-height: 1;">N</div>
                                                <div style="display: flex; flex-direction: column; justify-content: center;">
                                                    <span style="font-size: 10px; text-transform: uppercase; font-weight: 800; color: var(--success); line-height: 1.2; letter-spacing: 0.5px;">Nurse</span>
                                                    <span style="font-weight: 700; font-size: 14px; color: var(--text-dark); line-height: 1.2;">{{ $nurse?->name ?? 'Unassigned' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            <span class="core1-status-tag" style="background: var(--primary); color: white; border: none; font-size: 10px; font-weight: 800; padding: 4px 12px; border-radius: 6px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); letter-spacing: 0.5px;">
                                                {{ strtoupper($admission->status) }}
                                            </span>
                                            <div style="font-size: 12px; color: var(--text-dark); font-weight: 700; margin-top: 4px;">
                                                Adm: {{ $admission->admission_date->format('M d, Y') }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--text-gray); display: flex; align-items: center; gap: 4px;">
                                                <i class="bi bi-clock" style="font-size: 12px;"></i> {{ $admission->admission_date->format('h:i A') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle; padding-right: 32px;">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openRecordModal('{{ route('core1.medical-records.show', $patient->id) }}')" 
                                                    title="Clinical Overview"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            {{-- NEW CLINICAL ACTIONS --}}
                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openVitalsModal({{ $admission->encounter_id }}, '{{ $patient->name }}')" 
                                                    title="Record Vitals"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; color: var(--danger); border-color: rgba(220, 38, 38, 0.1);">
                                                <i class="bi bi-heart-pulse"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openNotesModal({{ $admission->encounter_id }}, '{{ $patient->name }}')" 
                                                    title="Clinical Notes"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openMedicationModal({{ $admission->encounter_id }})" 
                                                    title="Issue Medication"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-capsule"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openLabOrderModal({{ $admission->encounter_id }})" 
                                                    title="Order Lab Test"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-droplet-half"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openDischargeModal({{ $admission->id }}, '{{ $patient->name }}')" 
                                                    title="Discharge Patient"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; color: var(--danger); border-color: rgba(220, 38, 38, 0.2);">
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
                                                                        @if($bed['patient'])
                                                                            <br><span style="color: var(--primary); font-weight: 600;">{{ $bed['patient'] }}</span>
                                                                            <br><span style="font-size: 10px; color: var(--text-gray);">MRN: {{ $bed['mrn'] }}</span>
                                                                            
                                                                            @if($bed['triage'])
                                                                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed rgba(0,0,0,0.1); font-size: 11px;">
                                                                                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                                                                        <span>BP: <strong>{{ $bed['triage']['bp'] ?? '--' }}</strong></span>
                                                                                        <span>HR: <strong>{{ $bed['triage']['hr'] ?? '--' }}</strong></span>
                                                                                    </div>
                                                                                    <div style="display: flex; justify-content: space-between;">
                                                                                        <span>Temp: <strong>{{ $bed['triage']['temp'] ?? '--' }}°C</strong></span>
                                                                                        <span>SpO2: <strong>{{ $bed['triage']['spo2'] ?? '--' }}%</strong></span>
                                                                                    </div>
                                                                                </div>
                                                                            @endif

                                                                            <div style="margin-top: 12px; display: flex; gap: 6px; justify-content: center; background: rgba(255,255,255,0.5); padding: 6px; border-radius: 8px;">
                                                                                <button type="button" onclick="openVitalsModal({{ $bed['encounter_id'] }}, '{{ $bed['patient'] }}')" class="fm-tip-btn" style="color: var(--danger);" title="Record Vitals"><i class="bi bi-heart-pulse"></i></button>
                                                                                <button type="button" onclick="openNotesModal({{ $bed['encounter_id'] }}, '{{ $bed['patient'] }}')" class="fm-tip-btn" style="color: var(--info);" title="Clinical Notes"><i class="bi bi-pencil-square"></i></button>
                                                                                <button type="button" onclick="openMedicationModal({{ $bed['encounter_id'] }})" class="fm-tip-btn" style="color: var(--primary);" title="Issue Medication"><i class="bi bi-capsule"></i></button>
                                                                                <button type="button" onclick="openLabOrderModal({{ $bed['encounter_id'] }})" class="fm-tip-btn" style="color: var(--warning);" title="Order Lab"><i class="bi bi-droplet-half"></i></button>
                                                                            </div>
                                                                        @else
                                                                            <br><span style="color: var(--success);">{{ ucfirst($bed['status']) }}</span>
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

@include('core.core1.inpatient.modals.clinical_actions')

<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.core1-tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.core1-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

// Discharge Modal Logic
// Clinical Overview Modal Logic
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
    closeModal('medicalRecordModal');
}
</script>

{{-- Medical Record / Clinical Overview Modal --}}
<div id="medicalRecordModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:1100; align-items:center; justify-content:center; padding: 20px;" role="dialog" aria-modal="true">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 950px; padding:0; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid rgba(255,255,255,0.1);">

        {{-- Modal Header --}}
        <div style="padding: 20px 28px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: var(--text-dark); letter-spacing: -0.01em;">Clinical Overview</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--text-gray); display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-shield-check" style="color: var(--success);"></i> Secure Health Record
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeRecordModal()" style="background: var(--bg-hover); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-gray); transition: all 0.2s;">
                <i class="bi bi-x-lg" style="font-size: 14px;"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="flex: 1; overflow-y: auto; padding: 0; background: var(--bg);" id="modalContentWrapper">
            <div id="modalLoader" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding: 80px 0; background: #ffffff;">
                <div style="width: 48px; height: 48px; border: 3px solid var(--primary-light); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px;"></div>
                <h4 style="margin: 0 0 8px 0; font-weight: 600; color: var(--text-dark); font-size: 15px;">Retrieving Records</h4>
                <p style="font-size: 13px; color: var(--text-gray); margin: 0;">Accessing secure clinical database...</p>
            </div>
            <div id="modalContentInner" class="w-full" style="padding: 24px;"></div>
        </div>
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
