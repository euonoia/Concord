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
            <button class="core1-tab-btn" onclick="switchTab(event, 'prescription-recording')">
                <i class="bi bi-capsule mr-5"></i> Prescription & Treatment
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'diagnostic-orders')">
                <i class="bi bi-clipboard-pulse mr-5"></i> Diagnostic Orders
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
                                <th class="text-right">ACTIONS</th>
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
                            <td class="text-right">
                                <div class="core1-flex-gap-2 justify-end">

                                    <button class="core1-btn-sm core1-btn-primary" 
                                            onclick="openConsultationModal({{ $apt['id'] }}, '{{ $apt['patient'] }}')">
                                        <i class="bi bi-chat-left-dots"></i> Consult
                                    </button>
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
                                <th class="text-right">ACTION</th>
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
                                    <td class="text-right d-flex gap-2 justify-end">
                                        <button class="core1-btn-sm core1-btn-outline" 
                                                onclick="openTriageModal({{ $reg['id'] }})">
                                            <i class="bi bi-heart-pulse"></i> Triage
                                        </button>

                                        @if($reg['status'] === 'Triaged' && $reg['type'] === 'Pending')
                                            <form action="{{ route('core1.outpatient.disposition', $reg['id']) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="type" value="OPD">
                                                <button type="submit" class="core1-btn-sm core1-btn-primary" title="Send to Outpatient">
                                                    <i class="bi bi-person-walking"></i> OPD
                                                </button>
                                            </form>

                                            <form action="{{ route('core1.outpatient.disposition', $reg['id']) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="type" value="IPD">
                                                <button type="submit" class="core1-btn-sm core1-btn-outline" title="Admit to Inpatient">
                                                    <i class="bi bi-hospital"></i> IPD
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
                                <th>DOSAGE</th>
                                <th>INSTRUCTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescriptions as $rx)
                                <tr>
                                    <td>{{ $rx->created_at->format('Y-m-d') }}</td>
                                    <td class="font-bold text-blue">{{ $rx->encounter->patient->name ?? 'Unknown' }}</td>
                                    <td class="font-bold">{{ $rx->medication }}</td>
                                    <td>{{ $rx->dosage }}</td>
                                    <td class="text-xs text-gray">{{ $rx->instructions }}</td>
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
                                <th>TEST</th>
                                <th>INDICATION</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diagnosticOrders as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="font-bold text-blue">{{ $order->encounter->patient->name ?? 'Unknown' }}</td>
                                    <td class="font-bold">{{ $order->test_name }}</td>
                                    <td class="text-xs">{{ $order->clinical_note }}</td>
                                    <td>
                                        <span class="core1-status-tag core1-tag-neutral">Ordered</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Follow Up Tab -->
            <div id="follow-up" class="core1-tab-pane">
                <div class="core1-flex-center py-50">
                    <div class="text-center">
                        <i class="bi bi-calendar2-week text-gray mb-20" style="font-size: 3rem;"></i>
                        <p class="text-gray italic">Follow-up scheduling is integrated into the Consultation Room flow.</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Generic Triage Modal -->
    <div id="triageModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; display:flex; align-items:center; justify-content:center;">
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
                    <select name="triage_level" class="core1-input w-full">
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
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('triageModal')">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-primary">Save Triage</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Generic Consultation Modal (SOAP) -->
    <div id="consultationModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; display:flex; align-items:center; justify-content:center;">
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
                    </div>
                </div>

                <div class="core1-flex-gap-2 justify-between pt-10 border-top">
                    <button type="button" class="core1-btn core1-btn-outline" onclick="closeModal('consultationModal')">Pause</button>
                    <div class="core1-flex-gap-2">
                        <button type="submit" class="core1-btn core1-btn-primary">Save Notes</button>
                        <button type="button" class="core1-btn core1-btn-success" id="completeBtn">Complete & Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lab Order Modal -->
    <div id="labModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:200; display:flex; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
            <h4 class="font-bold mb-15">Order Laboratory Test</h4>
            <form method="POST" action="{{ route('core1.outpatient.storeLabOrder') }}">
                @csrf
                <input type="hidden" name="encounter_id" id="labEncounterId">
                <div class="mb-10">
                    <label class="font-bold block mb-5">Test Name</label>
                    <input type="text" name="test_name" class="core1-input w-full" required placeholder="e.g. Complete Blood Count (CBC)">
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

    <!-- Prescription Modal -->
    <div id="prescriptionModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:200; display:flex; align-items:center; justify-content:center;">
        <div class="core1-modal-content core1-card" style="width:400px; max-width:90%;">
            <h4 class="font-bold mb-15">Issue e-Prescription</h4>
            <form method="POST" action="{{ route('core1.outpatient.storePrescription') }}">
                @csrf
                <input type="hidden" name="encounter_id" id="rxEncounterId">
                <div class="mb-10">
                    <label class="font-bold block mb-5">Medication Name</label>
                    <input type="text" name="medication" class="core1-input w-full" required placeholder="e.g. Amoxicillin 500mg">
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

function openTriageModal(id) {
    const form = document.getElementById('triageForm');
    form.action = `/core/outpatient/${id}/triage`;
    document.getElementById('triageModal').style.display = 'flex';
}

function openConsultationModal(id, name) {
    currentEncounterId = id;
    const form = document.getElementById('consultationForm');
    form.action = `/core/outpatient/${id}/consultation`;
    document.getElementById('consultingPatientName').innerText = name;
    
    // Complete logic
    const completeBtn = document.getElementById('completeBtn');
    completeBtn.onclick = function() {
        if(confirm('Are you sure you want to close this encounter?')) {
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = `/core/outpatient/${id}/complete`;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            tempForm.appendChild(csrf);
            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    };

    document.getElementById('consultationModal').style.display = 'flex';
}

function openLabModal() {
    if (!currentEncounterId) return;
    document.getElementById('labEncounterId').value = currentEncounterId;
    document.getElementById('labModal').style.display = 'flex';
}

function openPrescriptionModal() {
    if (!currentEncounterId) return;
    document.getElementById('rxEncounterId').value = currentEncounterId;
    document.getElementById('prescriptionModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    closeModal('triageModal');
    closeModal('consultationModal');
    closeModal('labModal');
    closeModal('prescriptionModal');
});
</script>
@endsection
