@extends(request()->ajax() ? 'core.core1.layouts.ajax' : 'core.core1.layouts.app')

@section('title', 'Medical Record Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Medical Record Details</h1>
            <p class="text-gray-600">Complete medical record information</p>
        </div>
        <div class="flex gap-2">
            @if(isset($activeEncounter))
                <div class="core1-flex-gap-2 mr-10" style="padding: 4px; background: var(--bg-light); border-radius: 12px; border: 1px solid var(--border-color);">
                    <button type="button" class="core1-btn-sm core1-btn-outline" 
                            onclick="openVitalsModal({{ $activeEncounter->id }}, '{{ $patient->name }}')" 
                            title="Record Vitals"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; color: var(--danger); border-color: rgba(220, 38, 38, 0.1);">
                        <i class="bi bi-heart-pulse"></i> Vitals
                    </button>
                    <button type="button" class="core1-btn-sm core1-btn-outline" 
                            onclick="openNotesModal({{ $activeEncounter->id }}, '{{ $patient->name }}')" 
                            title="Add Clinical Note"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px;">
                        <i class="bi bi-pencil-square"></i> Notes
                    </button>
                    <button type="button" class="core1-btn-sm core1-btn-outline" 
                            onclick="openMedicationModal({{ $activeEncounter->id }})" 
                            title="Issue Medication"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px;">
                        <i class="bi bi-capsule"></i> Meds
                    </button>
                    <button type="button" class="core1-btn-sm core1-btn-outline" 
                            onclick="openLabOrderModal({{ $activeEncounter->id }})" 
                            title="Order Lab Test"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px;">
                        <i class="bi bi-droplet-half"></i> Labs
                    </button>
                </div>
            @endif
            <a href="{{ route('core1.medical-records.index') }}"
               class="px-4 py-2 border rounded-lg hover:bg-gray-100 flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-xl border p-6 space-y-8">

    <div style="font-family: 'Inter', system-ui, sans-serif; color: var(--text-dark); display: flex; flex-direction: column; gap: 32px;">

        {{-- ================= RECORD INFORMATION ================= --}}
        <div>
            <h2 style="font-size: 16px; font-weight: 600; color: var(--text-dark); margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-flex; justify-content: center; align-items: center; width: 28px; height: 28px; background: var(--primary-light); color: var(--primary); border-radius: 8px; font-size: 14px;"><i class="bi bi-person-vcard"></i></span>
                Record Information
            </h2>
            <div style="background: var(--bg-light); border-radius: 12px; border: 1px solid var(--border-color); padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        <tr style="border-bottom: 1px dashed var(--border-color);">
                            <td style="padding: 14px 20px; color: var(--text-gray); font-weight: 500; width: 35%;">Patient Name</td>
                            <td style="padding: 14px 20px; color: var(--text-dark); font-weight: 600;">{{ $patient->name ?? 'N/A' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed var(--border-color);">
                            <td style="padding: 14px 20px; color: var(--text-gray); font-weight: 500;">MRN (Patient ID)</td>
                            <td style="padding: 14px 20px; font-family: monospace; font-weight: 600; color: var(--primary);">{{ $patient->mrn ?? 'N/A' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed var(--border-color);">
                            <td style="padding: 14px 20px; color: var(--text-gray); font-weight: 500;">Primary Doctor</td>
                            <td style="padding: 14px 20px; color: var(--text-dark);">
                                @if(isset($patient->doctor->name))
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: white; padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 12px; font-weight: 500;">
                                        <i class="bi bi-person-badge" style="color: var(--primary);"></i> Dr. {{ $patient->doctor->name }}
                                    </span>
                                @else
                                    <span style="color: var(--text-light); font-style: italic;">Unassigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px dashed var(--border-color);">
                            <td style="padding: 14px 20px; color: var(--text-gray); font-weight: 500;">Registration Status</td>
                            <td style="padding: 14px 20px;">
                                @if(($patient->registration_status ?? '') === 'REGISTERED')
                                    <span style="display: inline-flex; align-items: center; background: var(--success-light); color: var(--success); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;"><i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> REGISTERED</span>
                                @else
                                    <span style="display: inline-flex; align-items: center; background: var(--warning-light-more); color: var(--warning); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ $patient->registration_status ?? 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 14px 20px; color: var(--text-gray); font-weight: 500;">Last Visit</td>
                            <td style="padding: 14px 20px; color: var(--text-dark);">
                                @if(optional($patient->last_visit)->format('M d, Y'))
                                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500;"><i class="bi bi-calendar-event" style="color: var(--text-light);"></i> {{ optional($patient->last_visit)->format('M d, Y') }}</span>
                                @else
                                    <span style="color: var(--text-light); font-style: italic;">No previous visits</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= CLINICAL ENCOUNTERS HISTORY ================= --}}
        <div>
            <h2 style="font-size: 16px; font-weight: 600; color: var(--text-dark); margin: 0 0 16px 0; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-flex; justify-content: center; align-items: center; width: 28px; height: 28px; background: var(--info-light); color: var(--info); border-radius: 8px; font-size: 14px;"><i class="bi bi-activity"></i></span>
                Clinical Encounters History
            </h2>
            <div style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: white;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                    <thead style="background: var(--bg-light); border-bottom: 1px solid var(--border-color);">
                        <tr>
                            <th style="padding: 14px 20px; font-weight: 600; color: var(--text-gray);">Date & Time</th>
                            <th style="padding: 14px 20px; font-weight: 600; color: var(--text-gray);">Encounter Type</th>
                            <th style="padding: 14px 20px; font-weight: 600; color: var(--text-gray);">Attending Doctor</th>
                            <th style="padding: 14px 20px; font-weight: 600; color: var(--text-gray);">Details</th>
                            <th style="padding: 14px 20px; font-weight: 600; color: var(--text-gray); text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encounters as $encounter)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 16px 20px; color: var(--text-dark); white-space: nowrap;">
                                <div style="font-weight: 600;">{{ $encounter->created_at->format('M d, Y') }}</div>
                                <div style="font-size: 12px; color: var(--text-gray); margin-top: 2px;">{{ $encounter->created_at->format('h:i A') }}</div>
                            </td>
                            <td style="padding: 16px 20px;">
                                @if($encounter->type === 'IPD')
                                    <span style="display: inline-flex; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ strtoupper($encounter->type) }}</span>
                                @elseif($encounter->type === 'Operating Room')
                                    <span style="display: inline-flex; background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ strtoupper($encounter->type) }}</span>
                                @else
                                    <span style="display: inline-flex; background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">{{ strtoupper($encounter->type) }}</span>
                                @endif
                            </td>
                            <td style="padding: 16px 20px; color: var(--text-dark); font-weight: 500;">
                                {{ $encounter->doctor->name ?? 'Unassigned' }}
                            </td>
                            <td style="padding: 16px 20px;">
                                @if(!empty($encounter->chief_complaint))
                                    <div style="color: var(--text-dark); margin-bottom: 8px; font-weight: 500;">{{ $encounter->chief_complaint }}</div>
                                @endif

                                {{-- ADMISSION DETAILS (If IPD) --}}
                                @if($encounter->type === 'IPD' && $encounter->admission)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-hospital" style="color: var(--primary);"></i> Admission Location
                                        </div>
                                        <div style="color: var(--text-gray); margin-bottom: 6px; font-weight: 500;">
                                            {{ optional(optional(optional($encounter->admission->bed)->room)->ward)->name }} - Room {{ optional(optional($encounter->admission->bed)->room)->room_number }} (Bed {{ optional($encounter->admission->bed)->bed_number }})
                                        </div>
                                        <div style="color: var(--text-gray); display: flex; flex-wrap: wrap; align-items: center; gap: 12px; font-weight: 500;">
                                            <span style="display: flex; align-items: center; gap: 4px;"><i class="bi bi-box-arrow-in-right" style="color: var(--success);"></i> IN: {{ \Carbon\Carbon::parse($encounter->admission->admission_date)->format('M d, Y h:i A') }}</span>
                                            @if($encounter->admission->discharge_date)
                                                <span style="display: flex; align-items: center; gap: 4px;"><i class="bi bi-box-arrow-right" style="color: var(--danger);"></i> OUT: {{ \Carbon\Carbon::parse($encounter->admission->discharge_date)->format('M d, Y h:i A') }}</span>
                                            @else
                                                <span style="display: flex; align-items: center; gap: 4px; color: var(--warning);"><i class="bi bi-clock-history"></i> Ongoing</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- TRIAGE DETAILS --}}
                                @if($encounter->triage)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-heart-pulse" style="color: var(--danger);"></i> Triage Vitals</div>
                                            <div style="text-align: right;">
                                                <span style="font-size: 10px; color: var(--text-gray); display: block;">{{ $encounter->triage->created_at->format('M d, Y h:i A') }}</span>
                                                @if($encounter->triage->creator)
                                                    <span style="font-size: 9px; color: var(--primary); font-weight: 700; text-transform: uppercase;">Taken by: {{ $encounter->triage->creator->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <button type="button" onclick="this.nextElementSibling.style.display = (this.nextElementSibling.style.display === 'none' ? 'block' : 'none'); this.querySelector('i').className = (this.nextElementSibling.style.display === 'none' ? 'bi bi-chevron-down' : 'bi bi-chevron-up')" style="width: 100%; background: white; border: 1px dashed var(--border-color); padding: 6px; border-radius: 6px; color: var(--primary); font-size: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; margin-bottom: 8px; transition: all 0.2s;">
                                            <i class="bi bi-chevron-down"></i> VIEW VITALS DETAILS
                                        </button>
                                        
                                        <div style="display: none;">
                                            <div style="color: var(--text-gray); display: flex; flex-wrap: wrap; gap: 12px; font-weight: 500; padding: 8px; background: white; border-radius: 6px; border: 1px solid var(--border-color);">
                                                <span><strong>BP:</strong> {{ $encounter->triage->blood_pressure ?? '--' }}</span>
                                                <span><strong>HR:</strong> {{ $encounter->triage->heart_rate ?? '--' }} bpm</span>
                                                <span><strong>Temp:</strong> {{ $encounter->triage->temperature ?? '--' }} °C</span>
                                                <span><strong>SpO2:</strong> {{ $encounter->triage->spo2 ?? '--' }} %</span>
                                                <span><strong>Weight:</strong> {{ $encounter->triage->weight ?? '--' }} kg</span>
                                            </div>
                                            @if($encounter->triage->notes)
                                                <div style="margin-top: 6px; font-size: 11px; color: var(--text-gray); font-style: italic;">"{{ $encounter->triage->notes }}"</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- CONSULTATION DETAILS --}}
                                @if($encounter->consultation)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-stethoscope" style="color: var(--info);"></i> Consultation Note</div>
                                            <span style="font-size: 10px; color: var(--text-gray);">{{ $encounter->consultation->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        <div style="color: var(--text-gray); font-weight: 500; margin-bottom: 4px;">
                                            <span style="color: var(--text-dark);">Diagnosis:</span> {{ $encounter->consultation->diagnosis ?? 'Not specified' }}
                                        </div>
                                        <div style="color: var(--text-gray); font-weight: 500;">
                                            <span style="color: var(--text-dark);">Notes:</span> {{ $encounter->consultation->notes ?? 'No additional notes' }}
                                        </div>
                                    </div>
                                @endif

                                {{-- LAB ORDERS --}}
                                @if($encounter->labOrders->count() > 0)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-droplet-half" style="color: var(--warning);"></i> Laboratory Orders</div>
                                            <span style="font-size: 10px; color: var(--text-gray);">{{ $encounter->labOrders->count() }} test(s)</span>
                                        </div>
                                        
                                        <button type="button" onclick="const content = this.nextElementSibling; const isHidden = content.style.display === 'none'; content.style.display = isHidden ? 'flex' : 'none'; this.querySelector('i').className = isHidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down'; this.style.borderColor = isHidden ? 'var(--warning)' : 'var(--border-color)';" 
                                                style="width: 100%; background: white; border: 1px solid var(--border-color); padding: 8px; border-radius: 8px; color: var(--warning); font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="bi bi-chevron-down"></i> <span style="letter-spacing: 0.3px;">VIEW LABORATORY DETAILS</span>
                                        </button>

                                        <div style="display: none; flex-direction: column; gap: 10px;">
                                            @foreach($encounter->labOrders as $lab)
                                                <div style="padding: 12px; background: white; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px dashed var(--border-color);">
                                                        <span style="font-weight: 700; color: var(--text-dark); font-size: 13px;">{{ $lab->test_name }}</span>
                                                        <span style="font-size: 10px; padding: 3px 8px; border-radius: 6px; background: {{ $lab->status === 'Completed' ? 'var(--success-light)' : 'var(--warning-light-more)' }}; color: {{ $lab->status === 'Completed' ? 'var(--success)' : 'var(--warning)' }}; font-weight: 700; letter-spacing: 0.5px;">{{ strtoupper($lab->status) }}</span>
                                                    </div>
                                                    
                                                    <div style="font-size: 11px; color: var(--text-gray); display: flex; flex-direction: column; gap: 6px;">
                                                        <div style="display: flex; gap: 16px; font-weight: 500;">
                                                            <span style="display: flex; align-items: center; gap: 4px;"><i class="bi bi-calendar-event"></i> {{ $lab->created_at->format('M d, Y') }}</span>
                                                            @if($lab->result_received_at)
                                                                <span style="display: flex; align-items: center; gap: 4px; color: var(--primary);"><i class="bi bi-clock-history"></i> {{ $lab->result_received_at->format('h:i A') }}</span>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($lab->clinical_note)
                                                            <div style="padding: 6px 10px; background: #f8fafc; border-radius: 6px; font-style: italic; color: #475569; font-size: 11px; border-left: 2px solid #cbd5e1;">
                                                                <strong style="color: #1e293b; font-style: normal;">Clinical Note:</strong> {{ $lab->clinical_note }}
                                                            </div>
                                                        @endif

                                                        @if($lab->result_received_at)
                                                            <div style="margin-top: 4px;">
                                                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px; color: var(--text-dark); font-weight: 700; font-size: 11px;">
                                                                    <i class="bi bi-clipboard2-pulse" style="color: var(--success);"></i> CLINICAL RESULTS
                                                                </div>
                                                                                                                                 @php
                                                                    $resultData = $lab->result_data;
                                                                    if (is_string($resultData)) {
                                                                        $decoded = json_decode($resultData, true);
                                                                        if (json_last_error() === JSON_ERROR_NONE) {
                                                                            $resultData = $decoded;
                                                                        }
                                                                    }
                                                                @endphp
                                                                
                                                                @if(is_array($resultData))
                                                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px;">
                                                                        @foreach($resultData as $key => $value)
                                                                            <div style="padding: 8px; background: #f0fdf4; border-radius: 6px; border: 1px solid #dcfce7;">
                                                                                <div style="font-size: 9px; text-transform: uppercase; color: #166534; font-weight: 700; margin-bottom: 2px;">{{ str_replace('_', ' ', $key) }}</div>
                                                                                <div style="font-size: 12px; color: #064e3b; font-weight: 600; font-family: 'JetBrains Mono', monospace;">{{ is_array($value) ? json_encode($value) : $value }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @elseif($resultData)
                                                                    {{-- Fallback if not an array but has content --}}
                                                                    <div style="padding: 10px; background: #f0fdf4; border-radius: 6px; border-left: 3px solid var(--success); font-family: monospace; font-size: 11px; color: #064e3b; line-height: 1.5;">
                                                                        {{ is_string($resultData) ? $resultData : json_encode($resultData) }}
                                                                    </div>
                                                                @else
                                                                    <div style="padding: 10px; background: #fff7ed; border-radius: 6px; border-left: 3px solid var(--warning); color: #9a3412; font-size: 11px; font-style: italic;">
                                                                        <i class="bi bi-exclamation-triangle"></i> Result data is empty or pending final validation.
                                                                    </div>
                                                                @endif

                                                            </div>
                                                        @else
                                                            <div style="margin-top: 8px; padding: 12px; background: #fffbeb; border-radius: 8px; border: 1px solid #fef3c7; color: #92400e; font-size: 11px; display: flex; align-items: center; gap: 8px;">
                                                                <div style="display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #fef3c7; border-radius: 50%;"><i class="bi bi-hourglass-split" style="font-size: 14px;"></i></div>
                                                                <div>
                                                                    <div style="font-weight: 700;">Results Pending</div>
                                                                    <div style="font-size: 10px; opacity: 0.8;">Sample is being processed by the laboratory.</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif


                                {{-- PRESCRIPTIONS --}}
                                @if($encounter->prescriptions->count() > 0)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 6px;"><i class="bi bi-capsule" style="color: var(--primary);"></i> Inpatient Meds & History</div>
                                            <span style="font-size: 10px; color: var(--text-gray);">{{ $encounter->prescriptions->count() }} active item(s)</span>
                                        </div>

                                        <button type="button" onclick="const content = this.nextElementSibling; const isHidden = content.style.display === 'none'; content.style.display = isHidden ? 'flex' : 'none'; this.querySelector('i').className = isHidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down'; this.style.borderColor = isHidden ? 'var(--primary)' : 'var(--border-color)';" 
                                                style="width: 100%; background: white; border: 1px solid var(--border-color); padding: 8px; border-radius: 8px; color: var(--primary); font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class="bi bi-chevron-down"></i> <span style="letter-spacing: 0.3px;">VIEW MEDICATION DETAILS</span>
                                        </button>

                                        <div style="display: none; flex-direction: column; gap: 10px;">
                                            @foreach($encounter->prescriptions as $rx)
                                                <div style="padding: 12px; background: white; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                                        <strong style="color: var(--text-dark); font-size: 13px;">{{ $rx->medication }}</strong>
                                                        <span style="font-size: 11px; font-weight: 600; color: var(--primary);">{{ $rx->dosage }}</span>
                                                    </div>
                                                    <div style="font-size: 11px; color: var(--text-gray); margin-bottom: 8px; font-style: italic;">Sig: {{ $rx->instructions }}</div>
                                                    
                                                    @php $adminCount = $rx->administrations->count(); @endphp
                                                    
                                                    @if($adminCount > 0)
                                                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border-color);">
                                                            <button type="button" onclick="const log = this.nextElementSibling; const isHidden = log.style.display === 'none'; log.style.display = isHidden ? 'flex' : 'none'; this.querySelector('i').className = isHidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down';" 
                                                                    style="width: 100%; height: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; color: #64748b; font-size: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 4px; transition: all 0.2s;">
                                                                <i class="bi bi-chevron-down"></i> ADMINISTRATION LOG ({{ $adminCount }})
                                                            </button>
                                                            <div style="display: none; flex-direction: column; gap: 4px; margin-top: 6px;">
                                                                @foreach($rx->administrations as $admin)
                                                                    <div style="display: flex; justify-content: space-between; font-size: 10px; padding: 6px 8px; background: #f0fdf4; border-radius: 4px; border-left: 2px solid var(--success);">
                                                                        <span style="color: var(--text-dark); font-weight: 600;">{{ $admin->administered_at->format('M d, Y h:i A') }}</span>
                                                                        <span style="color: var(--primary); font-weight: 700;">{{ $admin->administrator->name ?? 'Staff' }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed var(--border-color); color: var(--text-gray); font-size: 10px; font-style: italic; text-align: center; opacity: 0.8;">
                                                            <i class="bi bi-info-circle"></i> No administrations recorded.
                                                        </div>
                                                    @endif

                                                    @if($encounter->type === 'IPD' && in_array($encounter->admission->status ?? '', ['Admitted', 'Doctor Approved']))
                                                        @if($rx->status === 'Dispensed')
                                                            <form method="POST" action="{{ route('core1.outpatient.administerMedication', $rx->id) }}" style="margin-top: 10px;">
                                                                @csrf
                                                                <button type="submit" style="width: 100%; height: 32px; border: 1px solid var(--success); background: #f0fdf4; color: var(--success); border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;" onmouseover="this.style.background='var(--success)'; this.style.color='white'" onmouseout="this.style.background='#f0fdf4'; this.style.color='var(--success)'">
                                                                    <i class="bi bi-plus-circle"></i> Mark as Administered
                                                                </button>
                                                            </form>
                                                        @elseif($rx->status !== 'Administered')
                                                            <div style="margin-top: 10px; padding: 10px; background: var(--bg-light); border: 1px dashed var(--warning); border-radius: 8px; color: var(--warning); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                                <i class="bi bi-hourglass-split"></i> PENDING PHARMACY ({{ $rx->status }})
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif


                            </td>
                            <td style="padding: 16px 20px; text-align: center;">
                                @php
                                    // A record is truly closed ONLY if it's marked as Closed AND (if admitted) they have a discharge date.
                                    $isActuallyClosed = ($encounter->status ?? '') === 'Closed';
                                    if ($encounter->type === 'IPD' && $encounter->admission && null === $encounter->admission->discharge_date) {
                                        $isActuallyClosed = false;
                                    }
                                @endphp

                                @if($isActuallyClosed)
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--text-gray); font-size: 12px; font-weight: 600;">
                                        <i class="bi bi-lock-fill"></i> Closed
                                    </span>
                                @else
                                    @php
                                        $location = 'Outpatient Dept';
                                        $locationColor = 'var(--text-dark)';
                                        
                                        if ($encounter->type === 'IPD' && $encounter->admission) {
                                            $wardName = optional(optional(optional($encounter->admission->bed)->room)->ward)->name ?? 'Ward';
                                            $location = 'Admitted: ' . $wardName;
                                            $locationColor = 'var(--primary)';
                                        } elseif ($encounter->type === 'IPD') {
                                            $location = 'Pending Admission';
                                            $locationColor = 'var(--warning)';
                                        } elseif ($encounter->status === 'Pending Billing') {
                                            $location = 'Billing Dept';
                                            $locationColor = 'var(--info)';
                                        } elseif ($encounter->consultation) {
                                            $location = 'Consultation Room';
                                            $locationColor = 'var(--success)';
                                        } elseif ($encounter->triage) {
                                            $location = 'Triage Station';
                                            $locationColor = 'var(--warning)';
                                        } else {
                                            $location = 'Waiting / Reception';
                                            $locationColor = 'var(--text-gray)';
                                        }
                                    @endphp

                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px; color: {{ $locationColor }}; font-size: 11px; font-weight: 700; background: var(--bg-light); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-color);">
                                            <i class="bi bi-geo-alt-fill"></i> {{ $location }}
                                        </span>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--success); font-size: 11px; font-weight: 600;">
                                            <i class="bi bi-unlock-fill"></i> {{ $encounter->status === 'Closed' ? 'Active' : $encounter->status }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 40px 20px; text-align: center;">
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px; background: var(--bg-light); color: var(--text-light); margin-bottom: 12px;">
                                    <i class="bi bi-journal-x" style="font-size: 1.5rem;"></i>
                                </div>
                                <div style="color: var(--text-gray); font-size: 13px; font-weight: 500;">No clinical encounters recorded.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

@php $role = auth()->user()->role; @endphp

{{-- ================= ASSIGNED NURSE FOR DOCTOR ================= --}}
@if($role === 'doctor')
<div>
    <h2 class="text-lg font-semibold mb-4 border-b pb-2">Assigned Nurse</h2>
    <table class="w-full text-sm">
        <tbody class="divide-y">
            <tr>
                <td class="font-medium py-2 w-1/3">Assigned Nurse</td>
                <td>{{ optional($patient->assignedNurse)->name ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

        @php $role = auth()->user()->role; @endphp

        @if(in_array($role, ['admin','head_nurse','nurse']))

        {{-- ================= PATIENT INFORMATION ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Patient Information</h2>
            <table class="w-full text-sm">
                <tbody class="divide-y">
                    <tr><td class="font-medium py-2 w-1/3">Date of Birth</td><td>{{ $patient->date_of_birth ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Gender</td><td>{{ $patient->gender ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Phone</td><td>{{ $patient->phone ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Email</td><td>{{ $patient->email ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Address</td><td>{{ $patient->address ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Blood Type</td><td>{{ $patient->blood_type ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Allergies</td><td>{{ $patient->allergies ?? 'N/A' }}</td></tr>
                    <tr>
                        <td class="font-medium py-2">Medical History</td>
                        <td>
                            @if(!empty($patient->medical_history) && strlen($patient->medical_history) > 100)
                                <div class="relative">
                                    <div id="med-history-content" class="text-gray-700" style="max-height: 3em; overflow: hidden; transition: max-height 0.3s ease-out;">
                                        {{ $patient->medical_history }}
                                    </div>
                                    <button type="button" onclick="const content = document.getElementById('med-history-content'); const isExpanded = content.style.maxHeight !== '3em'; content.style.maxHeight = isExpanded ? '3em' : 'none'; this.innerText = isExpanded ? 'Read More' : 'Read Less';" 
                                            class="text-primary hover:underline font-semibold text-xs mt-1">Read More</button>
                                </div>
                            @else
                                {{ $patient->medical_history ?? 'N/A' }}
                            @endif
                        </td>
                    </tr>
                    <tr><td class="font-medium py-2">Status</td><td>{{ $patient->status ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Care Type</td><td>{{ $patient->care_type ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Admission Date</td><td>{{ $patient->admission_date ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Reason</td><td>{{ $patient->reason ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>

        {{-- ================= ASSIGNED STAFF ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Assigned Staff</h2>
            <table class="w-full text-sm">
                <tbody class="divide-y">
                    <tr>
                        <td class="font-medium py-2 w-1/3">Assigned Doctor</td>
                        <td>{{ optional($patient->doctor)->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Assigned Nurse</td>
                        <td>{{ optional($patient->assignedNurse)->name ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ================= APPOINTMENTS ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Appointments</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Date</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Type</th>
                            <th class="p-2 border">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->appointments ?? [] as $appointment)
                        <tr>
                            <td class="p-2 border">{{ $appointment->appointment_date ?? 'N/A' }}</td>
                            <td class="p-2 border">{{ $appointment->status ?? 'N/A' }}</td>
                            <td class="p-2 border">{{ $appointment->type ?? 'N/A' }}</td>
                            <td class="p-2 border">{{ $appointment->reason ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-2 text-center">No appointments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= BILLING ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Billing Information</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Bill #</th>
                            <th class="p-2 border">Total</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->bills ?? [] as $bill)
                        <tr>
                            <td class="p-2 border">{{ $bill->bill_number ?? 'N/A' }}</td>
                            <td class="p-2 border">â‚±{{ number_format($bill->total ?? 0, 2) }}</td>
                            <td class="p-2 border">{{ $bill->status ?? 'N/A' }}</td>
                            <td class="p-2 border">{{ $bill->payment_method ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-2 text-center">No billing records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @endif

    </div>
</div>
@if(!request()->ajax())
    {{-- Only include modals if it's NOT an AJAX request (standalone page) --}}
    @include('core.core1.inpatient.modals.clinical_actions')
@endif
@endsection