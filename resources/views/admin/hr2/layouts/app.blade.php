<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR2 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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
<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <a href="{{ route('admin.hr2.dashboard') }}" 
           class="{{ request()->routeIs('admin.hr2.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>
       {{-- Competencies Dropdown --}}
        <div class="nav-dropdown {{ request()->is('competencies*') || request()->is('onboarding-assessment*') ? 'open' : '' }}">
            <a href="#" onclick="toggleDropdown(event)">
                <i class="bi bi-lightbulb"></i>
                <span>Competencies</span>
                <i class="bi bi-chevron-down arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('competencies.index') }}" 
                class="sub-link {{ request()->routeIs('competencies.index') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Create Competency</span>
                </a>
                
                <a href="{{ route('onboarding.assessment.public') }}" 
                class="sub-link {{ request()->is('onboarding-assessment*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Competency Assessment</span>
                </a>
            </div>
        </div>

        {{-- Learning Dropdown --}}
        <div class="nav-dropdown {{ request()->is('admin/hr2/learning*') || request()->is('admin/hr2/enrolls*') ? 'open' : '' }}">
            <a href="#" onclick="toggleDropdown(event)">
                <i class="bi bi-book"></i>
                <span>Learning</span>
                <i class="bi bi-chevron-down arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('learning.index') }}" 
                   class="sub-link {{ request()->routeIs('learning.index') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i>
                    <span>Learning Modules</span>
                </a>
                <a href="{{ route('hr2.learning.enroll') }}" 
                   class="sub-link {{ request()->routeIs('hr2.learning.enroll') ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Assign Module</span>
                </a>
            </div>
        </div>


        <a href="{{ route('succession.index') }}" 
           class="{{ request()->routeIs('succession.*') ? 'active' : '' }}">
            <i class="bi bi-tree"></i>
            <span>Succession</span>
        </a>
        
        <a href="{{ route('hr2.training') }}" 
           class="{{ request()->routeIs('training.*') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i>
            <span>Training</span>
        </a>

        <a href="{{ route('ess.index') }}" 
           class="{{ request()->routeIs('ess.*') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i>
            <span>ESS</span>
        </a>

        <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
            @csrf
        </form>
        <a href="#" class="logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<<div class="dashboard-main main unique-hr2-container">
    <div class="main-inner hr2-content-wrapper">
        @yield('content')
    </div>
</div>

<style>
    /* Removes all background, borders, and shadows from the HR2 layout */
    .unique-hr2-container, 
    .hr2-content-wrapper {
        background: none !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 20px; /* Kept for spacing, remove if you want it flush to the edge */
    }

    /* This ensures any default dashboard styling doesn't override the 'no background' rule */
    .dashboard-main {
        background: transparent !important;
    }
</style>
</body>


<script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.currentTarget.parentElement;
    parent.classList.toggle('open');
}

// Sidebar behavior (collapsed/hover)
const sidebar = document.getElementById('sidebar');
if (window.innerWidth > 768) sidebar.classList.add('collapsed');

sidebar.addEventListener('mouseenter', () => {
    if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
});

sidebar.addEventListener('mouseleave', () => {
    if (window.innerWidth > 768) sidebar.classList.add('collapsed');
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