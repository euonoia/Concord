<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR2 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard">

<!-- Mobile Topbar -->
<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        ☰
    </button>
    <div class="title">HR2</div>
</div>

<!-- Sidebar -->
<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <a href="{{ route('admin.hr3.dashboard') }}" class="{{ request()->routeIs('admin.hr3.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('shifts.index') }}" class="{{ request()->routeIs('shifts.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-range"></i>
            <span>Shifts</span>
        </a>

        <div class="nav-dropdown {{ (request()->routeIs('schedule.*') || request()->routeIs('training_schedule.*')) ? 'open' : '' }}">
            <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(this)">
                <i class="bi bi-calendar-event"></i>
                <span>Schedule</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('schedule.index') }}" class="sub-link {{ request()->routeIs('schedule.index') ? 'active' : '' }}">
                    <i class="bi bi-person-video3"></i>
                    <span>Interview Schedule</span>
                </a>
                
                <a href="{{ route('training_schedule.index') }}" class="sub-link {{ request()->routeIs('training_schedule.index') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Training Schedule</span>
                </a>
            </div>
        </div>

        <a href="{{ route('timesheet.index') }}" class="{{ request()->routeIs('timesheet.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Timesheet</span>
        </a>

        <a href="{{ route('admin.hr3.leave.index') }}" class="{{ request()->routeIs('admin.hr3.leave.*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check"></i>
            <span>Leave Management</span>
        </a>
        <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
            @csrf
        </form>

        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="dashboard-main main">
    <div class="main-inner">
        @yield('content')
    </div>
</div>

</body>


<script>
const sidebar = document.getElementById('sidebar');

// Function to toggle the dropdown
function toggleDropdown(element) {
    const parent = element.parentElement;
    parent.classList.toggle('open');
}

// default collapsed on desktop
if (window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
}

// hover expand (desktop)
sidebar.addEventListener('mouseenter', () => {
    if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        // Optional: Close all dropdowns when mouse leaves
        document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
    }
});

// close sidebar on mobile click outside
document.addEventListener('click', (e) => {
    const toggle = document.querySelector('.menu-toggle');
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});
</script>

</body>
</html>