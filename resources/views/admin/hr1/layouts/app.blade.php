<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR1 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard">

<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        ☰
    </button>
    <div class="title">HR1 - Recruitment & Performance</div>
</div>

<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <a href="{{ route('admin.hr1.dashboard') }}" class="{{ request()->routeIs('admin.hr1.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('hr1.applicants.index') }}" class="{{ request()->routeIs('hr1.applicants.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Applicant Management</span>
        </a>

        <a href="{{ route('hr1.newhires.index') }}" class="{{ request()->routeIs('hr1.newhires.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i>
            <span>New Hires</span>
        </a>

        <div class="nav-dropdown {{ (request()->routeIs('hr1.training.performance.*') || request()->routeIs('hr1.assessment.performance.*')) ? 'open' : '' }}">
            <a href="javascript:void(0)" class="dropdown-toggle" onclick="toggleDropdown(this)">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Performance</span>
                <i class="bi bi-chevron-down ms-auto arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('hr1.assessment.performance.index') }}" 
                   class="sub-link {{ request()->routeIs('hr1.assessment.performance.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Assessment Scores</span>
                </a>
                <a href="{{ route('hr1.training.performance.index') }}" 
                   class="sub-link {{ request()->routeIs('hr1.training.performance.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Training Performance</span>
                </a>
            </div>
        </div>

        <a href="{{ route('hr1.recruitment.index') }}" class="{{ request()->routeIs('hr1.recruitment.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i>
            <span>Recruitment</span>
        </a>

        <a href="{{ route('admin.hr1.recognition.index') }}" class="{{ request()->routeIs('admin.hr1.recognition.*') ? 'active' : '' }}">
            <i class="bi bi-patch-check"></i>
            <span>Social Recognition</span>
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

<div class="dashboard-main main">
    <div class="main-inner">
        @yield('content')
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');

// Logic for Toggling Dropdowns
function toggleDropdown(element) {
    const parent = element.parentElement;
    parent.classList.toggle('open');
}

// Logic for Sidebar Hover/Mobile
if (window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
}

sidebar.addEventListener('mouseenter', () => {
    if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        // Close dropdowns on leave to keep it tidy
        document.querySelectorAll('.nav-dropdown').forEach(d => d.classList.remove('open'));
    }
});

document.addEventListener('click', (e) => {
    const toggle = document.querySelector('.menu-toggle');
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});
</script>

</body>
</html>