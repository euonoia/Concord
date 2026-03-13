<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR4 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Small sidebar submenu styles for CoreHuman */
        .sidebar nav .nav-parent { position: relative; }
        .sidebar nav .nav-toggle { display:flex; align-items:center; gap:8px; cursor:pointer; padding:8px 12px; color:inherit; text-decoration:none; }
        .sidebar nav .nav-toggle .chevron { margin-left:auto; transition:transform .2s ease; }
        .sidebar nav .submenu { display:none; padding-left:14px; border-left:2px solid rgba(255,255,255,0.03); margin-left:6px; }
        .sidebar nav .submenu.open { display:block; }
        .sidebar nav .submenu a { display:block; padding:6px 10px; color:inherit; text-decoration:none; font-size:0.95rem; }
        .sidebar nav a.active, .sidebar nav .submenu a.active { background: rgba(255,255,255,0.04); border-radius:4px; }
        .sidebar nav .nav-toggle.open .chevron { transform:rotate(180deg); }
    </style>
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
        <a href="{{ route('admin.hr4.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-parent">
            <a href="#" id="corehumanToggle" class="nav-toggle {{ request()->routeIs('hr4.core') ? 'active' : '' }}" onclick="toggleCoreHumanSubmenu(event)">
                <i class="bi bi-diagram-3"></i>
                <span>CoreHuman</span>
                <i class="bi bi-chevron-down chevron"></i>
            </a>

            <div class="submenu" id="corehumanSubmenu" aria-hidden="true">
                <a href="{{ route('hr4.core') }}" class="">Overview</a>
                <a href="{{ route('hr4.core') }}#employees">Employees</a>
                <a href="{{ route('hr4.core') }}#departments">Departments</a>
                <a href="{{ route('hr4.core') }}#positions">Positions</a>
                <a href="{{ route('hr4.core') }}#available">Available Specializations</a>
            </div>
        </div>

        <a href="{{ route('hr4.direct_compensation.index') }}" class="{{ request()->routeIs('hr4.direct_compensation.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Direct Compensation</span>
        </a>

        <hr style="border: 0; border-top: 1px solid #333; margin: 10px 0;">

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
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});

// CoreHuman submenu toggle and auto-open
function toggleCoreHumanSubmenu(e) {
    e.preventDefault();
    const submenu = document.getElementById('corehumanSubmenu');
    const toggle = document.getElementById('corehumanToggle');
    const open = submenu.classList.toggle('open');
    toggle.classList.toggle('open', open);
    submenu.setAttribute('aria-hidden', !open);
}

function closeCoreHumanSubmenu() {
    const submenu = document.getElementById('corehumanSubmenu');
    const toggle = document.getElementById('corehumanToggle');
    if (!submenu) return;
    submenu.classList.remove('open');
    toggle && toggle.classList.remove('open');
    submenu.setAttribute('aria-hidden', 'true');
}

document.addEventListener('DOMContentLoaded', function () {
    const submenu = document.getElementById('corehumanSubmenu');
    const toggle = document.getElementById('corehumanToggle');

    // If we're on the core-human page (path) or there's a hash for its tabs, open submenu
    if (location.pathname.includes('/hr4/core-human-capital') || location.hash) {
        submenu.classList.add('open');
        toggle.classList.add('open');
        submenu.setAttribute('aria-hidden', 'false');
    }

    // Highlight submenu link if it matches current hash/path
    try {
        const links = submenu.querySelectorAll('a');
        links.forEach(a => {
            const aUrl = new URL(a.href, location.origin);
            if (aUrl.pathname === location.pathname && (aUrl.hash === location.hash || (!aUrl.hash && !location.hash))) {
                a.classList.add('active');
            }
            if (aUrl.hash && aUrl.hash === location.hash) {
                a.classList.add('active');
            }
        });
    } catch (err) {
        // ignore URL parsing errors
    }

    // Watch for sidebar class changes (collapse/show) and close submenu when sidebar is collapsed or hidden
    try {
        const obs = new MutationObserver(() => {
            if (!sidebar) return;
            // if sidebar collapsed (desktop) or hidden (mobile), ensure submenu closed
            if (sidebar.classList.contains('collapsed') || !sidebar.classList.contains('show')) {
                closeCoreHumanSubmenu();
            }
        });
        obs.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    } catch (e) {
        // ignore
    }
});
</script>

</body>
</html>