@extends('layouts.dashboard.app')

@section('page', 'hr/hr3/station-scanner')

@section('content')
<div class="flex flex-col items-center justify-center py-10 px-4">
    <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl w-full max-w-md border border-slate-100 text-center">
        
        <div class="mb-8">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-qr-code-scan text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800">Station Scanner</h2>
            <p class="text-slate-500 font-medium">Point your camera at the tablet screen</p>
        </div>

        <div 
            class="relative overflow-hidden rounded-3xl border-4 border-blue-600 bg-slate-900 aspect-square mb-8"
            id="reader-wrapper"
        >
            <div id="reader" class="w-full h-full"></div>

            <div 
                id="scan-line"
                class="absolute top-0 left-0 w-full h-1 bg-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.8)] z-10 hidden animate-scan"
            ></div>
        </div>

        <div 
            id="feedback"
            class="hidden p-4 rounded-2xl mb-6 text-sm font-bold"
        ></div>

        <button 
            id="start-btn"
            class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-lg shadow-lg active:scale-95 transition-all"
        >
            <i class="bi bi-camera me-2"></i>
            Open Camera
        </button>
    </div>
</div>

<style>
@keyframes scan-anim {
    0% { top: 0%; }
    100% { top: 100%; }
}
.animate-scan {
    animation: scan-anim 2s linear infinite;
}
#reader video {
    object-fit: cover !important;
}
</style>
@endsection