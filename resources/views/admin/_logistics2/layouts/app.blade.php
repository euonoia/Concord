<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard">

<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        ☰
    </button>
    <div class="title">{{ request()->is('admin/logistics2*') ? 'Logistics 2' : 'Logistics 1' }}</div>
</div>

<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <div class="logo-text">HRMS</div>
    </div>
<nav>
    <a href="{{ route('admin.logistics2.dashboard') }}"
       class="{{ request()->routeIs('admin.logistics2.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Dashboard</span>
    </a>

    <div class="nav-dropdown {{ request()->is('admin/logistics2/vendor*') ? 'open' : '' }}">
        <a href="#" onclick="toggleDropdown(event)">
            <i class="bi bi-truck-flatbed"></i>
            <span>Vendor Portal</span>
            <i class="bi bi-chevron-down arrow-icon"></i>
        </a>
        <div class="dropdown-container">
            <a href="{{ route('admin.logistics2.vendor.index') }}" 
               class="sub-link {{ request()->routeIs('admin.logistics2.vendor.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>Procurement Requests</span>
            </a>
        </div>
    </div>

    <a href="{{ route('admin.logistics2.vehicle.index') }}" 
       class="{{ request()->routeIs('admin.logistics2.vehicle.index') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i>
        <span>Reservations</span>
    </a>

    <a href="{{ route('admin.logistics2.fleet.index') }}" 
       class="{{ request()->is('admin/logistics2/fleet*') ? 'active' : '' }}">
        <i class="bi bi-bus-front"></i>
        <span>Fleet Management</span>
    </a>

    <a href="{{ route('admin.logistics2.audit.index') }}"
       class="{{ request()->routeIs('admin.logistics2.audit.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i>
        <span>Audit Logs</span>
    </a>

    <div class="nav-dropdown {{ request()->is('admin/logistics2/document*') ? 'open' : '' }}">
    <a href="#" onclick="toggleDropdown(event)">
        <i class="bi bi-folder2-open"></i>
        <span>Document Tracking</span>
        <i class="bi bi-chevron-down arrow-icon"></i>
    </a>

    <div class="dropdown-container">
        <a href="{{ route('admin.logistics2.document.index') }}" 
           class="sub-link {{ request()->routeIs('admin.logistics2.document.index') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-medical"></i>
            <span>Lab Documents</span>
        </a>
         <!-- Diet Orders -->
        <a href="{{ route('admin.logistics2.document.diet') }}" 
           class="sub-link {{ request()->routeIs('admin.logistics2.document.diet') ? 'active' : '' }}">
            <i class="bi bi-cup-hot"></i>
            <span>Diet Documents</span>
        </a>
         <a href="{{ route('admin.logistics2.document.surgery') }}" 
           class="sub-link {{ request()->routeIs('admin.logistics2.document.surgery') ? 'active' : '' }}">
            <i class="bi bi-heart-pulse"></i>
            <span>Surgery Documents</span>
        </a>

    </div>
    
    </div>

    <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
        @csrf
    </form>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </a>
</nav>
</div>

<div class="dashboard-main main">
    <div class="main-inner">
        @if(session('success'))
            <div class="alert alert-success" style="padding: 1rem; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="padding: 1rem; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                {{ session('error') }}
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