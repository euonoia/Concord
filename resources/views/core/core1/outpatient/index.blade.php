@extends('core.core1.layouts.app')

@section('title', 'Doctor | Outpatient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Doctor's Clinical Workspace</h1>
            <p class="core1-subtitle">Review schedules, prescribe treatments, and manage clinical outcomes</p>
        </div>

        @if(auth()->user()->role === 'doctor' || auth()->user()->isAdmin())
            <div class="d-flex gap-2">
                <button class="core1-btn core1-btn-outline">
                    <i class="bi bi-clock-history"></i>
                    <span class="ml-10">My History</span>
                </button>
                <button class="core1-btn core1-btn-primary">
                    <i class="bi bi-play-fill"></i>
                    <span class="ml-10">Next Consultation</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Clinical Stats Grid -->
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-calendar-check text-blue mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['my_appointments'] }}</p>
                <p class="text-xs text-gray">My Appointments</p>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-person-check text-green mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['consulted'] }}</p>
                <p class="text-xs text-gray">Patients Consulted Today</p>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-clipboard-pulse text-orange mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['pending_results'] }}</p>
                <p class="text-xs text-gray">Pending Lab Results</p>
            </div>
        </div>

        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-stopwatch text-blue mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['avg_consultation_time'] }}</p>
                <p class="text-xs text-gray">Avg. Consultation</p>
            </div>
        </div>
    </div>

    <!-- Clinical Workflow Tabs -->
    <div class="core1-card no-hover p-0 overflow-hidden mt-30">
        <div class="core1-tabs-header border-bottom">
            <button class="core1-tab-btn active" onclick="switchTab(event, 'arrival-logs')">
                <i class="bi bi-journal-check mr-5"></i> Arrival Logs & Triage
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'consultation-tracking')">
                <i class="bi bi-activity mr-5"></i> Consultation Tracking
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'diagnostic-orders')">
                <i class="bi bi-clipboard-pulse mr-5"></i> Diagnostic Orders
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'prescription-recording')">
                <i class="bi bi-capsule mr-5"></i> Prescription & Treatment
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'follow-up')">
                <i class="bi bi-calendar-check mr-5"></i> Follow Up
            </button>
        </div>

        <div class="tab-content p-25">
            <!-- Consultation Tracking Tab -->
            <div id="consultation-tracking" class="core1-tab-pane">
                <div class="d-flex justify-between items-center mb-20">
                    <h3 class="core1-title core1-section-title">Active Queue</h3>
                    <div class="core1-toolbar-search">
                        <i class="bi bi-search"></i>
                        <input type="text" placeholder="Search my patients...">
                    </div>
                </div>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>APP. TIME</th>
                                <th>PATIENT</th>
                                <th>TYPE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td class="font-bold">{{ $apt['time'] }}</td>
                                    <td>
                                        <div class="font-bold text-blue">{{ $apt['patient'] }}</div>
                                        <div class="text-xs text-gray">{{ $apt['id'] }}</div>
                                    </td>
                                    <td>
                                        <span class="core1-status-tag core1-tag-neutral">
                                            {{ ucfirst($apt['type']) }}
                                        </span>
                                    </td>
                                    <td>
                                       @php
    $statusClass = 'core1-tag-recovering';

    if($apt['status'] == 'In consultation') {
        $statusClass = 'core1-tag-critical';
    }

    if($apt['status'] == 'Waiting') {
        $statusClass = 'core1-tag-cleaning';
    }

    if($apt['status'] == 'Consulted') {
        $statusClass = 'core1-tag-stable';
    }
@endphp

                                        <span class="core1-status-tag {{ $statusClass }}">
                                            {{ $apt['status'] }}
                                        </span>
                                    </td>
                            <td>
                                <div style="display: grid; grid-template-columns: repeat(4, 32px); gap: 6px; justify-content: center;">
                                    <button type="button" class="core1-btn-sm core1-btn-outline" 
                                            onclick="openPatientModal({{ $apt['patient_id'] }})" title="View Patient Details" style="flex-shrink: 0; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($apt['triage'])
                                        <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                onclick="openViewTriageModal(this)"
                                                data-bp="{{ $apt['triage']['blood_pressure'] }}"
                                                data-hr="{{ $apt['triage']['heart_rate'] }}"
                                                data-temp="{{ $apt['triage']['temperature'] }}"
                                                data-spo2="{{ $apt['triage']['spo2'] }}"
                                                data-level="{{ $apt['triage']['triage_level'] }}"
                                                data-notes="{{ $apt['triage']['notes'] }}"
                                                title="View Nurse Triage Assessment" style="flex-shrink: 0; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-clipboard2-pulse"></i>
                                        </button>
                                    @endif
                                    @if($apt['status'] === 'Triaged' || $apt['status'] === 'In consultation')
                                        <button class="core1-btn-sm core1-btn-primary" 
                                                onclick="openConsultationModal({{ $apt['id'] }}, '{{ $apt['patient'] }}')" style="flex-shrink: 0; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0 12px; grid-column: span 2;">
                                            <i class="bi bi-chat-left-dots"></i> Consult
                                        </button>
                                    @endif
                                </div>
                            </td>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arrival Logs & Triage Tab -->
            <div id="arrival-logs" class="core1-tab-pane active">
                <div class="d-flex justify-between items-center mb-20">
                    <h3 class="core1-title core1-section-title">Patient Arrival & Triage Summary</h3>
                </div>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>ARRIVAL</th>
                                <th>PATIENT</th>
                                <th>VITALS</th>
                                <th>STATUS</th>
                                <th class="text-center" style="width: 220px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $reg)
                                <tr>
                                    <td>{{ $reg['date'] }}</td>
                                    <td class="font-bold text-blue">{{ $reg['patient'] }}</td>
                                    <td>
                                        @if($reg['triage'] !== 'No Triage')
                                            <span class="text-xs text-dark">{{ $reg['triage'] }}</span>
                                        @else
                                            <span class="text-xs text-gray italic">Pending Triage</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="core1-status-tag {{ $reg['status'] === 'Triaged' ? 'core1-tag-stable' : 'core1-tag-cleaning' }}">
                                            {{ $reg['status'] }}
                                        </span>
                                    </td>
                                    <td style="display: grid; grid-template-columns: repeat(4, 32px); gap: 6px; justify-content: center; border: none;">
                                        <button type="button" class="core1-btn-sm core1-btn-outline" 
                                                onclick="openPatientModal({{ $reg['patient_id'] }})" title="View Patient Details" style="flex-shrink: 0; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="core1-btn-sm core1-btn-outline" 
                                                onclick="openTriageModal({{ $reg['id'] }})" style="flex-shrink: 0; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-heart-pulse"></i>
                                        </button>

                                        @if($reg['status'] === 'Triaged' && $reg['type'] === 'Pending')
                                            <form action="{{ route('core1.outpatient.disposition', $reg['id']) }}" method="POST" class="m-0" style="flex-shrink: 0; width: 32px; height: 32px;">
                                                @csrf
                                                <input type="hidden" name="type" value="OPD">
                                                <button type="submit" class="core1-btn-sm core1-btn-primary" title="Send to Outpatient" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-person-walking"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('core1.outpatient.disposition', $reg['id']) }}" method="POST" class="m-0" style="flex-shrink: 0; width: 32px; height: 32px;">
                                                @csrf
                                                <input type="hidden" name="type" value="IPD">
                                                <button type="submit" class="core1-btn-sm core1-btn-outline" title="Admit to Inpatient" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-hospital"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Diagnostic Orders Tab -->
            <div id="diagnostic-orders" class="core1-tab-pane">
                <div class="d-flex justify-between items-center mb-20">
                    <h3 class="core1-title core1-section-title">Laboratory & Diagnostic Management</h3>
                </div>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>ORDERED</th>
                                <th>PATIENT</th>
                                <th>ORDERED BY</th>
                                <th>TEST</th>
                                <th>PRIORITY</th>
                                <th>INDICATION</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="diagnosticOrdersTbody">
                            @foreach($diagnosticOrders as $order)
                                @php
                                    $syncClass = match($order->sync_status ?? 'Pending') {
                                        'ResultReceived' => 'core1-tag-critical',
                                        'Failed'         => 'core1-tag-cleaning',
                                        default          => 'core1-tag-neutral',
                                    };
                                    $syncLabel = match($order->sync_status ?? 'Pending') {
                                        'ResultReceived' => 'Result Received',
                                        default          => 'Pending',
                                    };
                                    $priorityClass = match($order->priority ?? 'Routine') {
                                        'STAT'   => 'core1-tag-critical',
                                        'Urgent' => 'core1-tag-cleaning',
                                        default  => 'core1-tag-neutral',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="font-bold text-blue">{{ $order->patient->name ?? $order->encounter->patient->name ?? 'Unknown' }}</td>
                                    <td class="text-xs">{{ $order->doctor->name ?? 'Unknown' }}</td>
                                    <td class="font-bold">{{ $order->test_name }}</td>
                                    <td>
                                        <span class="core1-status-tag {{ $priorityClass }}">{{ $order->priority ?? 'Routine' }}</span>
                                    </td>
                                    <td class="text-xs">{{ $order->clinical_note }}</td>
                                    <td>
                                        <span class="core1-status-tag {{ $syncClass }}">{{ $syncLabel }}</span>
                                    </td>
                                    <td>
                                        @if($order->sync_status === 'ResultReceived' && $order->result_data)
                                            <button type="button" class="core1-btn-sm core1-btn-outline"
                                                    onclick="openResultsModal(this)"
                                                    data-test="{{ $order->test_name }}"
                                                    data-patient="{{ $order->patient->name ?? 'Unknown' }}"
                                                    data-result="{{ $order->result_data }}"
                                                    data-received="{{ $order->result_received_at ? $order->result_received_at->format('Y-m-d H:i') : '' }}"
                                                    title="View Lab Results">
                                                <i class="bi bi-file-earmark-medical"></i> View Results
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Prescription Recording Tab -->
            <div id="prescription-recording" class="core1-tab-pane">
                <div class="d-flex justify-between items-center mb-20">
                    <h3 class="core1-title core1-section-title">Record Prescriptions</h3>
                    <p class="text-xs text-gray">Prescriptions are issued during the consultation process in the Consultation Room.</p>
                </div>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>PATIENT</th>
                                <th>MEDICATION</th>
                                <th>QTY</th>
                                <th>DOSAGE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescriptions as $rx)
                                <tr>
                                    <td>{{ $rx->created_at->format('Y-m-d') }}</td>
                                    <td class="font-bold text-blue">{{ $rx->encounter->patient->name ?? 'Unknown' }}</td>
                                    <td class="font-bold">{{ $rx->medication }}</td>
                                    <td class="font-black text-indigo-600">{{ $rx->quantity }}</td>
                                    <td class="text-xs">
                                        <span class="font-bold">{{ $rx->dosage }}</span><br>
                                        <span class="text-gray italic">{{ $rx->instructions }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $rxStatusClass = match($rx->status ?? 'Ordered') {
                                                'Dispensed'    => 'core1-tag-stable',
                                                'Administered' => 'core1-tag-neutral',
                                                'Synced'       => 'core1-tag-recovering',
                                                default        => 'core1-tag-cleaning',
                                            };
                                        @endphp
                                        <span class="core1-status-tag {{ $rxStatusClass }}">
                                            {{ $rx->status ?? 'Ordered' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($rx->status === 'Dispensed')
                                            <form action="{{ route('core1.outpatient.administerMedication', $rx->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="core1-btn-sm core1-btn-success" title="Mark as Administered">
                                                    <i class="bi bi-check-circle"></i> Administer
                                                </button>
                                            </form>
                                        @elseif($rx->status === 'Administered')
                                            <span class="text-xs text-green font-bold"><i class="bi bi-check-all"></i> Given</span>
                                        @else
                                            <div style="padding: 4px 8px; background: var(--warning-light); border: 1px dashed var(--warning); border-radius: 4px; color: var(--warning); font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="bi bi-hourglass-split"></i> PENDING PHARMACY ({{ $rx->status }})
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Follow Up Tab -->
            <div id="follow-up" class="core1-tab-pane">
                <div class="d-flex justify-between items-center mb-20">
                    <h3 class="core1-title core1-section-title">Schedule Follow-up Appointment</h3>
                </div>
                <div class="core1-card shadow-none border" style="max-width: 800px;">
                    <form action="{{ route('core1.appointments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="follow-up">
                        
                        <div class="core1-form-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="core1-form-group core1-col-span-2">
                                <label for="patient_id" class="font-bold block mb-5">Patient *</label>
                                <select id="patient_id" name="patient_id" required class="core1-input w-full">
                                    <option value="">Select Patient</option>
                                    @foreach($patients as $p)
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->patient_id ?? $p->mrn }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="core1-form-group core1-col-span-2">
                                <label for="doctor_id" class="font-bold block mb-5">Doctor *</label>
                                <select id="doctor_id" name="doctor_id" required class="core1-input w-full">
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id', auth()->user()->id) == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="core1-form-group">
                                <label for="appointment_date" class="font-bold block mb-5">Date *</label>
                                <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required class="core1-input w-full">
                            </div>

                            <div class="core1-form-group">
                                <label for="appointment_time" class="font-bold block mb-5">Time *</label>
                                <select id="appointment_time" name="appointment_time" required class="core1-input w-full">
                                    <option value="">Select Date & Doctor First</option>
                                </select>
                                <p id="availability-msg" class="core1-error-text" style="color: var(--text-light); font-size: 12px; margin-top: 5px;"></p>
                            </div>

                            <div class="core1-form-group core1-col-span-2">
                                <label for="reason" class="font-bold block mb-5">Reason</label>
                                <textarea id="reason" name="reason" rows="3" class="core1-input w-full" placeholder="Notes for this follow-up...">{{ old('reason') }}</textarea>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                            <button type="reset" class="core1-btn core1-btn-outline">Reset</button>
                            <button type="submit" class="core1-btn core1-btn-primary">Book Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Generic Triage Modal -->
    <div id="triageModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:500px; max-width:90%;">
            <div class="core1-header border-bottom mb-20 pb-10">
                <h3 class="core1-title">Clinical Triage</h3>
                <p class="core1-subtitle">Record patient vitals and urgency level</p>
            </div>
            <form id="triageForm" method="POST">
                @csrf
                <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label class="font-bold block mb-5">Blood Pressure</label>
                        <input type="text" name="blood_pressure" class="core1-input w-full" placeholder="e.g. 120/80">
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Heart Rate (bpm)</label>
                        <input type="number" name="heart_rate" class="core1-input w-full" placeholder="72">
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Temp (°C)</label>
                        <input type="number" step="0.1" name="temperature" class="core1-input w-full" placeholder="36.5">
                    </div>
                    <div>
                        <label class="font-bold block mb-5">SpO2 (%)</label>
                        <input type="number" name="spo2" class="core1-input w-full" placeholder="98">
                    </div>
                </div>
                <div class="mb-15">
                    <label class="font-bold block mb-5">Acuity Level</label>
                    <select name="triage_level" id="triageLevelSelect" class="core1-input w-full" onchange="toggleSendToAdmissionBtn()">
                        <option value="5">Level 5 - Non-Urgent</option>
                        <option value="4">Level 4 - Less Urgent</option>
                        <option value="3">Level 3 - Urgent</option>
                        <option value="2">Level 2 - Emergent</option>
                        <option value="1">Level 1 - Resuscitation</option>
                    </select>
                </div>
                <div class="mb-20">
                    <label class="font-bold block mb-5">Triage Notes</label>
                    <textarea name="notes" class="core1-input w-full" rows="3" placeholder="General observations..."></textarea>
                </div>
                <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                    <input type="hidden" name="send_to_admission" id="sendToAdmissionFlag" value="0">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('triageModal')">Cancel</button>
                    <button type="button" id="sendToAdmissionBtn" class="core1-btn core1-btn-outline-primary" style="display:none;" onclick="submitTriageForAdmission()">Send to Admission</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Save Triage</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Generic Consultation Modal (SOAP) -->
    <div id="consultationModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:700px; max-width:90%; max-height: 90vh; overflow-y: auto;">
            <div class="core1-header border-bottom mb-20 pb-10">
                <h3 class="core1-title">Consultation Room</h3>
                <p class="core1-subtitle">Consulting: <span id="consultingPatientName" class="font-bold text-dark"></span></p>
            </div>
            <form id="consultationForm" method="POST">
                @csrf
                <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label class="font-bold block mb-5">Subjective (Symptoms)</label>
                        <textarea name="subjective" class="core1-input w-full" rows="3" placeholder="Patient's complaints..."></textarea>
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Objective (Exam)</label>
                        <textarea name="objective" class="core1-input w-full" rows="3" placeholder="Physical exam findings..."></textarea>
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Assessment (Diagnosis)</label>
                        <textarea name="assessment" class="core1-input w-full" rows="3" placeholder="Clinical diagnosis..."></textarea>
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Plan (Treatment)</label>
                        <textarea name="plan" class="core1-input w-full" rows="3" placeholder="Next steps and treatment..."></textarea>
                    </div>
                </div>
                <div class="mt-15 mb-20">
                    <label class="font-bold block mb-5">Confidential Doctor Notes</label>
                    <textarea name="doctor_notes" class="core1-input w-full" rows="2" placeholder="Internal remarks..."></textarea>
                </div>
                <!-- Orders inside consultation -->
                <div class="mb-20 pt-15 border-top">
                    <h4 class="font-bold mb-10 text-sm">Clinical Actions</h4>
                    <div class="core1-flex-gap-2">
                        <button type="button" class="core1-btn-sm core1-btn-outline" onclick="openLabModal()">
                            <i class="bi bi-droplet"></i> Order Lab Test
                        </button>
                        <button type="button" class="core1-btn-sm core1-btn-outline" onclick="openPrescriptionModal()">
                            <i class="bi bi-capsule"></i> Prescribe Medication
                        </button>
                        <button type="button" class="core1-btn-sm core1-btn-outline" onclick="openSurgeryOrderModal(currentEncounterId)">
                            <i class="bi bi-scissors"></i> Order Surgery
                        </button>
                        <button type="button" class="core1-btn-sm core1-btn-outline" onclick="openDietOrderModal(currentEncounterId)">
                            <i class="bi bi-egg-fried"></i> Set Diet
                        </button>
                    </div>
                </div>

                <div class="core1-flex-gap-2 justify-between pt-10 border-top">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('consultationModal')">Pause</button>
                    <div class="core1-flex-gap-2">
                        <button type="submit" class="core1-btn core1-btn-primary">Save Notes</button>
                        <button type="button" class="core1-btn core1-btn-success" id="dischargeBtn">Discharge Home</button>
                        <button type="button" class="core1-btn core1-btn-outline-primary" id="admitBtn">Recommend Admission</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lab Order Modal -->
    <div id="labModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1050; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
            <h4 class="font-bold mb-15">Order Laboratory Test</h4>
            <form method="POST" action="{{ route('core1.outpatient.storeLabOrder') }}">
                @csrf
                <input type="hidden" name="encounter_id" id="labEncounterId">
                <div class="mb-10">
                    <label class="font-bold block mb-5">Test Name</label>
                    <select name="test_name" class="core1-input w-full" required>
                        <option value="">Select Test...</option>
                        <option value="Complete Blood Count (CBC)">Complete Blood Count (CBC)</option>
                        <option value="Urinalysis">Urinalysis</option>
                        <option value="Blood Chemistry Panel">Blood Chemistry Panel</option>
                        <option value="Lipid Panel">Lipid Panel</option>
                        <option value="Microbiology/Molecular Tests">Microbiology/Molecular Tests</option>
                    </select>
                </div>
                <div class="mb-10">
                    <label class="font-bold block mb-5">Priority</label>
                    <select name="priority" class="core1-input w-full">
                        <option value="Routine">Routine</option>
                        <option value="Urgent">Urgent</option>
                        <option value="STAT">STAT (Emergency)</option>
                    </select>
                </div>
                <div class="mb-15">
                    <label class="font-bold block mb-5">Clinical Indication</label>
                    <textarea name="clinical_note" class="core1-input w-full" rows="2" placeholder="Reason for test..."></textarea>
                </div>
                <div class="core1-flex-gap-2 justify-end">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('labModal')">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Order Test</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lab Results Modal -->
    <div id="labResultsModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1050; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:500px; max-width:90%;">
            <div class="core1-flex-between mb-15">
                <h4 class="font-bold">Lab Results</h4>
                <button type="button" onclick="closeModal('labResultsModal')" style="background:transparent; border:none; font-size:1.4rem; color:var(--text-gray); cursor:pointer;"><i class="bi bi-x"></i></button>
            </div>
            <div class="mb-10">
                <label class="text-xs font-bold text-gray block mb-3">TEST</label>
                <p id="resultTestName" class="font-bold text-dark" style="font-size:15px;"></p>
            </div>
            <div class="mb-10">
                <label class="text-xs font-bold text-gray block mb-3">PATIENT</label>
                <p id="resultPatientName" class="font-bold text-blue" style="font-size:14px;"></p>
            </div>
            <div class="mb-10">
                <label class="text-xs font-bold text-gray block mb-3">RECEIVED AT</label>
                <p id="resultReceivedAt" class="text-dark" style="font-size:13px;"></p>
            </div>
            <div class="mb-15" style="background:#f8f9fb; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">
                <table class="w-full text-left border-collapse" style="font-size:13px; color:var(--text-dark);">
                    <tbody id="resultDataContent">
                        <!-- Dynamic rows injected here -->
                    </tbody>
                </table>
            </div>
            <div class="core1-flex-gap-2 justify-end pt-10 border-top">
                <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('labResultsModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Admission Modal – 2D Bed Picker -->
    <div id="admissionModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1050; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:820px; max-width:95%; max-height:90vh; display:flex; flex-direction:column; padding:0; overflow:hidden; border-radius:14px;">

            {{-- Modal Header --}}
            <div style="padding:20px 24px; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; flex-shrink:0; background:#fff;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:40px; height:40px; border-radius:10px; background:var(--info-light); color:var(--info); display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <div>
                        <h3 class="core1-title" style="margin:0; font-size:17px;">Admit Patient</h3>
                        <p class="core1-subtitle" style="margin:0; font-size:12px;">Select an available bed from the floor plan below</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('admissionModal')" style="background:transparent; border:none; font-size:1.6rem; color:var(--text-gray); cursor:pointer; line-height:1;">
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <form id="admissionForm" method="POST" action="{{ route('core1.admissions.store') }}" onsubmit="submitAdmissionAjax(event)" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
                @csrf
                <input type="hidden" name="encounter_id" id="admissionEncounterId">
                <input type="hidden" name="bed_id" id="selectedBedId" required>

                {{-- Zone Tab Strip --}}
                @php
                    $bpZones = ['ICU' => 'bi-heart-pulse-fill', 'ER' => 'bi-lightning-fill', 'WARD' => 'bi-hospital-fill', 'OR' => 'bi-scissors'];
                    $bpFloor = [];
                    foreach ($bpZones as $zk => $_) {
                        $bpFloor[$zk] = [];
                    }
                    foreach ($wards as $ward) {
                        $zk = strtoupper(trim($ward->ward_type));
                        if (!array_key_exists($zk, $bpFloor)) $zk = 'WARD';
                        $bpFloor[$zk][] = $ward;
                    }
                    $firstZone = collect($bpFloor)->first(fn($w) => !empty($w)) ? array_key_first(array_filter($bpFloor, fn($w) => !empty($w))) : 'WARD';
                @endphp

                <div class="bp-zone-tabs" style="display:flex; gap:0; background:#f8f9fb; border-bottom:1px solid var(--border-color); flex-shrink:0;">
                    @foreach($bpZones as $zk => $icon)
                        @php
                            $zoneColors = ['ICU' => '#dc2626', 'ER' => '#ea580c', 'WARD' => '#2563eb', 'OR' => '#7c3aed'];
                            $hasZoneBeds = !empty($bpFloor[$zk]);
                        @endphp
                        <button type="button"
                            class="bp-zone-tab {{ $zk === $firstZone ? 'active' : '' }} {{ !$hasZoneBeds ? 'bp-zone-tab-empty' : '' }}"
                            data-zone="{{ $zk }}"
                            onclick="switchBpZone('{{ $zk }}')"
                            style="padding:12px 20px; border:none; background:none; font-size:13px; font-weight:600; color:{{ $zk === $firstZone ? $zoneColors[$zk] : 'var(--text-gray)' }}; cursor:pointer; border-bottom:2px solid {{ $zk === $firstZone ? $zoneColors[$zk] : 'transparent' }}; display:flex; align-items:center; gap:7px; transition:all 0.15s; white-space:nowrap;"
                            data-color="{{ $zoneColors[$zk] }}">
                            <i class="bi {{ $icon }}" style="font-size:13px;"></i> {{ $zk }}
                            <span style="font-size:11px; font-weight:500; background:{{ !$hasZoneBeds ? '#e5e7eb' : '#eef2ff' }}; color:{{ !$hasZoneBeds ? '#9ca3af' : 'var(--info)' }}; padding:1px 7px; border-radius:20px; margin-left:2px;">
                                {{ collect($bpFloor[$zk] ?? [])->flatMap->rooms->flatMap->beds->count() }}
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Floor Plan Body --}}
                <div style="flex:1; overflow-y:auto; padding:20px 24px; background:#f8f9fb;">

                    @foreach($bpZones as $zk => $icon)
                        <div id="bp-zone-{{ $zk }}" class="bp-zone-panel" style="{{ $zk === $firstZone ? '' : 'display:none;' }}">
                            @if(empty($bpFloor[$zk]))
                                <div style="text-align:center; padding:40px 0; color:var(--text-light);">
                                    <i class="bi bi-building-slash" style="font-size:2rem; display:block; margin-bottom:8px; opacity:0.4;"></i>
                                    No wards configured for {{ $zk }}
                                </div>
                            @else
                                @foreach($bpFloor[$zk] as $ward)
                                    <div class="bp-ward" style="margin-bottom:20px;">
                                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                                            <i class="bi bi-signpost-2" style="color:var(--text-gray); font-size:12px;"></i>
                                            <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-dark);">{{ $ward->name }}</span>
                                        </div>

                                        @foreach($ward->rooms as $room)
                                            <div class="bp-room" style="background:#fff; border:1px solid var(--border-color); border-radius:10px; padding:14px; margin-bottom:10px;">
                                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid var(--border-color);">
                                                    <span style="font-size:11px; font-weight:700; background:#f3f4f6; color:var(--text-dark); padding:2px 10px; border-radius:20px;">Room {{ $room->room_number }}</span>
                                                    @if($room->room_type)
                                                        <span style="font-size:10px; color:var(--text-gray);">{{ $room->room_type }}</span>
                                                    @endif
                                                </div>
                                                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                                                    @foreach($room->beds as $bed)
                                                        @php
                                                            $bedStatus = strtolower($bed->status);
                                                            $activeAdmission = $bed->admissions->first();
                                                            $patientName = $activeAdmission?->encounter?->patient?->name;
                                                            $firstName = $patientName ? explode(' ', $patientName)[0] : null;
                                                            $mrn = $activeAdmission?->encounter?->patient?->mrn;

                                                            $isAvailable = $bedStatus === 'available';
                                                            $isOccupied  = $bedStatus === 'occupied';
                                                        @endphp

                                                        <div class="bp-bed-wrap" style="position:relative;">
                                                            <div class="bp-bed bp-bed-{{ $bedStatus }} {{ $isAvailable ? 'bp-bed-clickable' : '' }}"
                                                                 data-bed-id="{{ $bed->id }}"
                                                                 data-bed-label="{{ $ward->name }} — Room {{ $room->room_number }} — Bed {{ $bed->bed_number }}"
                                                                 @if($isAvailable) onclick="selectBed(this)" @endif
                                                                 title="{{ $isOccupied && $patientName ? $patientName . ($mrn ? ' (MRN: '.$mrn.')' : '') : ucfirst($bedStatus) }}"
                                                                 style="
                                                                    width:70px; height:80px;
                                                                    border-radius:8px;
                                                                    border:2px solid {{ $isAvailable ? '#86efac' : ($isOccupied ? '#fca5a5' : '#fcd34d') }};
                                                                    background:{{ $isAvailable ? '#f0fff4' : ($isOccupied ? '#fff5f5' : '#fffbeb') }};
                                                                    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;
                                                                    cursor:{{ $isAvailable ? 'pointer' : 'not-allowed' }};
                                                                    opacity:{{ $isAvailable ? '1' : '0.7' }};
                                                                    transition:all 0.15s;
                                                                    position:relative; overflow:hidden;
                                                                    user-select:none;
                                                                 ">
                                                                {{-- Bed icon --}}
                                                                <i class="bi bi-caret-up-fill" style="font-size:9px; color:{{ $isAvailable ? '#166534' : ($isOccupied ? '#991b1b' : '#92400e') }};"></i>
                                                                <div style="width:46px; height:24px; border-radius:4px 4px 6px 6px; background:{{ $isAvailable ? '#bbf7d0' : ($isOccupied ? '#fecaca' : '#fde68a') }};"></div>
                                                                {{-- Bed number --}}
                                                                <span style="font-size:10px; font-weight:700; color:{{ $isAvailable ? '#166534' : ($isOccupied ? '#991b1b' : '#92400e') }};">{{ $bed->bed_number }}</span>
                                                                {{-- Patient short name (occupied only) --}}
                                                                @if($isOccupied && $firstName)
                                                                    <span style="font-size:9px; font-weight:600; color:#991b1b; max-width:66px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:center; line-height:1.1;">{{ $firstName }}</span>
                                                                @endif
                                                                {{-- Status badge --}}
                                                                @if(!$isAvailable && !$isOccupied)
                                                                    <span style="font-size:8px; color:#92400e; font-weight:600;">Cleaning</span>
                                                                @endif
                                                                {{-- Selected checkmark overlay --}}
                                                                <div class="bp-bed-check" style="display:none; position:absolute; inset:0; background:rgba(37,99,235,0.12); border-radius:6px; align-items:center; justify-content:center;">
                                                                    <i class="bi bi-check-circle-fill" style="font-size:1.4rem; color:#2563eb;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach

                </div>

                {{-- Legend + Selection Bar + Actions --}}
                <div style="flex-shrink:0; border-top:1px solid var(--border-color); background:#fff; padding:14px 24px;">
                    {{-- Legend --}}
                    <div style="display:flex; gap:16px; margin-bottom:12px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-gray);">
                            <span style="width:12px; height:12px; border-radius:3px; background:#f0fff4; border:2px solid #86efac; display:inline-block;"></span> Available
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-gray);">
                            <span style="width:12px; height:12px; border-radius:3px; background:#fff5f5; border:2px solid #fca5a5; display:inline-block;"></span> Occupied
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-gray);">
                            <span style="width:12px; height:12px; border-radius:3px; background:#fffbeb; border:2px solid #fcd34d; display:inline-block;"></span> Cleaning
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-gray);">
                            <span style="width:12px; height:12px; border-radius:3px; background:rgba(37,99,235,0.12); border:2px solid #2563eb; display:inline-block;"></span> Selected
                        </div>
                    </div>
                    {{-- Selection indicator + actions --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                        <div id="bpSelectionBar" style="font-size:13px; color:var(--text-gray); font-style:italic;">
                            No bed selected — click an available bed above
                        </div>
                        <div style="display:flex; gap:10px;">
                            <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('admissionModal')">Cancel</button>
                            <button type="submit" class="core1-btn core1-btn-primary" id="admitPatientBtn" disabled style="opacity:0.5;">
                                <i class="bi bi-hospital mr-5"></i> Admit Patient
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Issue e-Prescription Modal -->
    <div id="prescriptionModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1050; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
            <h4 class="font-bold mb-15">Issue e-Prescription</h4>
            <form method="POST" action="{{ route('core1.outpatient.storePrescription') }}">
                @csrf
                <input type="hidden" name="encounter_id" id="rxEncounterId">
                <div class="mb-10" style="position: relative;">
                    <label class="font-bold block mb-5">Medication Name</label>
                    <input type="text" name="medication" id="medicationSearch" class="core1-input w-full" autocomplete="off" required placeholder="e.g. Amoxicillin 500mg">
                    <div id="drugSearchResults" class="core1-card" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:1100; max-height:200px; overflow-y:auto; padding:5px; margin-top:5px; box-shadow: var(--shadow-md);"></div>
                </div>
                <div class="core1-stats-grid" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <div>
                        <label class="font-bold block mb-5">Dosage</label>
                        <input type="text" name="dosage" class="core1-input w-full" required placeholder="e.g. 1 capsule TDS">
                    </div>
                    <div>
                        <label class="font-bold block mb-5">Duration</label>
                        <input type="text" name="duration" class="core1-input w-full" placeholder="e.g. 5 days">
                    </div>
                </div>
                <div class="mb-10">
                    <label class="font-bold block mb-5">Quantity</label>
                    <input type="number" name="quantity" class="core1-input w-full" required placeholder="Number of units" min="1">
                </div>
                <div class="mb-15">
                    <label class="font-bold block mb-5">Instructions</label>
                    <input type="text" name="instructions" class="core1-input w-full" placeholder="e.g. Take after meals">
                </div>
                <div class="core1-flex-gap-2 justify-end">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('prescriptionModal')">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Prescribe</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Patient Details Modal (Clinical View) -->
    <div id="patientDetailsModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">

<script>
    function fetchDiagnosticOrders() {
        fetch('{{ route("core1.outpatient.diagnosticOrders.json") }}')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('diagnosticOrdersTbody');
                if(!tbody) return;
                tbody.innerHTML = '';
                data.forEach(order => {
                    let syncClass = 'core1-tag-neutral';
                    let syncLabel = 'Pending';
                    if (order.sync_status === 'ResultReceived') {
                        syncClass = 'core1-tag-critical';
                        syncLabel = 'Result Received';
                    } else if (order.sync_status === 'Failed') {
                        syncClass = 'core1-tag-cleaning';
                    }

                    let priorityClass = 'core1-tag-neutral';
                    if (order.priority === 'STAT') {
                        priorityClass = 'core1-tag-critical';
                    } else if (order.priority === 'Urgent') {
                        priorityClass = 'core1-tag-cleaning';
                    }

                    let tr = document.createElement('tr');
                    
                    let td1 = document.createElement('td'); td1.textContent = order.created_at_fmt;
                    let td2 = document.createElement('td'); td2.className = 'font-bold text-blue'; td2.textContent = order.patient_name;
                    let td3 = document.createElement('td'); td3.className = 'text-xs'; td3.textContent = order.doctor_full;
                    let td4 = document.createElement('td'); td4.className = 'font-bold'; td4.textContent = order.test_name;
                    
                    let td5 = document.createElement('td');
                    let span5 = document.createElement('span'); span5.className = `core1-status-tag ${priorityClass}`; span5.textContent = order.priority;
                    td5.appendChild(span5);
                    
                    let td6 = document.createElement('td'); td6.className = 'text-xs'; td6.textContent = order.clinical_note || '';

                    let td7 = document.createElement('td');
                    let span7 = document.createElement('span'); span7.className = `core1-status-tag ${syncClass}`; span7.textContent = syncLabel;
                    td7.appendChild(span7);

                    let td8 = document.createElement('td');
                    if (order.sync_status === 'ResultReceived' && order.result_data) {
                        let btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'core1-btn-sm core1-btn-outline';
                        btn.setAttribute('onclick', 'openResultsModal(this)');
                        btn.setAttribute('data-test', order.test_name);
                        btn.setAttribute('data-patient', order.patient_name);
                        btn.setAttribute('data-result', order.result_data);
                        btn.setAttribute('data-received', order.result_received_at_fmt);
                        btn.title = 'View Lab Results';
                        btn.innerHTML = '<i class="bi bi-file-earmark-medical"></i> View Results';
                        td8.appendChild(btn);
                    }

                    tr.append(td1, td2, td3, td4, td5, td6, td7, td8);
                    tbody.appendChild(tr);
                });
            })
            .catch(err => console.error("Failed to fetch diagnostic orders", err));
    }

    // Auto refresh every 5 seconds
    setInterval(fetchDiagnosticOrders, 5000);
</script>
        <div class="core1-modal-content core1-card" style="width:750px; max-width:90%; max-height: 85vh; overflow-y: auto; padding:0; border-top:none; border-radius:12px;">
            <!-- Modal Header -->
            <div class="core1-flex-between" style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color); border-radius: 12px 12px 0 0; position: sticky; top: 0; z-index: 10;">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h3 class="core1-title" id="modalPatientName" style="font-size: 18px; line-height:1.2; margin:0; padding:0;">Patient Name</h3>
                        <p class="core1-subtitle" id="modalPatientMRN" style="font-size: 13px; margin:2px 0 0 0; padding:0; font-family: monospace;">MRN: ---</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('patientDetailsModal')" class="core1-btn-sm" style="background: transparent; border: none; color: var(--text-gray); font-size: 1.8rem; cursor: pointer; padding:0;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <div style="padding: 25px;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
                    <!-- Section 1: Demographics -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                            <i class="bi bi-person-lines-fill"></i> 1. Demographics
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Birth Date</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalDOB">---</p>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Gender</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalGender">---</p>
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Email</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalEmail">---</p>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Age</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalAge">---</p>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Phone</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalPhone">---</p>
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Address</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalAddress">---</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Medical Info -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                            <i class="bi bi-heart-pulse"></i> 2. Medical Info
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Blood Type</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalBloodType">---</p>
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Allergies</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--danger);" id="modalAllergies">---</p>
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Medical History</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalHistory">---</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Emergency Contact -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                            <i class="bi bi-exclamation-triangle"></i> 3. Emergency Contact
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Contact Name</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECName">---</p>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Relation</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECRelation">---</p>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Phone</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalECPhone">---</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Insurance -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin:0; display:flex; align-items:center; gap:8px;">
                            <i class="bi bi-shield-lock"></i> 4. Insurance
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Provider</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalInsurance">---</p>
                            </div>
                            <div style="grid-column: span 2;">
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 4px;">Policy Number</label>
                                <p style="font-size: 14px; font-weight: 600; margin: 0; color: var(--text-dark);" id="modalPolicy">---</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding: 20px 25px; border-top: 1px solid var(--border-color); background: var(--bg); display: flex; justify-content: flex-end; border-radius: 0 0 12px 12px;">
                <button type="button" class="core1-btn core1-btn-outline" style="border-radius: 8px; padding: 10px 20px;" onclick="closeModal('patientDetailsModal')">Close Record</button>
            </div>
        </div>
    </div>

    <!-- View Triage Results Modal (Read-Only) -->
    <div id="viewTriageModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:500px; max-width:95%; border-top:none; padding:0; overflow:hidden;">
            <!-- Modal Header -->
            <div class="core1-flex-between" style="background: var(--bg); padding: 20px 25px; border-bottom: 1px solid var(--border-color);">
                <div class="d-flex items-center gap-3">
                    <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width: 40px; height: 40px; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-size: 1.2rem;">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <div>
                        <h3 class="core1-title" style="font-size: 18px; line-height:1.2; margin:0; padding:0;">Triage Assessment</h3>
                        <p class="core1-subtitle" style="font-size: 13px; margin:2px 0 0 0; padding:0;">Nurse Recorded Clinical Vitals</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('viewTriageModal')" class="core1-btn-sm" style="background: transparent; border: none; color: var(--text-gray); font-size: 1.8rem; cursor: pointer; padding:0;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <div style="padding: 25px;">
                <!-- Vitals Grid -->
                <div style="margin-bottom: 25px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-activity"></i> Clinical Vitals
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff;">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="bi bi-droplet"></i>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); display: block; margin-bottom: 2px;">Blood Pressure</label>
                                <p style="font-size: 14px; font-weight: 700; margin: 0; color: var(--text-dark);" id="viewTriageBP">---</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff;">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: #ffe4e6; color: #e11d48; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); display: block; margin-bottom: 2px;">Heart Rate</label>
                                <p style="font-size: 14px; font-weight: 700; margin: 0; color: var(--text-dark);" id="viewTriageHR">---</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff;">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="bi bi-thermometer-half"></i>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); display: block; margin-bottom: 2px;">Temp (°C)</label>
                                <p style="font-size: 14px; font-weight: 700; margin: 0; color: var(--text-dark);" id="viewTriageTemp">---</p>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: #fff;">
                            <div style="width: 36px; height: 36px; border-radius: 6px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="bi bi-lungs"></i>
                            </div>
                            <div>
                                <label style="font-size: 11px; font-weight: 600; color: var(--text-light); display: block; margin-bottom: 2px;">SpO2 (%)</label>
                                <p style="font-size: 14px; font-weight: 700; margin: 0; color: var(--text-dark);" id="viewTriageSpO2">---</p>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- Acuity Section -->
                <div style="margin-bottom: 25px;">
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-shield-check"></i> Urgency Level
                    </h4>
                    <div id="viewTriageLevelContainer" style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border-radius: 8px; border: 2px solid var(--border-color); background: var(--bg);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div id="viewTriageLevelIconContainer" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background: #fff; box-shadow: var(--shadow-sm);">
                                <i id="viewTriageLevelIcon" class="bi bi-speedometer2"></i>
                            </div>
                            <div>
                                <p style="font-size: 12px; font-weight: 600; color: var(--text-light); margin: 0 0 2px 0;">Assigned Acuity</p>
                                <p style="font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;" id="viewTriageLevel">---</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div>
                    <h4 style="font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-chat-left-text"></i> Nurse Observations
                    </h4>
                    <div style="padding: 15px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg); min-height: 80px;">
                        <p style="font-size: 14px; color: var(--text-dark); margin: 0; line-height: 1.5; font-style: italic;" id="viewTriageNotes">No notes recorded.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding: 0 25px 25px 25px; display: flex; justify-content: flex-end;">
                <button type="button" class="core1-btn core1-btn-primary" style="width: 100%; font-size: 14px; padding: 12px; border-radius: 8px;" onclick="closeModal('viewTriageModal')">
                    Done Reviewing
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEncounterId = null;

function switchTab(evt, tabId) {
    const tabPanes = document.getElementsByClassName('core1-tab-pane');
    for (let i = 0; i < tabPanes.length; i++) {
        tabPanes[i].classList.remove('active');
    }
    const tabBtns = document.getElementsByClassName('core1-tab-btn');
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove('active');
    }
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function toggleSendToAdmissionBtn() {
    const level = document.getElementById('triageLevelSelect').value;
    const btn = document.getElementById('sendToAdmissionBtn');
    if (['1', '2', '3'].includes(level)) {
        btn.style.display = 'block';
    } else {
        btn.style.display = 'none';
    }
}

function submitTriageForAdmission() {
    document.getElementById('sendToAdmissionFlag').value = '1';
    document.getElementById('triageForm').submit();
}

function submitAdmissionAjax(event) {
    event.preventDefault();
    
    const form = document.getElementById('admissionForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerText;
    
    submitBtn.innerText = 'Admitting...';
    submitBtn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            closeModal('admissionModal');
            window.location.reload();
        } else {
            alert(data.message || 'Error occurred during admission');
        }
    })
    .catch(error => {
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const msg = document.getElementById('availability-msg');

    function checkAvailability() {
        if (!doctorSelect || !dateInput) return;
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            if(timeSelect) timeSelect.innerHTML = '<option value="">Select Date & Doctor First</option>';
            return;
        }

        if(msg) msg.textContent = 'Checking availability...';
        if(timeSelect) timeSelect.disabled = true;

        fetch(`{{ route('core1.appointments.check-availability') }}?doctor_id=${doctorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if(timeSelect) timeSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        if(timeSelect){
                            const option = document.createElement('option');
                            option.value = slot.time;
                            option.textContent = `${slot.time} (${slot.status})`;
                            if (slot.status === 'booked') {
                                option.disabled = true;
                                option.classList.add('bg-gray-100', 'text-gray-400');
                            }
                            timeSelect.appendChild(option);
                        }
                    });
                    if(msg) msg.textContent = 'Slots updated.';
                } else {
                    if(timeSelect) timeSelect.innerHTML = '<option value="">No slots available</option>';
                    if(msg) msg.textContent = 'No slots available for this date.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if(msg) msg.textContent = 'Error checking availability.';
            })
            .finally(() => {
                if(timeSelect) timeSelect.disabled = false;
            });
    }

    if(doctorSelect && dateInput) {
        doctorSelect.addEventListener('change', checkAvailability);
        dateInput.addEventListener('change', checkAvailability);
        
        // Check initial state if old values exist
        if (doctorSelect.value && dateInput.value) {
            checkAvailability();
        }
    }
});


function openAdmissionModal(encounterId) {
    document.getElementById('admissionEncounterId').value = encounterId;

    // Reset bed picker state
    document.getElementById('selectedBedId').value = '';
    document.getElementById('bpSelectionBar').textContent = 'No bed selected — click an available bed above';
    const admitBtn = document.getElementById('admitPatientBtn');
    admitBtn.disabled = true;
    admitBtn.style.opacity = '0.5';

    // Clear any previously selected bed highlight
    document.querySelectorAll('.bp-bed').forEach(b => {
        b.style.borderColor = b.classList.contains('bp-bed-available') ? '#86efac'
            : b.classList.contains('bp-bed-occupied') ? '#fca5a5' : '#fcd34d';
        b.style.background = b.classList.contains('bp-bed-available') ? '#f0fff4'
            : b.classList.contains('bp-bed-occupied') ? '#fff5f5' : '#fffbeb';
        const check = b.querySelector('.bp-bed-check');
        if (check) check.style.display = 'none';
    });

    document.getElementById('admissionModal').style.display = 'flex';
}

function selectBed(el) {
    // Deselect all beds first
    document.querySelectorAll('.bp-bed.bp-bed-available').forEach(b => {
        b.style.borderColor = '#86efac';
        b.style.background = '#f0fff4';
        b.style.boxShadow = 'none';
        const check = b.querySelector('.bp-bed-check');
        if (check) check.style.display = 'none';
    });

    // Highlight selected bed
    el.style.borderColor = '#2563eb';
    el.style.background = '#eff6ff';
    el.style.boxShadow = '0 0 0 3px rgba(37,99,235,0.2)';
    const check = el.querySelector('.bp-bed-check');
    if (check) { check.style.display = 'flex'; }

    // Set hidden input and update selection bar
    const bedId = el.getAttribute('data-bed-id');
    const bedLabel = el.getAttribute('data-bed-label');
    document.getElementById('selectedBedId').value = bedId;
    document.getElementById('bpSelectionBar').innerHTML =
        `<i class="bi bi-check-circle-fill" style="color:#2563eb; margin-right:6px;"></i><strong style="color:var(--text-dark);">Selected:</strong> <span style="color:#2563eb; font-style:normal;">${bedLabel}</span>`;

    // Enable admit button
    const admitBtn = document.getElementById('admitPatientBtn');
    admitBtn.disabled = false;
    admitBtn.style.opacity = '1';
}

function switchBpZone(zone) {
    // Hide all zone panels
    document.querySelectorAll('.bp-zone-panel').forEach(p => p.style.display = 'none');
    // Show selected zone panel
    const panel = document.getElementById('bp-zone-' + zone);
    if (panel) panel.style.display = '';

    // Update zone tab styles
    document.querySelectorAll('.bp-zone-tab').forEach(tab => {
        const tabZone = tab.getAttribute('data-zone');
        const color = tab.getAttribute('data-color');
        if (tabZone === zone) {
            tab.style.color = color;
            tab.style.borderBottomColor = color;
            tab.style.background = 'white';
        } else {
            tab.style.color = 'var(--text-gray)';
            tab.style.borderBottomColor = 'transparent';
            tab.style.background = 'none';
        }
    });
}

function openTriageModal(id) {
    const form = document.getElementById('triageForm');
    form.action = `/core/outpatient/${id}/triage`;
    document.getElementById('triageLevelSelect').value = '5';
    document.getElementById('sendToAdmissionFlag').value = '0';
    toggleSendToAdmissionBtn();
    document.getElementById('triageModal').style.display = 'flex';
}

function openConsultationModal(id, name) {
    currentEncounterId = id;
    const form = document.getElementById('consultationForm');
    form.action = `/core/outpatient/${id}/consultation`;
    document.getElementById('consultingPatientName').innerText = name;
    
    // Disposition logic
    const dischargeBtn = document.getElementById('dischargeBtn');
    const admitBtn = document.getElementById('admitBtn');
    
    const submitDisposition = function(type) {
        const actionText = type === 'discharge' ? 'discharge this patient home' : 'recommend admission for this patient';
        if(confirm(`Are you sure you want to ${actionText}?`)) {
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = `/core/outpatient/${id}/complete`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            tempForm.appendChild(csrf);
            
            const dispInput = document.createElement('input');
            dispInput.type = 'hidden'; dispInput.name = 'disposition'; dispInput.value = type;
            tempForm.appendChild(dispInput);
            
            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    };

    dischargeBtn.onclick = () => submitDisposition('discharge');
    admitBtn.onclick = () => submitDisposition('admit');

    document.getElementById('consultationModal').style.display = 'flex';
}

function openLabModal() {
    if (!currentEncounterId) return;
    document.getElementById('labEncounterId').value = currentEncounterId;
    document.getElementById('labModal').style.display = 'flex';
}

function openResultsModal(btn) {
    document.getElementById('resultTestName').innerText = btn.getAttribute('data-test') || '---';
    document.getElementById('resultPatientName').innerText = btn.getAttribute('data-patient') || '---';
    document.getElementById('resultReceivedAt').innerText = btn.getAttribute('data-received') || '---';

    let resultRaw = btn.getAttribute('data-result') || '';
    const tbody = document.getElementById('resultDataContent');
    tbody.innerHTML = ''; // Clear previous

    try {
        let parsed = JSON.parse(resultRaw);
        for (const [key, value] of Object.entries(parsed)) {
            // Format key (e.g., "specific_gravity" -> "Specific Gravity")
            const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            
            const tr = document.createElement('tr');
            tr.className = 'border-b border-slate-200 last:border-0 hover:bg-slate-100/50';
            
            const tdKey = document.createElement('td');
            tdKey.className = 'px-4 py-3 text-xs font-bold text-slate-500 w-1/3 bg-slate-50/50';
            tdKey.textContent = formattedKey;
            
            const tdVal = document.createElement('td');
            tdVal.className = 'px-4 py-3 text-sm font-bold text-slate-900';
            tdVal.textContent = value;
            
            tr.appendChild(tdKey);
            tr.appendChild(tdVal);
            tbody.appendChild(tr);
        }
    } catch(e) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.className = 'px-4 py-3 text-sm text-slate-800 whitespace-pre-wrap';
        td.colSpan = 2;
        td.textContent = resultRaw;
        tr.appendChild(td);
        tbody.appendChild(tr);
    }

    document.getElementById('labResultsModal').style.display = 'flex';
}

function openPrescriptionModal() {
    if (!currentEncounterId) return;
    document.getElementById('rxEncounterId').value = currentEncounterId;
    document.getElementById('prescriptionModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function openViewTriageModal(btn) {
    document.getElementById('viewTriageBP').innerText = btn.getAttribute('data-bp') || '---';
    document.getElementById('viewTriageHR').innerText = btn.getAttribute('data-hr') ? btn.getAttribute('data-hr') + ' bpm' : '---';
    document.getElementById('viewTriageTemp').innerText = btn.getAttribute('data-temp') ? btn.getAttribute('data-temp') + ' °C' : '---';
    document.getElementById('viewTriageSpO2').innerText = btn.getAttribute('data-spo2') ? btn.getAttribute('data-spo2') + ' %' : '---';
    
    // Core1 colors: danger, warning, success, info
    const levels = {
        '1': { text: 'Level 1 - Resuscitation', color: '#dc2626', bg: '#fee2e2', border: '#fca5a5', icon: 'bi-exclamation-octagon-fill' },
        '2': { text: 'Level 2 - Emergent', color: '#ea580c', bg: '#ffedd5', border: '#fdba74', icon: 'bi-lightning-charge-fill' },
        '3': { text: 'Level 3 - Urgent', color: '#d97706', bg: '#fef3c7', border: '#fcd34d', icon: 'bi-exclamation-triangle-fill' },
        '4': { text: 'Level 4 - Less Urgent', color: '#16a34a', bg: '#dcfce7', border: '#86efac', icon: 'bi-check-circle-fill' },
        '5': { text: 'Level 5 - Non-Urgent', color: '#3b82f6', bg: '#dbeafe', border: '#93c5fd', icon: 'bi-info-circle-fill' }
    };
    
    const levelKey = btn.getAttribute('data-level');
    const levelData = levels[levelKey] || { text: 'Not Assigned', color: '#6b7280', bg: '#f7f8fa', border: '#e5e7eb', icon: 'bi-dash-circle' };
    
    const levelText = document.getElementById('viewTriageLevel');
    const levelContainer = document.getElementById('viewTriageLevelContainer');
    const levelIcon = document.getElementById('viewTriageLevelIcon');
    // We already styled the icon container using generic drop shadow and white background.
    
    levelText.innerText = levelData.text;
    levelText.style.color = levelData.color;
    levelContainer.style.backgroundColor = levelData.bg;
    levelContainer.style.borderColor = levelData.border; 
    
    levelIcon.className = `bi ${levelData.icon}`;
    levelIcon.style.color = levelData.color;
    
    const rawNotes = btn.getAttribute('data-notes');
    document.getElementById('viewTriageNotes').innerText = (rawNotes && rawNotes !== 'null' && rawNotes !== '') ? rawNotes : 'No nurse observations reported for this encounter.';
    
    document.getElementById('viewTriageModal').style.display = 'flex';
}


function openPatientModal(id) {
    document.getElementById('modalPatientName').innerText = 'Loading...';
    document.getElementById('patientDetailsModal').style.display = 'flex';

    fetch(`/core/patients/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const p = data.patient;
        document.getElementById('modalPatientName').innerText = p.first_name + ' ' + (p.middle_name ? p.middle_name + ' ' : '') + p.last_name;
        document.getElementById('modalPatientMRN').innerText = 'MRN: ' + (p.mrn || 'Not assigned');
        document.getElementById('modalDOB').innerText = p.date_of_birth ? new Date(p.date_of_birth).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '---';
        document.getElementById('modalAge').innerText = data.age + ' years';
        document.getElementById('modalGender').innerText = p.gender ? p.gender.charAt(0).toUpperCase() + p.gender.slice(1) : '---';
        document.getElementById('modalPhone').innerText = p.phone || '---';
        document.getElementById('modalEmail').innerText = p.email || '---';
        document.getElementById('modalAddress').innerText = p.address || '---';
        
        document.getElementById('modalBloodType').innerText = p.blood_type || '---';
        document.getElementById('modalAllergies').innerText = p.allergies || 'None';
        document.getElementById('modalHistory').innerText = p.medical_history || 'None';
        
        document.getElementById('modalECName').innerText = p.emergency_contact_name || '---';
        document.getElementById('modalECPhone').innerText = p.emergency_contact_phone || '---';
        document.getElementById('modalECRelation').innerText = p.emergency_contact_relation || '---';
        
        document.getElementById('modalInsurance').innerText = p.insurance_provider || '---';
        document.getElementById('modalPolicy').innerText = p.policy_number || '---';
    })
    .catch(error => {
        console.error('Error fetching patient details:', error);
        document.getElementById('modalPatientName').innerText = 'Error loading details';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    closeModal('triageModal');
    closeModal('consultationModal');
    closeModal('labModal');
    closeModal('prescriptionModal');
    closeModal('patientDetailsModal');
    closeModal('viewTriageModal');
});

// ── Medication Search (Autocomplete) ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('medicationSearch');
    const resultsContainer = document.getElementById('drugSearchResults');
    let debounceTimer;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                resultsContainer.innerHTML = '<div style="padding: 10px; color: var(--text-gray); font-style: italic;"><i class="bi bi-arrow-repeat core1-spin"></i> Searching inventory...</div>';
                resultsContainer.style.display = 'block';

                fetch(`/api/pharmacy-sync/search-drugs?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(drug => {
                                const div = document.createElement('div');
                                div.style.padding = '10px 14px';
                                div.style.cursor = 'pointer';
                                div.style.borderBottom = '1px solid var(--border-color)';
                                div.className = 'hover:bg-slate-50';
                                div.innerHTML = `
                                    <div class="font-bold text-sm" style="color: var(--text-dark);">${drug.drug_name}</div>
                                    <div style="font-size: 11px; color: var(--text-gray); margin-top: 2px;">
                                        <span style="background: var(--info-light); color: var(--info); padding: 1px 6px; border-radius: 4px; font-weight: 700; margin-right: 6px;">
                                            Stock: ${drug.quantity}
                                        </span>
                                        <span style="opacity: 0.7;">Code: ${drug.drug_num}</span>
                                    </div>
                                `;
                                div.onclick = () => {
                                    searchInput.value = drug.drug_name;
                                    resultsContainer.style.display = 'none';
                                };
                                resultsContainer.appendChild(div);
                            });
                            resultsContainer.style.display = 'block';
                        } else {
                            resultsContainer.innerHTML = '<div style="padding: 12px; color: var(--text-gray); font-style: italic; text-align: center;">No matching medicine found.</div>';
                        }
                    })
                    .catch(err => {
                        console.error('Drug search failed:', err);
                        resultsContainer.innerHTML = '<div style="padding: 12px; color: var(--danger); font-size: 11px;">Search failed. Try again.</div>';
                    });
            }, 400);
        });

        // Close results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    }
});
</script>

@if(session('open_admission_modal'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        openAdmissionModal({{ session('open_admission_modal') }});
    });
</script>
@endif
@endsection
