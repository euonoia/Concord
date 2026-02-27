@extends('layouts.core1.layouts.app')

@section('title', 'Medical Records')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Medical Records</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $patient)
                        @php
                            $latestRecord = $patient->medicalRecords->first();
                            $latestAppointment = $patient->appointments->first();
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $patient->name }}</td>
                            <td class="px-6 py-4">
                                @if($latestRecord)
                                    {{ $latestRecord->record_type }}
                                @elseif($latestAppointment)
                                    {{ ucfirst($latestAppointment->type ?? 'N/A') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($latestRecord)
                                    {{ $latestRecord->record_date->format('M d, Y') }}
                                @elseif($latestAppointment)
                                    {{ \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
    <a href="{{ route('core1.medical-records.show', $patient->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">
        {{ $records->links() }}
    </div>
</div>
@endsection