@extends('layouts.core1.layouts.app')

@section('title', 'Discharge Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Discharge Management</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Patient</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Patient ID</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Doctor</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Assigned Nurse</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Care Type</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Appointment Date</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Last Diagnosis</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Billing Status</th>
                        <th class="px-6 py-3 text-left uppercase text-xs font-medium text-gray-500">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        @php
                            $patient = $appointment->patient;
                            $latestRecord = $patient->medicalRecords()->latest('record_date')->first();
                            $latestBill = $patient->bills()->latest('bill_date')->first();
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $patient->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $patient->patient_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($appointment->doctor)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ optional($patient->assignedNurse)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $patient->care_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $latestRecord->diagnosis ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($latestBill)
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $latestBill->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($latestBill->status) }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                    Mark as Discharged
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-6 text-center text-gray-500">
                                No completed patients ready for discharge.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $appointments->links() }}
    </div>
</div>
@endsection