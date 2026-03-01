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
        </div>

        <div id="feedback" class="hr3-feedback hidden p-4 rounded-2xl mb-6 text-sm"></div>

        <button id="start-btn"
            class="hr3-btn-start w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-lg shadow-xl hover:bg-blue-600 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3">
            <i class="bi bi-camera-fill"></i>
            Initialize Camera
        </button>
    </div>
</div>

<style>
.hr3-scanner-container {
    min-height: 80vh;
    background: #f9fafb;
}

.hr3-card {
    position: relative;
    transition: transform 0.3s ease;
}

.hr3-card:hover {
    transform: translateY(-4px);
}

.hr3-reader-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 2rem;
    border: 6px solid #f1f5f9;
    background: #1e293b;
    aspect-ratio: 1 / 1;
}

.hr3-reader-view {
    width: 100%;
    height: 100%;
}

.hr3-scan-line {
    height: 4px;
    background: linear-gradient(90deg, rgba(0,255,255,0.7), rgba(0,255,255,0.3));
    top: 0;
    left: 0;
    position: absolute;
    animation: scan 2s linear infinite;
}

@keyframes scan {
    0% { top: 0%; }
    100% { top: 100%; }
}

.hr3-feedback {
    min-height: 40px;
    text-align: center;
}

.hr3-btn-start {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.hr3-btn-start:hover {
    background-color: #3b82f6; /* blue-600 */
    transform: scale(1.02);
}

.hr3-btn-start:active {
    transform: scale(0.98);
}
</style>
@push('scripts')
<script type="module">
import { Html5Qrcode } from '/node_modules/html5-qrcode/minified/html5-qrcode.min.js';

export default function initAttendanceScanner() {
    const startBtn = document.getElementById('start-btn');
    const scanLine = document.getElementById('scan-line');
    const feedback = document.getElementById('feedback');

    if (!startBtn) return;

    let html5QrCode = null;
    let activeCameraId = null;

    startBtn.addEventListener('click', async () => {
        startBtn.disabled = true;
        startBtn.style.display = 'none';
        scanLine?.classList.remove('hidden');

        html5QrCode = new Html5Qrcode("reader");

        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || cameras.length === 0) throw new Error("No camera devices found.");

            const backCamera = cameras.find(cam =>
                cam.label.toLowerCase().includes('back') ||
                cam.label.toLowerCase().includes('rear')
            );
            activeCameraId = backCamera ? backCamera.id : cameras[0].id;

            await html5QrCode.start(
                activeCameraId,
                { fps: 15, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                async (decodedText) => {
                    await html5QrCode.stop();
                    scanLine?.classList.add('hidden');
                    handleScan(decodedText);
                }
            );
        } catch (err) {
            console.error(err);
            alert("Camera failed to start.\n\n• HTTPS required\n• Allow camera permissions\n• Close other apps using the camera");
            resetScanner();
        }
    });

    async function handleScan(decodedText) {
        feedback?.classList.remove('hidden');
        feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-blue-50 text-blue-700 animate-pulse";
        feedback.innerText = "Verifying Attendance...";

        try {
            // Extract station and token from scanned QR (assume QR encodes JSON or query string)
            // Example QR: {"station": 1, "token": "uuid-string"}
            let payload;
            try {
                payload = JSON.parse(decodedText);
            } catch (e) {
                throw new Error("Invalid QR code format");
            }

            const response = await fetch("/hr/hr3/attendance/verify", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    station: payload.station,
                    token: payload.token
                })
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.message || "Invalid QR Code");

            // Success feedback
            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-emerald-50 text-emerald-700";
            feedback.innerText = "Success! Attendance Recorded.";
            if (navigator.vibrate) navigator.vibrate(200);

            setTimeout(() => window.location.href = "/hr/dashboard", 2000);

        } catch (err) {
            feedback.className = "p-4 rounded-2xl mb-6 text-sm font-bold bg-red-50 text-red-700";
            feedback.innerText = err.message;
            resetScanner();
        }
    }

    function resetScanner() {
        startBtn.disabled = false;
        startBtn.style.display = 'block';
        scanLine?.classList.add('hidden');
    }
}

// Initialize scanner
document.addEventListener('DOMContentLoaded', () => {
    initAttendanceScanner();
});
</script>
@endpush
@endsection