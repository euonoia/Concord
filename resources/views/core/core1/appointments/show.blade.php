@extends('layouts.core1.layouts.app')

@section('title', 'Appointment Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
            <p class="text-gray-600 mt-1">View or edit appointment information</p>
        </div>
        <a href="{{ route('core1.appointments.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 max-w-2xl">
        <form action="{{ route('core1.appointments.update', $appointment) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    @if(auth()->user()->role !== 'doctor')
        <div>
            <label>Patient</label>
            <select name="patient_id" class="core1-input">
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ $appointment->patient_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Doctor</label>
            <select name="doctor_id" class="core1-input">
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ $appointment->doctor_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Date</label>
                <input type="date" name="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d') }}" class="core1-input">
            </div>
            <div>
                <label>Time</label>
                <input type="time" name="appointment_time" value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}" class="core1-input">
            </div>
        </div>

        <div>
            <label>Type</label>
            <input type="text" name="type" value="{{ $appointment->type }}" class="core1-input">
        </div>

        <div>
            <label>Reason</label>
            <textarea name="reason" class="core1-input">{{ $appointment->reason }}</textarea>
        </div>

        <div>
            <label>Notes</label>
            <textarea name="notes" class="core1-input">{{ $appointment->notes }}</textarea>
        </div>
    @endif

    @if(auth()->user()->role === 'doctor')
        <div>
            <label>Status</label>
            <select name="status" class="core1-input">
                @foreach(['pending','scheduled','declined','completed','cancelled'] as $status)
                    <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <button type="submit" class="core1-btn core1-btn-primary">Update</button>
</form>

    </div>
</div>
@endsection
