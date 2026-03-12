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
        <a href="{{ route('core1.medical-records.index') }}"
           class="px-4 py-2 border rounded-lg hover:bg-gray-100">
            Back
        </a>
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
                            <td style="padding: 14px 20px; font-family: monospace; font-weight: 600; color: var(--primary);">{{ $patient->mrn ?? $patient->patient_id ?? 'N/A' }}</td>
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
                                            <span style="font-size: 10px; color: var(--text-gray);">{{ $encounter->triage->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                        <div style="color: var(--text-gray); display: flex; flex-wrap: wrap; gap: 12px; font-weight: 500;">
                                            <span><strong>BP:</strong> {{ $encounter->triage->blood_pressure ?? '--' }}</span>
                                            <span><strong>HR:</strong> {{ $encounter->triage->heart_rate ?? '--' }} bpm</span>
                                            <span><strong>Temp:</strong> {{ $encounter->triage->temperature ?? '--' }} °C</span>
                                            <span><strong>SpO2:</strong> {{ $encounter->triage->oxygen_saturation ?? '--' }} %</span>
                                            <span><strong>Weight:</strong> {{ $encounter->triage->weight ?? '--' }} kg</span>
                                        </div>
                                        @if($encounter->triage->notes)
                                            <div style="margin-top: 6px; font-size: 11px; color: var(--text-gray); font-style: italic;">"{{ $encounter->triage->notes }}"</div>
                                        @endif
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
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-droplet-half" style="color: var(--warning);"></i> Laboratory Orders
                                        </div>
                                        <ul style="margin: 0; padding-left: 20px; color: var(--text-gray); font-weight: 500;">
                                            @foreach($encounter->labOrders as $lab)
                                                <li style="margin-bottom: 2px;">
                                                    {{ $lab->test_name }} 
                                                    <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: {{ $lab->status === 'Completed' ? 'var(--success-light)' : 'var(--warning-light-more)' }}; color: {{ $lab->status === 'Completed' ? 'var(--success)' : 'var(--warning)' }};">{{ $lab->status }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- PRESCRIPTIONS --}}
                                @if($encounter->prescriptions->count() > 0)
                                    <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-capsule" style="color: var(--primary);"></i> Prescriptions
                                        </div>
                                        <ul style="margin: 0; padding-left: 20px; color: var(--text-gray); font-weight: 500;">
                                            @foreach($encounter->prescriptions as $rx)
                                                <li style="margin-bottom: 4px;">
                                                    <strong style="color: var(--text-dark);">{{ $rx->medication_name }}</strong> ({{ $rx->dosage }}) <br>
                                                    <span style="font-size: 11px; font-style: italic;">Sig: {{ $rx->instructions }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 16px 20px; text-align: center;">
                                @if(($encounter->status ?? '') === 'Closed')
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--text-gray); font-size: 12px; font-weight: 600;"><i class="bi bi-lock-fill"></i> Closed</span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--success); font-size: 12px; font-weight: 600;"><i class="bi bi-unlock-fill"></i> {{ $encounter->status }}</span>
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
                    <tr><td class="font-medium py-2">Medical History</td><td>{{ $patient->medical_history ?? 'N/A' }}</td></tr>
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
@endsection