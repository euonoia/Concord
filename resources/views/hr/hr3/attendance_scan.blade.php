@extends('layouts.dashboard.app')

@section('page', 'hr/hr3/station-scanner')

@section('content')
<div class="scanner-container flex flex-col items-center justify-center py-10 px-4">
    <div class="scanner-card bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center">
        <h2 class="text-3xl font-black text-slate-800">Attendance Scanner</h2>
        <p class="text-slate-400 mt-1">HR3 Monitoring System</p>

        <div class="reader-wrapper relative mt-8 aspect-square w-full rounded-2xl border-6 border-slate-50 bg-slate-900">
            <div id="reader" class="w-full h-full"></div>
            <div id="scan-line" class="absolute w-full h-1 bg-cyan-400 top-0 hidden animate-scan"></div>
        </div>

        <div id="feedback" class="hidden p-4 rounded-2xl mt-6 text-sm"></div>

        <button id="start-btn" class="mt-4 w-full py-3 bg-slate-900 text-white rounded-2xl font-bold">
            Initialize Camera
        </button>
    </div>
</div>

<style>
@keyframes scan { 0% { top:0; } 100% { top:100%; } }
#scan-line { animation: scan 2s linear infinite; }
</style>
@endsection