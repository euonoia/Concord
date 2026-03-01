@extends('layouts.dashboard.app')

@section('page', 'hr/hr3/station-scanner')

@section('content')
<div class="hr3-scanner-container flex flex-col items-center justify-center py-10 px-4">
    <div class="hr3-card bg-white p-8 rounded-[2.5rem] shadow-2xl w-full max-w-md border border-slate-100 text-center">
        <div class="mb-8">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Attendance Scanner</h2>
            <p class="text-slate-400 font-medium mt-1">HR3 Monitoring System</p>
        </div>

        <div class="hr3-reader-wrapper relative overflow-hidden rounded-[2rem] border-[6px] border-slate-50 bg-slate-900 aspect-square mb-8 shadow-inner">
            <div id="reader" class="hr3-reader-view w-full h-full"></div>
            <div id="scan-line" class="hr3-scan-line absolute left-0 w-full z-10 hidden animate-hr3-scan"></div>
            <div class="absolute top-4 left-4 w-6 h-6 border-t-2 border-l-2 border-blue-400 opacity-50"></div>
            <div class="absolute top-4 right-4 w-6 h-6 border-t-2 border-r-2 border-blue-400 opacity-50"></div>
            <div class="absolute bottom-4 left-4 w-6 h-6 border-b-2 border-l-2 border-blue-400 opacity-50"></div>
            <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-blue-400 opacity-50"></div>
        </div>

        <div id="feedback" class="hr3-feedback hidden p-4 rounded-2xl mb-6 text-sm"></div>

        <button id="start-btn"
            class="hr3-btn-start w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-lg shadow-xl hover:bg-blue-600 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3">
            <i class="bi bi-camera-fill"></i>
            Initialize Camera
        </button>
    </div>
</div>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
.hr3-scanner-container {
    background: radial-gradient(circle at top right, #f8fafc, #f1f5f9);
    min-height: 80vh;
}
.hr3-card { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); }
.hr3-scan-line {
    height: 4px;
    background: linear-gradient(90deg, transparent, #60a5fa, #ffffff, #60a5fa, transparent);
    box-shadow: 0 0 20px rgba(59,130,246,0.8);
    filter: blur(1px);
}
@keyframes hr3-scan-anim { 0%{top:0%;opacity:0;}10%{opacity:1;}90%{opacity:1;}100%{top:100%;opacity:0;} }
.animate-hr3-scan { animation: hr3-scan-anim 2.5s ease-in-out infinite; }
.hr3-feedback { transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); border:1px solid rgba(0,0,0,0.05); backdrop-filter: blur(4px); }
.hr3-reader-view video { object-fit: cover !important; filter: contrast(1.1) brightness(1.1); }
.hr3-icon-wrapper i { animation: hr3-icon-pulse 3s infinite; }
@keyframes hr3-icon-pulse { 0%,100%{transform:scale(1);opacity:1;}50%{transform:scale(1.1);opacity:0.8;} }
</style>

@endsection

@push('scripts')
<script type="module">
    import initAttendanceScanner from '/resources/js/pages/hr/hr3/station-scanner.js';
    document.addEventListener('DOMContentLoaded', () => {
        initAttendanceScanner();
    });
</script>
@endpush