@extends('admin.layouts.kiosk')

@section('content')
<div class="hr3-qr-wrapper flex flex-row items-center justify-between gap-12">
    
    <div class="hr3-qr-content-left flex-1 min-w-[400px]">
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-6">
                <i class="bi bi-hospital text-blue-600" style="font-size: 3.5rem;"></i>
                <div class="h-10 w-1 bg-slate-200 rounded-full"></div>
                <div>
                    <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-0">System Time</p>
                    <h3 id="live-clock" class="text-xl font-bold text-slate-700">00:00:00</h3>
                </div>
            </div>
            
            <h1 class="text-5xl font-black text-slate-800 leading-tight">
                Concord <span class="text-blue-600">Attendance</span> 
            </h1>
            <p class="text-xl text-slate-500 mt-4 font-medium">
                Welcome to the Main Ward Entrance. Please scan the qr code to log your attendance.
            </p>
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <i class="bi bi-check2-circle text-blue-600 text-xl"></i>
                <span class="text-slate-700 font-bold">Open your logged in mobile HR portal</span>
            </div>
            <div class="flex items-center gap-3">
                <i class="bi bi-check2-circle text-blue-600 text-xl"></i>
                <span class="text-slate-700 font-bold">Point camera at the QR on the right</span>
            </div>
        </div>

    </div>

    <div class="hr3-qr-section-right flex-shrink-0">
        <div class="hr3-qr-frame shadow-2xl">
            <img src="{{ $qrCodeUrl }}" alt="Scan QR" class="hr3-qr-image">
        </div>

        <div class="mt-8 text-center">
            <div class="flex items-center justify-center mb-3">
                <span class="hr3-status-pulse"></span>
                <span class="text-uppercase small fw-bold text-secondary tracking-widest">Secure Live Session</span>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase mb-1">Refreshing in</p>
            <h2 class="text-6xl font-black text-blue-600 tracking-tighter">
                <span id="timer">60</span>s
            </h2>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Timer Logic
        let timeLeft = 60;
        const timerEl = document.getElementById('timer');
        
        const countdown = setInterval(() => {
            timeLeft--;
            if(timerEl) timerEl.innerText = timeLeft;
            
            if(timeLeft <= 10) {
                timerEl.parentElement.style.color = '#ef4444'; 
            }

            if(timeLeft <= 0) {
                clearInterval(countdown);
                window.location.reload();
            }
        }, 1000);

        // 2. Live Clock Logic
        const clockEl = document.getElementById('live-clock');
        function updateClock() {
            const now = new Date();
            if(clockEl) clockEl.innerText = now.toLocaleTimeString([], { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();
    });
</script>
@endpush