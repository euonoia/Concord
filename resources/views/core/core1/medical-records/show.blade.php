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

        {{-- ================= RECORD INFORMATION ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Record Information</h2>
            <table class="w-full text-sm">
                <tbody class="divide-y">
                    <tr>
                        <td class="font-medium py-2 w-1/3">Patient</td>
                        <td>{{ $patient->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">MRN (Patient ID)</td>
                        <td class="font-mono text-blue-800">{{ $patient->mrn ?? $patient->patient_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Primary Doctor</td>
                        <td>{{ $patient->doctor->name ?? 'N/A' }}</td>
                    </tr>
                    
                    <tr>
                        <td class="font-medium py-2">Registration Status</td>
                        <td>{{ $patient->registration_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Last Visit</td>
                        <td>{{ optional($patient->last_visit)->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ================= CLINICAL ENCOUNTERS HISTORY ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Clinical Encounters History</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Date & Time</th>
                            <th class="p-2 border">Encounter Type</th>
                            <th class="p-2 border">Attending Doctor</th>
                            <th class="p-2 border">Chief Complaint / Details</th>
                            <th class="p-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encounters as $encounter)
                        <tr>
                            <td class="p-2 border whitespace-nowrap">{{ $encounter->created_at->format('M d, Y h:i A') }}</td>
                            <td class="p-2 border font-semibold text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $encounter->type === 'IPD' ? 'bg-blue-100 text-blue-800' : ($encounter->type === 'Operating Room' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $encounter->type }}
                                </span>
                            </td>
                            <td class="p-2 border">{{ $encounter->doctor->name ?? 'Unassigned' }}</td>
                            <td class="p-2 border">
                                <p class="mb-1">{{ $encounter->chief_complaint ?? '--' }}</p>
                                @if($encounter->type === 'IPD' && $encounter->admission)
                                    <div class="mt-2 text-xs bg-gray-50 p-2 rounded border">
                                        <strong>Admission Details:</strong><br>
                                        Date: {{ \Carbon\Carbon::parse($encounter->admission->admission_date)->format('M d, Y h:i A') }}<br>
                                        Location: {{ $encounter->admission->bed->room->ward->name }} - Room {{ $encounter->admission->bed->room->room_number }} (Bed {{ $encounter->admission->bed->bed_number }})<br>
                                        Discharge: {{ $encounter->admission->discharge_date ? \Carbon\Carbon::parse($encounter->admission->discharge_date)->format('M d, Y h:i A') : 'Ongoing' }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-2 border text-center">{{ $encounter->status }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500 italic">No clinical encounters recorded.</td>
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