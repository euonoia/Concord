@extends('layouts.core1.layouts.app')

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
            <button class="core1-tab-btn active" onclick="switchTab(event, 'consultation-tracking')">
                <i class="bi bi-activity mr-5"></i> Consultation Tracking
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'arrival-logs')">
                <i class="bi bi-journal-check mr-5"></i> Arrival Logs & Triage
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
            <div id="consultation-tracking" class="core1-tab-pane active">
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
    @if($apt['status'] === 'Consulted')

        <span class="core1-status-tag core1-tag-stable">DONE</span>
    @else
        @if(in_array(auth()->user()->role, ['doctor','admin','nurse']))
            <form method="POST"
                  action="{{ route('core1.outpatient.updateStatus', $apt['id']) }}"
                  class="d-inline">
                @csrf
                <select name="status"
                        onchange="this.form.submit()"
                        class="core1-btn-sm core1-btn-primary">
                    <option disabled selected>EDIT</option>
                    <option value="waiting">Waiting</option>
                    <option value="in_consultation">In Consultation</option>
                    <option value="consulted">Consulted</option>
                </select>
            </form>
        @else
            <button class="core1-btn-sm core1-btn-primary" disabled>EDIT</button>
        @endif
    @endif
</td>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arrival Logs & Triage Tab -->
            <div id="arrival-logs" class="core1-tab-pane">
                <h3 class="mb-20 text-sm font-bold">Patient Arrival & Triage Summary</h3>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>ARRIVAL</th>
                                <th>PATIENT</th>
                                <th>TRIAGE NOTE / VITALS</th>
                                <th>STATUS</th>
                                <th class="text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($registrations as $reg)
<tr>
    <td>{{ $reg['date'] }}</td>

    <td class="font-bold text-blue">
        {{ $reg['patient'] }}
    </td>

    <td>
        {{ $reg['triage'] }}
    </td>

    <td>
        @php
            if($reg['status'] == 'Emergency'){
                $statusClass = 'core1-tag-critical';
            } elseif($reg['status'] == 'Triaged'){
                $statusClass = 'core1-tag-stable';
            } else {
                $statusClass = 'core1-tag-stable';
            }
        @endphp

        <span class="core1-status-tag {{ $statusClass }}">
            {{ $reg['status'] }}
        </span>
    </td>

    <td class="text-right">

        @if($reg['canAction'])
            <button class="core1-btn-sm core1-btn-outline"
                onclick="document.getElementById('triageModal{{ $reg['id'] }}').style.display='block'">
                Review Vitals
            </button>
        @else
            <button class="core1-btn-sm core1-btn-outline" disabled>
                Review Vitals
            </button>
        @endif

        <!-- Modal -->
        @if($reg['canAction'])
        <div id="triageModal{{ $reg['id'] }}" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
            <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
                <h4>Triage Form</h4>

                <form method="POST"
                      action="{{ route('core1.outpatient.saveTriage',$reg['id']) }}">
                    @csrf

                    <div class="mb-10">
                        <label>Triage Note</label>
                        <select name="triage_note" class="core1-input w-100">
                            <option value="Stable">Stable</option>
                            <option value="Critical">Critical</option>
                            <option value="Under Observation">Under Observation</option>
                            <option value="Requires Immediate Care">Requires Immediate Care</option>
                        </select>
                    </div>

                    <div class="mb-10">
                        <label>Blood Pressure</label>
                        <select name="vital_signs" class="core1-input w-100">
                            <option value="120/80">120/80</option>
                            <option value="130/85">130/85</option>
                            <option value="140/90">140/90</option>
                            <option value="150/95">150/95</option>
                        </select>
                    </div>

                    <button type="submit" class="core1-btn core1-btn-primary">
                        Submit
                    </button>

                    <button type="button"
                        class="core1-btn core1-btn-outline"
                        onclick="document.getElementById('triageModal{{ $reg['id'] }}').style.display='none'">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
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

        @if(in_array(auth()->user()->role, ['doctor','nurse']))
            <button class="core1-btn core1-btn-primary"
                onclick="document.getElementById('newPrescriptionModal').style.display='block'">
                <i class="bi bi-pencil-square"></i> New e-Prescription
            </button>
        @endif
    </div>

    <div class="core1-table-container shadow-none border">
        <table class="core1-table">
            <thead>
                <tr>
                    <th>PATIENT</th>
                    <th>MEDICATION</th>
                    <th>DOSAGE</th>
                    <th>INSTRUCTIONS</th>
                    <th class="text-right">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $rx)
                    @php
    $rxData = json_decode($rx->prescription, true);
    $patient = \App\Models\core1\Patient::find($rx->patient_id); // fetch dynamically
@endphp
<td class="font-bold text-blue">{{ $patient->name ?? 'Unknown' }} ({{ $patient->patient_id ?? '' }})</td>

                        <td>{{ $rxData['medication'] ?? '' }}</td>
                        <td>{{ $rxData['dosage'] ?? '' }}</td>
                        <td class="text-xs">{{ $rxData['instructions'] ?? '' }}</td>
                        <td class="text-right">
                            @if(in_array(auth()->user()->role, ['doctor','nurse']))
                                <button class="core1-btn-sm core1-btn-outline"
                                    onclick="document.getElementById('editPrescriptionModal{{ $rx->id }}').style.display='block'">
                                    Edit
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit Prescription Modal (Styled like New Prescription) -->
                    @if(in_array(auth()->user()->role, ['doctor','nurse']))
                    <div id="editPrescriptionModal{{ $rx->id }}" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
                        <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
                            <h4>Edit Prescription</h4>
                            <form method="POST" action="{{ route('core1.outpatient.updatePrescription', $rx->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-10">
                                    <label>Patient</label>
                                    <input type="text" class="core1-input w-100" value="{{ $patient->name ?? 'Unknown' }}" disabled>
                                </div>

                                <div class="mb-10">
                                    <label>Medication</label>
                                    <select name="medication" class="core1-input w-100">
                                        <option value="Atovastatin 40mg" {{ ($rxData['medication'] ?? '') == 'Atovastatin 40mg' ? 'selected' : '' }}>Atovastatin 40mg</option>
                                        <option value="Metformin 500mg" {{ ($rxData['medication'] ?? '') == 'Metformin 500mg' ? 'selected' : '' }}>Metformin 500mg</option>
                                        <option value="Amlodipine 5mg" {{ ($rxData['medication'] ?? '') == 'Amlodipine 5mg' ? 'selected' : '' }}>Amlodipine 5mg</option>
                                        <option value="Lisinopril 10mg" {{ ($rxData['medication'] ?? '') == 'Lisinopril 10mg' ? 'selected' : '' }}>Lisinopril 10mg</option>
                                    </select>
                                </div>

                                <div class="mb-10">
                                    <label>Dosage</label>
                                    <select name="dosage" class="core1-input w-100">
                                        <option value="Once daily (Night)" {{ ($rxData['dosage'] ?? '') == 'Once daily (Night)' ? 'selected' : '' }}>Once daily (Night)</option>
                                        <option value="Twice daily" {{ ($rxData['dosage'] ?? '') == 'Twice daily' ? 'selected' : '' }}>Twice daily</option>
                                        <option value="Thrice daily" {{ ($rxData['dosage'] ?? '') == 'Thrice daily' ? 'selected' : '' }}>Thrice daily</option>
                                    </select>
                                </div>

                                <div class="mb-10">
                                    <label>Instructions</label>
                                    <input type="text" name="instruction" class="core1-input w-100" value="{{ $rxData['instructions'] ?? '' }}">
                                </div>

                                <button type="submit" class="core1-btn core1-btn-primary">Save Changes</button>
                                <button type="button" class="core1-btn core1-btn-outline"
                                    onclick="document.getElementById('editPrescriptionModal{{ $rx->id }}').style.display='none'">
                                    Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                @endforeach
            </tbody>
        </table>
    </div>

    <!-- New Prescription Modal -->
    @if(in_array(auth()->user()->role, ['doctor','nurse']))
    <div id="newPrescriptionModal" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
        <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
            <h4>New Prescription</h4>
            <form method="POST" action="{{ route('core1.outpatient.storePrescription') }}">
                @csrf

                <div class="mb-10">
                    <label>Patient</label>
                    <select name="patient_id" class="core1-input w-100">
                        @foreach($patients as $p)
                            @php
                                $alreadyHasRx = $prescriptions->where('patient_id', $p->id)->count() > 0;
                            @endphp
                            <option value="{{ $p->id }}" {{ $alreadyHasRx ? 'disabled' : '' }}>
                                {{ $p->name }} ({{ $p->patient_id }}) {{ $alreadyHasRx ? '- Already has Rx' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-10">
                    <label>Medication</label>
                    <select name="medication" class="core1-input w-100">
                        <option value="Atovastatin 40mg">Atovastatin 40mg</option>
                        <option value="Metformin 500mg">Metformin 500mg</option>
                        <option value="Amlodipine 5mg">Amlodipine 5mg</option>
                        <option value="Lisinopril 10mg">Lisinopril 10mg</option>
                    </select>
                </div>

                <div class="mb-10">
                    <label>Dosage</label>
                    <select name="dosage" class="core1-input w-100">
                        <option value="Once daily (Night)">Once daily (Night)</option>
                        <option value="Twice daily">Twice daily</option>
                        <option value="Thrice daily">Thrice daily</option>
                    </select>
                </div>

                <div class="mb-10">
                    <label>Instructions</label>
                    <input type="text" name="instruction" class="core1-input w-100">
                </div>

                <button type="submit" class="core1-btn core1-btn-primary">Submit</button>
                <button type="button" class="core1-btn core1-btn-outline"
                    onclick="document.getElementById('newPrescriptionModal').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>
    @endif
</div>



           <!-- Diagnostic Orders Tab -->
<div id="diagnostic-orders" class="core1-tab-pane">
    <div class="d-flex justify-between items-center mb-20">
        <h3 class="core1-title core1-section-title">Laboratory & Diagnostic Management</h3>

       @if(in_array(auth()->user()->role, ['doctor','nurse']))
            <button class="core1-btn core1-btn-primary"
                onclick="document.getElementById('labOrderModal').style.display='block'">
                <i class="bi bi-plus-circle"></i> Create Lab Order
            </button>
        @endif
    </div>

    <div class="core1-table-container shadow-none border">
        <table class="core1-table">
            <thead>
                <tr>
                    <th>PATIENT</th>
                    <th>ORDERED TEST</th>
                    <th>CLINICAL INDICATION</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($diagnosticOrders as $order)
                    <tr>
                        <td class="font-bold text-blue">{{ $order['patient'] }}</td>
                        <td>{{ $order['test'] }}</td>
                        <td>{{ $order['clinical_note'] }}</td>
                        <td>
                            <span class="core1-status-tag {{ $order['status'] == 'Ordered' ? 'core1-tag-cleaning' : 'core1-tag-stable' }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                        <td class="text-right">
                            @if($order['status'] == 'Result ready')
                                <button class="core1-btn-sm core1-btn-primary" disabled>
                                    Review Result
                                </button>
                            @else
                                <button class="core1-btn-sm core1-btn-outline" disabled>
                                    Track Order
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CREATE LAB ORDER MODAL --}}
    @if(in_array(auth()->user()->role, ['doctor','admin']))
    <div id="labOrderModal" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
        <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
            <h4>Create Laboratory Order</h4>

            <form method="POST" action="{{ route('core1.outpatient.storeLabOrder') }}">
                @csrf

                {{-- Only Outpatients and Disable if Already Has Lab Order --}}
                <div class="mb-10">
                    <label>Patient</label>
                    <select name="patient_id" class="core1-input w-100">
                        @foreach($patients as $p)
                            @php
                              $alreadyOrdered = collect($diagnosticOrders)
    ->where('patient_id', $p->id)
    ->count() > 0;
                            @endphp
                            <option value="{{ $p->id }}" {{ $alreadyOrdered ? 'disabled' : '' }}>
                                {{ $p->name }} ({{ $p->patient_id }})
                                {{ $alreadyOrdered ? '- Already Ordered' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-10">
                    <label>Ordered Test</label>
                    <select name="test" class="core1-input w-100">
                        <option value="Lipid Profile Comprehensive Panel">Lipid Profile Comprehensive Panel</option>
                        <option value="Electrocardiogram Cardiac Rhythm Monitoring">Electrocardiogram Cardiac Rhythm Monitoring</option>
                        <option value="Complete Blood Count Hematology Panel">Complete Blood Count Hematology Panel</option>
                        <option value="Chest Xray Pulmonary Evaluation Study">Chest Xray Pulmonary Evaluation Study</option>
                        <option value="Fasting Blood Sugar Metabolic Assessment">Fasting Blood Sugar Metabolic Assessment</option>
                    </select>
                </div>

                <div class="mb-10">
                    <label>Clinical Indication</label>
                    <select name="clinical_note" class="core1-input w-100">
                        <option value="Suspected Hyperlipidemia Cardiovascular Risk Evaluation">Suspected Hyperlipidemia Cardiovascular Risk Evaluation</option>
                        <option value="Routine Follow Up Chronic Disease Monitoring">Routine Follow Up Chronic Disease Monitoring</option>
                        <option value="Chest Pain Rule Out Cardiac Ischemia">Chest Pain Rule Out Cardiac Ischemia</option>
                        <option value="Uncontrolled Hypertension Further Diagnostic Workup">Uncontrolled Hypertension Further Diagnostic Workup</option>
                        <option value="Pre Employment Medical Clearance Requirement">Pre Employment Medical Clearance Requirement</option>
                    </select>
                </div>

                <button type="submit" class="core1-btn core1-btn-primary">
                    Submit
                </button>

                <button type="button"
                    class="core1-btn core1-btn-outline"
                    onclick="document.getElementById('labOrderModal').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>
    @endif
</div>


          <!-- Follow Up Tab -->
<div id="follow-up" class="core1-tab-pane">
    <div class="d-flex justify-between items-center mb-20">
        <h3 class="core1-title core1-section-title">Planned Follow-up Visits</h3>

        @if(in_array(auth()->user()->role, ['doctor','nurse']))
            <button class="core1-btn core1-btn-primary"
                onclick="document.getElementById('newFollowUpModal').style.display='block'">
                <i class="bi bi-plus-circle"></i> Schedule Follow-Up
            </button>
        @endif
    </div>

    <div class="core1-table-container shadow-none border">
        <table class="core1-table">
            <thead>
                <tr>
                    <th>TIMEFRAME</th>
                    <th>PATIENT</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @foreach($followUps as $fu)
                    <tr>
                        <td class="font-bold">{{ $fu['next_visit'] }}</td>
                        <td class="font-bold text-blue">{{ $fu['patient'] }}</td>
                        <td>
    @php
    $statusClass = $fu['status'] == 'scheduled' ? 'core1-tag-success' : 'core1-tag-stable';
@endphp
<span class="core1-status-tag {{ $statusClass }}">
    {{ ucfirst($fu['status']) }}
</span>

</td>

                        <td class="text-right">
    @if(in_array(auth()->user()->role, ['doctor','nurse']))
        <button class="core1-btn-sm core1-btn-outline"
            onclick="document.getElementById('editFollowUpModal{{ $fu['id'] }}').style.display='block'">
            Modify Instructions
        </button>

        <!-- Modal -->
        <div id="editFollowUpModal{{ $fu['id'] }}" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
            <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
                <h4>Modify Follow-Up Date</h4>
                <form method="POST" action="{{ route('core1.outpatient.updateFollowUp', $fu['id']) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-10">
                        <label>Next Visit Date</label>
                        <input type="date" name="next_visit" class="core1-input w-100" value="{{ $fu['next_visit'] }}" required>
                    </div>

                    <button type="submit" class="core1-btn core1-btn-primary">Save Changes</button>
                    <button type="button" class="core1-btn core1-btn-outline"
                        onclick="document.getElementById('editFollowUpModal{{ $fu['id'] }}').style.display='none'">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    @endif
</td>

                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- New Follow-Up Modal -->
    @if(in_array(auth()->user()->role, ['doctor','nurse']))
    <div id="newFollowUpModal" style="display:none; background:#00000066; position:fixed; top:0; left:0; width:100%; height:100%;">
        <div style="background:white; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
            <h4>Schedule Follow-Up</h4>
            <form method="POST" action="{{ route('core1.outpatient.storeFollowUp') }}">
                @csrf

                <div class="mb-10">
                    <label>Patient</label>
                    <select name="patient_id" class="core1-input w-100">
           @foreach($patients as $p)
    @php
        $consulted = $appointments->where('patient', $p->name)
            ->where('status', 'Consulted')
            ->count() > 0;

        $alreadyScheduled = $followUps->where('patient_id', $p->id)
            ->where(fn($fu) => Str::lower($fu['status']) === 'scheduled')
            ->count() > 0;
    @endphp
    <option value="{{ $p->id }}" {{ !$consulted || $alreadyScheduled ? 'disabled' : '' }}>
        {{ $p->name }} ({{ $p->patient_id }}) 
        {{ !$consulted ? '- Must be Consulted' : '' }}
        {{ $alreadyScheduled ? '- Already Scheduled' : '' }}
    </option>
@endforeach


                    </select>
                </div>

                <div class="mb-10">
                    <label>Next Visit Date</label>
                    <input type="date" name="next_visit" class="core1-input w-100" required>
                </div>

                <button type="submit" class="core1-btn core1-btn-primary">Schedule</button>
                <button type="button" class="core1-btn core1-btn-outline"
                    onclick="document.getElementById('newFollowUpModal').style.display='none'">
                    Cancel
                </button>
            </form>
        </div>
    </div>
    @endif
</div>


<script>
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
</script>
@endsection
