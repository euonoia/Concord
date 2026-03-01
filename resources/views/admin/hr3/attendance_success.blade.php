@extends('admin.layouts.kiosk')

@section('content')
<div class="qr-container">
    <div class="qr-card">
        <div class="mb-4">
            <i class="bi bi-hospital text-primary" style="font-size: 3rem;"></i>
            <h1 class="fw-bold h3 mt-3">Staff Clock-In</h1>
            <p class="text-muted">Scan the QR code below using your mobile portal</p>
        </div>

        <div class="qr-frame shadow-sm">
            <img src="{{ $qrCodeUrl }}" alt="Scan QR">
        </div>

        <div class="mt-5">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <span class="qr-status-pulse"></span>
                <span class="text-uppercase small fw-bold text-secondary">Secure Live Session</span>
            </div>
            <h4 class="text-primary fw-bold">Refreshing in <span id="timer">60</span>s</h4>
        </div>
        
        <div class="mt-3 py-2 px-4 bg-light rounded-pill d-inline-block border">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <span class="small fw-bold">Location: Main Hospital Entrance</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let timeLeft = 60;
    setInterval(() => {
        timeLeft--;
        document.getElementById('timer').innerText = timeLeft;
        if(timeLeft <= 0) window.location.reload();
    }, 1000);
</script>
@endpush