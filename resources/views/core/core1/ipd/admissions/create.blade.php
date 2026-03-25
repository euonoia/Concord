@extends('core.core1.layouts.app')

@section('title', 'Admit Patient')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h2 class="core1-title">Admit Patient to IPD</h2>
            <p class="core1-subtitle">Assign a Ward, Room, and Bed for {{ $encounter->patient->first_name }} {{ $encounter->patient->last_name }} (MRN: <span class="font-mono" style="color:#1a3a5a;">{{ $encounter->patient->mrn ?? 'Pending' }}</span>)</p>
        </div>
        <div class="core1-flex-gap-2">
            <a href="{{ route('core1.patients.show', $encounter->patient_id) }}" class="core1-btn core1-btn-outline">
                <i class="fas fa-arrow-left"></i>
                <span class="pl-20">Cancel</span>
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="core1-alert core1-alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="core1-card">
        <form action="{{ route('core1.admissions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="encounter_id" value="{{ $encounter->id }}">
            
            <div class="core1-info-grid">
                <div class="core1-info-item core1-col-span-2">
                    <label for="bed_id" class="font-bold block mb-2" style="color:#1a3a5a;">Select Available Bed</label>
                    <select name="bed_id" id="bed_id" class="w-full p-2 border rounded" required>
                        <option value="">-- Choose Bed --</option>
                        @foreach ($wards as $ward)
                            <optgroup label="Ward: {{ $ward->name }}">
                                @foreach ($ward->rooms as $room)
                                    @foreach ($room->beds as $bed)
                                        <option value="{{ $bed->id }}">
                                            Room {{ $room->room_number }} ({{ $room->room_type }}) - Bed {{ $bed->bed_number }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="core1-btn core1-btn-primary">
                    <i class="fas fa-check"></i>
                    <span class="pl-20">Confirm Admission</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
