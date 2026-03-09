<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hospital Management System')</title>
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>

@auth
<!-- Mobile Topbar -->
<div class="topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.sidebar').classList.toggle('show')">
        â˜°
    </button>
    <div class="title">Concord</div>
</div>

<!-- Sidebar -->
@include('core.core1.components.navigation')

<!-- Main Content -->
<div class="main">
    <div class="main-inner">
        @if(session('success'))
            <div class="alert alert-success session-alert d-flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error session-alert d-flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="alert alert-error session-alert">
                <div class="d-flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="font-bold">Please correct the following:</span>
                </div>
                <ul class="m-0 pl-20">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');

// default collapsed on desktop
if (window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
}

// hover expand (desktop)
sidebar.addEventListener('mouseenter', () => {
    if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (window.innerWidth > 768) sidebar.classList.add('collapsed');
});

// close sidebar on mobile click outside
document.addEventListener('click', (e) => {
    const toggle = document.querySelector('.menu-toggle');
    if (!sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});

// Auto-hide session alerts after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.session-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500); // Wait for transition
        }, 5000);
    });
});
</script>

@else
    @if(session('success'))
        <div class="alert alert-success session-alert d-flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error session-alert d-flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="alert alert-error session-alert">
            <div class="d-flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-circle"></i>
                <span class="font-bold">Please correct the following:</span>
            </div>
            <ul class="m-0 pl-20">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
@endauth

@stack('scripts')
</body>
</html>
