@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-white px-6 text-center">
    <div class="mb-6 text-emerald-500">
        <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
    </div>
    
    <h1 class="text-3xl font-bold text-slate-800 mb-2">Attendance Logged!</h1>
    <p class="text-slate-500 mb-8 text-lg">Your clock-in has been recorded successfully for today.</p>

    <div class="bg-slate-50 p-4 rounded-2xl w-full max-auto mb-8 border border-slate-100">
        <p class="text-xs text-slate-400 uppercase font-bold tracking-widest mb-1">Time Recorded</p>
        <p class="text-xl font-mono font-bold text-slate-700">{{ now()->format('h:i A') }}</p>
    </div>

    <a href="{{ route('hr.dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200">
        Back to Dashboard
    </a>
</div>
@endsection