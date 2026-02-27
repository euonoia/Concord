@extends('layouts.core1.layouts.app')

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
                        <td>{{ $record->patient->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Patient ID</td>
                        <td>{{ $record->patient->patient_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Doctor</td>
                        <td>{{ $record->doctor->name ?? 'N/A' }}</td>
                    </tr>
                    
                    <tr>
                        <td class="font-medium py-2">Record Type</td>
                        <td>{{ $record->record_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Record Date</td>
                        <td>{{ optional($record->record_date)->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ================= CLINICAL INFORMATION ================= --}}
        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Clinical Information</h2>
            <table class="w-full text-sm">
                <tbody class="divide-y">
                    <tr>
                        <td class="font-medium py-2 w-1/3">Diagnosis</td>
                        <td>{{ $record->diagnosis ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Treatment</td>
                        <td>{{ $record->treatment ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Prescription</td>
                        <td class="whitespace-pre-line">{{ $record->prescription ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Notes</td>
                        <td>{{ $record->notes ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
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
                <td>{{ optional($record->patient->assignedNurse)->name ?? 'N/A' }}</td>
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
                    <tr><td class="font-medium py-2 w-1/3">Date of Birth</td><td>{{ $record->patient->date_of_birth ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Gender</td><td>{{ $record->patient->gender ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Phone</td><td>{{ $record->patient->phone ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Email</td><td>{{ $record->patient->email ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Address</td><td>{{ $record->patient->address ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Blood Type</td><td>{{ $record->patient->blood_type ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Allergies</td><td>{{ $record->patient->allergies ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Medical History</td><td>{{ $record->patient->medical_history ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Status</td><td>{{ $record->patient->status ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Care Type</td><td>{{ $record->patient->care_type ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Admission Date</td><td>{{ $record->patient->admission_date ?? 'N/A' }}</td></tr>
                    <tr><td class="font-medium py-2">Reason</td><td>{{ $record->patient->reason ?? 'N/A' }}</td></tr>
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
                        <td>{{ optional($record->patient->doctor)->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium py-2">Assigned Nurse</td>
                        <td>{{ optional($record->patient->assignedNurse)->name ?? 'N/A' }}</td>
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
                        @forelse($record->patient->appointments ?? [] as $appointment)
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
                        @forelse($record->patient->bills ?? [] as $bill)
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