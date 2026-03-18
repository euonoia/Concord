@extends('core.core1.layouts.app')

@section('title', 'Inpatient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.core1-med-popover {
    display: none;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    z-index: 2000;
    background: white;
    padding: 16px;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .core1-med-popover {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 400px;
        right: auto;
        max-height: 80vh;
        overflow-y: auto;
        border: 2px solid var(--primary);
        box-shadow: 0 0 0 100vmax rgba(0,0,0,0.4);
    }
}
</style>

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
                <div class="core1-table-container shadow-none border" style="min-height: 600px;">
                    <table class="core1-table" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 200px;">Patient & ID</th>
                                <th style="width: 170px;">Location</th>
                                <th style="width: 200px;">Latest Vitals</th>
                                <th style="width: 240px;">Active Medications</th>
                                <th style="width: 180px;">Attending Staff</th>
                                <th style="width: 150px; text-align: center;">Status</th>
                                <th class="text-center" style="width: 170px;">Actions</th>
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
                                            <div style="font-size: 10px; color: var(--text-gray); margin-top: 8px; display: flex; flex-direction: column; align-items: center; gap: 6px; justify-content: center;">
                                                <div style="display: flex; align-items: center; gap: 4px;">
                                                    <i class="bi bi-clock-history"></i> {{ $triage->created_at->diffForHumans() }}
                                                </div>
                                                <button type="button" 
                                                        onclick="openVitalsHistoryModal({{ $admission->encounter_id }}, '{{ $patient->name }}', '{{ json_encode($admission->encounter->triages) }}')"
                                                        style="background: none; border: none; color: var(--primary); font-size: 10px; font-weight: 700; cursor: pointer; text-decoration: underline; padding: 0;">
                                                    View History
                                                </button>
                                            </div>
                                        @else
                                            <div style="text-align: center; padding: 15px; background: var(--bg-light); border-radius: 8px; border: 1px dashed var(--border-color); color: var(--text-gray); font-size: 11px; font-style: italic;">
                                                Vitals not recorded
                                            </div>
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle; position: relative;">
                                        @php $firstMed = $meds->first(); @endphp
                                        @if($firstMed)
                                            <div style="margin-bottom: 4px; padding: 8px; background: white; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                                <div style="font-weight: 700; font-size: 12px; color: var(--text-dark); display: flex; align-items: center; gap: 6px;">
                                                    <i class="bi bi-capsule-pill" style="color: var(--primary);"></i> {{ $firstMed->medication }} 
                                                    <span style="font-weight: 800; color: var(--primary);">[x{{ $firstMed->quantity }}]</span>
                                                    <span style="font-weight: normal; font-size: 11px; color: var(--text-gray);">({{ $firstMed->dosage }})</span>
                                                </div>
                                                <div style="font-size: 11px; color: var(--text-light); margin-top: 4px; padding-left: 18px; line-height: 1.4;">
                                                    {{ $firstMed->instructions }}
                                                </div>
                                            </div>

                                            @if($meds->count() > 1)
                                                <div id="extra-meds-{{ $admission->encounter_id }}" class="core1-med-popover">
                                                    <div style="font-weight: 800; font-size: 10px; color: var(--text-gray); text-transform: uppercase; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                                                        <span>All Medications</span>
                                                        <span style="background: var(--primary); color: white; padding: 2px 6px; border-radius: 4px;">{{ $meds->count() }}</span>
                                                    </div>
                                                    @foreach($meds as $rx)
                                                        <div style="margin-bottom: 8px; padding: 10px; background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 10px;">
                                                            <div style="font-weight: 700; font-size: 13px; color: var(--text-dark); display: flex; align-items: center; justify-content: space-between; gap: 6px;">
                                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                                    <i class="bi bi-capsule-pill" style="color: var(--primary); font-size: 14px;"></i> {{ $rx->medication }} 
                                                                    <span style="font-weight: 800; color: var(--primary);">[x{{ $rx->quantity }}]</span>
                                                                    <span style="font-weight: normal; font-size: 11px; color: var(--text-gray);">({{ $rx->dosage }})</span>
                                                                </div>
                                                                @if($rx->status === 'Administered')
                                                                    <i class="bi bi-check-circle-fill" style="color: var(--success); font-size: 14px;" title="Administered"></i>
                                                                @endif
                                                            </div>
                                                            <div style="font-size: 11px; color: var(--text-light); margin-top: 6px; padding-left: 22px; line-height: 1.5; border-top: 1px solid rgba(0,0,0,0.03); padding-top: 4px;">
                                                                {{ $rx->instructions }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" 
                                                            onclick="toggleMeds({{ $admission->encounter_id }})" 
                                                            style="width: 100%; background: var(--primary); border: none; color: white; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 10px; cursor: pointer; margin-top: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                        Close Medications
                                                    </button>
                                                </div>
                                                <button type="button" 
                                                        onclick="toggleMeds({{ $admission->encounter_id }})" 
                                                        id="btn-toggle-meds-{{ $admission->encounter_id }}"
                                                        style="background: none; border: none; color: var(--info); font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 4px 0;">
                                                    <i class="bi bi-plus-circle"></i> Show all ({{ $meds->count() }})
                                                </button>
                                            @endif
                                        @else
                                            <div style="color: var(--text-gray); font-size: 11px; font-style: italic; background: var(--bg-light); border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; text-align: center;">
                                                <i class="bi bi-prescription2 mb-4"></i><br>No active prescriptions
                                            </div>
                                        @endif
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
                                    <td style="vertical-align: middle;">
                                        <div style="display: grid; grid-template-columns: repeat(4, 32px); gap: 6px; justify-content: center;">
                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openRecordModal('{{ route('core1.medical-records.show', $patient->id) }}')" 
                                                    title="Clinical Overview"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0;">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            {{-- NEW CLINICAL ACTIONS --}}
                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openVitalsModal({{ $admission->encounter_id }}, '{{ $patient->name }}')" 
                                                    title="Record Vitals"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; color: var(--danger); border-color: rgba(220, 38, 38, 0.1); flex-shrink: 0;">
                                                <i class="bi bi-heart-pulse"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openNotesModal({{ $admission->encounter_id }}, '{{ $patient->name }}')" 
                                                    title="Clinical Notes"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0;">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openMedicationModal({{ $admission->encounter_id }})" 
                                                    title="Issue Medication"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0;">
                                                <i class="bi bi-capsule"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openLabOrderModal({{ $admission->encounter_id }})" 
                                                    title="Order Lab Test"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0;">
                                                <i class="bi bi-droplet-half"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openAdministrationModal({{ $admission->encounter_id }}, '{{ $patient->name }}')" 
                                                    title="Medication Administration"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; color: var(--success); border-color: rgba(16, 185, 129, 0.1); flex-shrink: 0;">
                                                <i class="bi bi-clipboard2-check"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openSurgeryOrderModal({{ $admission->encounter_id }})" 
                                                    title="Order Surgery"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0; color: var(--primary);">
                                                <i class="bi bi-scissors"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openDietOrderModal({{ $admission->encounter_id }})" 
                                                    title="Set Diet"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; flex-shrink: 0; color: var(--warning);">
                                                <i class="bi bi-egg-fried"></i>
                                            </button>

                                            <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                    onclick="openDischargeModal({{ $admission->id }}, '{{ $patient->name }}')" 
                                                    title="Discharge Patient"
                                                    style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; color: var(--danger); border-color: rgba(220, 38, 38, 0.2); flex-shrink: 0;">
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
                                                                                <button type="button" onclick="openVitalsModal({{ $bed['encounter_id'] }}, '{{ $bed['patient'] }}')" class="fm-tip-btn" style="color: var(--danger); flex-shrink: 0;" title="Record Vitals"><i class="bi bi-heart-pulse"></i></button>
                                                                                @if($bed['triage'])
                                                                                    <button type="button" onclick="openVitalsHistoryModal({{ $bed['encounter_id'] }}, '{{ $bed['patient'] }}', '{{ json_encode($bed['triage']['history'] ?? []) }}')" class="fm-tip-btn" style="color: var(--primary); flex-shrink: 0;" title="Vitals History"><i class="bi bi-clock-history"></i></button>
                                                                                @endif
                                                                                <button type="button" onclick="openNotesModal({{ $bed['encounter_id'] }}, '{{ $bed['patient'] }}')" class="fm-tip-btn" style="color: var(--info); flex-shrink: 0;" title="Clinical Notes"><i class="bi bi-pencil-square"></i></button>
                                                                                <button type="button" onclick="openMedicationModal({{ $bed['encounter_id'] }})" class="fm-tip-btn" style="color: var(--primary); flex-shrink: 0;" title="Issue Medication"><i class="bi bi-capsule"></i></button>
                                                                                <button type="button" onclick="openLabOrderModal({{ $bed['encounter_id'] }})" class="fm-tip-btn" style="color: var(--warning); flex-shrink: 0;" title="Order Lab"><i class="bi bi-droplet-half"></i></button>
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

function toggleMeds(encounterId) {
    const extraMeds = document.getElementById('extra-meds-' + encounterId);
    const toggleBtn = document.getElementById('btn-toggle-meds-' + encounterId);
    
    if (extraMeds.style.display === 'none' || extraMeds.style.display === '') {
        // Toggle off all other popovers first to avoid clutter
        document.querySelectorAll('.core1-med-popover').forEach(pop => {
            pop.style.display = 'none';
        });
        
        extraMeds.style.display = 'block';
        if (toggleBtn) toggleBtn.style.visibility = 'hidden';
    } else {
        extraMeds.style.display = 'none';
        if (toggleBtn) toggleBtn.style.visibility = 'visible';
    }
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
{{-- Vitals History Modal --}}
<div id="vitalsHistoryModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:1200; align-items:center; justify-content:center; padding: 20px;" role="dialog" aria-modal="true">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 650px; padding:0; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <div style="padding: 20px 28px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--text-dark);">Vitals History</h3>
                    <p id="vh-patient-name" style="margin: 0; font-size: 12px; color: var(--text-gray); font-weight: 500;"></p>
                </div>
            </div>
            <button type="button" onclick="closeModal('vitalsHistoryModal')" style="background: var(--bg-hover); border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-gray);">
                <i class="bi bi-x-lg" style="font-size: 12px;"></i>
            </button>
        </div>
        <div style="flex: 1; overflow-y: auto; padding: 24px; background: var(--bg-light);">
            <div id="vh-content" style="display: flex; flex-direction: column; gap: 16px;">
                {{-- Content will be injected via JS --}}
            </div>
        </div>
    </div>
</div>

<script>
function openVitalsHistoryModal(encounterId, patientName, triagesJson) {
    const modal = document.getElementById('vitalsHistoryModal');
    const nameEl = document.getElementById('vh-patient-name');
    const contentEl = document.getElementById('vh-content');
    
    nameEl.innerText = patientName;
    contentEl.innerHTML = '';
    
    const triages = JSON.parse(triagesJson);
    
    if (triages.length === 0) {
        contentEl.innerHTML = `<div style="text-align:center; padding:40px; color:var(--text-gray); font-style:italic;">No vital records found for this encounter.</div>`;
    } else {
        triages.forEach(t => {
            const date = new Date(t.created_at).toLocaleString('en-US', { 
                month: 'short', day: 'numeric', year: 'numeric', 
                hour: '2-digit', minute: '2-digit' 
            });
            
            const card = document.createElement('div');
            card.style.background = 'white';
            card.style.borderRadius = '12px';
            card.style.border = '1px solid var(--border-color)';
            card.style.padding = '16px';
            card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.02)';
            
            card.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--primary);"><i class="bi bi-calendar-check mr-5"></i>${date}</span>
                    <span style="font-size: 10px; background: var(--bg-light); padding: 2px 8px; border-radius: 4px; color: var(--text-gray); font-weight: 600;">#${t.id}</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                    <div style="text-align: center;">
                        <div style="font-size: 9px; text-transform: uppercase; color: var(--text-gray); font-weight: 700; margin-bottom: 4px;">BP</div>
                        <div style="font-size: 13px; font-weight: 700; color: #be123c;">${t.blood_pressure || '--'}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 9px; text-transform: uppercase; color: var(--text-gray); font-weight: 700; margin-bottom: 4px;">HR</div>
                        <div style="font-size: 13px; font-weight: 700; color: #15803d;">${t.heart_rate || '--'} <small style="font-size: 10px; font-weight: normal;">bpm</small></div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 9px; text-transform: uppercase; color: var(--text-gray); font-weight: 700; margin-bottom: 4px;">Temp</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1d4ed8;">${t.temperature || '--'}°C</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 9px; text-transform: uppercase; color: var(--text-gray); font-weight: 700; margin-bottom: 4px;">SpO2</div>
                        <div style="font-size: 13px; font-weight: 700; color: #b45309;">${t.spo2 || '--'}%</div>
                    </div>
                </div>
                ${t.notes ? `<div style="margin-top: 12px; font-size: 11px; color: var(--text-gray); font-style: italic; background: var(--bg-light); padding: 8px; border-radius: 6px;">"${t.notes}"</div>` : ''}
            `;
            contentEl.appendChild(card);
        });
    }
    
    modal.style.display = 'flex';
}
</script>
@endsection
