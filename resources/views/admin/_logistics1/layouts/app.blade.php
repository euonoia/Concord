<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics 1 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (REQUIRED for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard">

<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        ☰
    </button>
    <div class="title">Logistics 1</div>
</div>

<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <a href="{{ route('admin.logistics1.dashboard') }}"
           class="{{ request()->routeIs('admin.logistics1.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.logistics1.procurement.index') }}"
           class="{{ request()->routeIs('admin.logistics1.procurement.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i>
            <span>Procurement</span>
        </a>

        <a href="{{ route('admin.logistics1.project_management.index') }}"
           class="{{ request()->routeIs('admin.logistics1.project_management.*') ? 'active' : '' }}">
            <i class="bi bi-kanban"></i>
            <span>Project Management</span>
        </a>

        <a href="{{ route('admin.logistics1.asset_management.index') }}"
           class="{{ request()->routeIs('admin.logistics1.asset_management.*') ? 'active' : '' }}">
            <i class="bi bi-boxes"></i>
            <span>Asset Management</span>
        </a>

        <a href="{{ route('admin.logistics1.maintenance.index') }}"
           class="{{ request()->routeIs('admin.logistics1.maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools"></i>
            <span>MRO</span>
        </a>

        <a href="{{ route('admin.logistics1.warehouse.index') }}"
           class="{{ request()->routeIs('admin.logistics1.warehouse.*') ? 'active' : '' }}">
            <i class="bi bi-archive"></i>
            <span>Warehousing</span>
        </a>

        <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="margin-top: auto;">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<div class="dashboard-main main">
    <div class="main-inner">
        {{-- Alert Messages --}}
        @if(session('success'))
            <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');

// Default collapsed on desktop
if (window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
}

// Hover expand (desktop)
sidebar.addEventListener('mouseenter', () => {
    if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (window.innerWidth > 768) sidebar.classList.add('collapsed');
});

// Close sidebar on mobile click outside
document.addEventListener('click', (e) => {
    const toggle = document.querySelector('.menu-toggle');
    if (sidebar && !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});

function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.currentTarget.parentElement;
    parent.classList.toggle('open');
}
</script>
</body>
</html>