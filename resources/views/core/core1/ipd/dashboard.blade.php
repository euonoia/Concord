@extends('core.core1.layouts.app')

@section('title', 'IPD Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Inpatient Department (IPD)</h2>
            <p class="core1-subtitle">Manage admitted patients, wards, and beds.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="core1-alert core1-alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="core1-card">
        <h3 class="core1-title text-lg mb-4">Currently Admitted Patients</h3>
        <div style="overflow-x:auto;">
            <table class="w-full text-left" style="border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">MRN</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Patient Name</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Ward</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Room</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Bed</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Admission Date</th>
                        <th class="p-3 border-b text-sm font-semibold tracking-wider text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admissions as $admission)
                        <tr class="border-b" style="transition: background-color 0.2s; cursor:pointer;" onmouseover="this.style.backgroundColor='#f1f5f9';" onmouseout="this.style.backgroundColor='transparent';">
                            <td class="p-3 font-mono text-sm" style="color:#1a3a5a;">{{ $admission->encounter->patient->mrn }}</td>
                            <td class="p-3 font-bold">{{ $admission->encounter->patient->first_name }} {{ $admission->encounter->patient->last_name }}</td>
                            <td class="p-3">{{ $admission->bed->room->ward->name }}</td>
                            <td class="p-3">Room {{ $admission->bed->room->room_number }} <span class="text-xstext-gray-500">({{ $admission->bed->room->room_type }})</span></td>
                            <td class="p-3">Bed {{ $admission->bed->bed_number }}</td>
                            <td class="p-3">{{ $admission->admission_date->format('M d, Y h:i A') }}</td>
                            <td class="p-3">
                                <a href="{{ route('core1.patients.show', $admission->encounter->patient_id) }}" class="core1-btn core1-btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.8rem;">
                                    View Patient
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-500 italic">No patients are currently admitted in the IPD.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
