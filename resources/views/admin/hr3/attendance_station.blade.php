@extends('admin.layouts.kiosk')

@section('content')
<div class="hr3-kiosk-container flex items-center justify-center min-h-screen bg-slate-50 px-6">
    <div class="hr3-qr-wrapper flex flex-row items-center justify-between gap-20 max-w-6xl w-full">
        
        {{-- Left content --}}
        <div class="hr3-qr-content-left flex-1">
            <div class="mb-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="hr3-icon-box p-3 bg-white rounded-2xl shadow-sm border border-slate-100">
                        <i class="bi bi-hospital text-blue-600" style="font-size: 3rem;"></i>
                    </div>
                    <div class="h-12 w-1 bg-slate-200 rounded-full"></div>
                    <div>
                        <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-0">System Time</p>
                        <h3 id="live-clock" class="text-2xl font-bold text-slate-700 tabular-nums">00:00:00</h3>
                    </div>
                </div>
                
                <h1 class="text-6xl font-black text-slate-800 leading-tight">
                    Concord <br><span class="text-blue-600">Attendance</span> 
                </h1>
                <p class="text-xl text-slate-500 mt-6 font-medium max-w-md">
                    Please scan the unique QR code with your mobile device to securely log your attendance.
                </p>
            </div>

            <div class="space-y-5">
                <div class="hr3-step flex items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-50">
                    <i class="bi bi-1-circle-fill text-blue-600 text-2xl"></i>
                    <span class="text-slate-700 font-bold">Open your logged in mobile HR portal</span>
                </div>
                <div class="hr3-step flex items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-50">
                    <i class="bi bi-2-circle-fill text-blue-600 text-2xl"></i>
                    <span class="text-slate-700 font-bold">Point camera at the QR on the right</span>
                </div>
            </div>
        </div>

        {{-- Right QR --}}
        <div class="hr3-qr-section-right flex-shrink-0 flex flex-col items-center">
            <div class="hr3-qr-frame-outer p-6 bg-white rounded-[3rem] shadow-2xl border border-slate-100">
                <div class="hr3-qr-image w-80 h-80 flex items-center justify-center">
                  {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(320)->generate($token) !!}
                </div>
            </div>

            <div class="mt-10 text-center">
                <div class="flex items-center justify-center gap-3 mb-3">
                    <span class="hr3-status-pulse"></span>
                    <span class="text-uppercase small fw-bold text-slate-400 tracking-[0.2em]">Secure Live Session</span>
                </div>
                <p class="text-slate-400 text-xs font-bold uppercase mb-1">Refreshing in</p>
                <h2 class="text-7xl font-black text-blue-600 tracking-tighter tabular-nums">
                    <span id="timer">30</span><span class="text-4xl text-slate-300 ml-1">s</span>
                </h2>
            </div>
        </div>

    </div>
</div>

<style>
    body { background-color: #f8fafc; overflow: hidden; }
    .hr3-kiosk-container { padding: 4rem; }
    .hr3-qr-frame-outer { transition: transform 0.3s ease; }
    .hr3-status-pulse {
        width: 10px; height: 10px; background-color: #22c55e;
        border-radius: 50%; display: inline-block;
        box-shadow: 0 0 0 rgba(34,197,94,0.4);
        animation: hr3-pulse 2s infinite;
    }
    @keyframes hr3-pulse {
        0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34,197,94,0); }
        100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
    }
    .tabular-nums { font-variant-numeric: tabular-nums; }
    @media (max-width:1024px) {
        .hr3-qr-wrapper { flex-direction: column; text-align: center; gap:3rem; }
        .hr3-qr-content-left { min-width: unset; }
        .hr3-qr-content-left .flex { justify-content: center; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live clock
    const clockEl = document.getElementById('live-clock');
    function updateClock() {
        const now = new Date();
        if(clockEl) {
            clockEl.innerText = now.toLocaleTimeString('en-US', { hour12:true, hour:'2-digit', minute:'2-digit', second:'2-digit' });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Auto-refresh timer
    let timeLeft = 30;
    const timerEl = document.getElementById('timer');
    const countdown = setInterval(() => {
        timeLeft--;
        if(timerEl) timerEl.innerText = timeLeft;
        if(timeLeft <= 10) timerEl.style.color = '#ef4444';
        if(timeLeft <= 0) { clearInterval(countdown); window.location.reload(); }
    }, 1000);
});
</script>
@endsection