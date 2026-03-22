<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR4 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap');

        :root {
            --sb-width:        240px;
            --sb-width-col:    64px;
            --sb-bg:           #0d1f2d;
            --sb-bg-hover:     #132333;
            --sb-bg-active:    #0a7c6e18;
            --sb-border:       #1e3244;
            --sb-accent:       #0a7c6e;
            --sb-accent-light: #0fd6bc;
            --sb-text:         #8baabb;
            --sb-text-active:  #e8f4f2;
            --sb-text-hover:   #c8dde8;
            --sb-icon-size:    1.1rem;
            --transition:      .25s cubic-bezier(.22,.68,0,1.2);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body.dashboard {
            font-family: 'DM Sans', sans-serif;
            background: #eef3f7;
            display: flex;
            min-height: 100vh;
        }

        /* ── Topbar (mobile) ── */
        .topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 52px;
            background: var(--sb-bg);
            border-bottom: 1px solid var(--sb-border);
            align-items: center;
            padding: 0 1rem;
            gap: .75rem;
            z-index: 200;
        }

        .menu-toggle {
            background: none;
            border: 1px solid var(--sb-border);
            color: var(--sb-text);
            width: 34px; height: 34px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background var(--transition), color var(--transition);
        }

        .menu-toggle:hover {
            background: var(--sb-bg-hover);
            color: var(--sb-text-active);
        }

        .topbar .title {
            font-size: .9rem;
            font-weight: 600;
            color: var(--sb-text-active);
            letter-spacing: .05em;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sb-width);
            height: 100vh;
            background: var(--sb-bg);
            border-right: 1px solid var(--sb-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width var(--transition);
            z-index: 100;
        }

        .sidebar.collapsed {
            width: var(--sb-width-col);
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid var(--sb-border);
            overflow: hidden;
            flex-shrink: 0;
        }

        .logo img {
            width: 36px; height: 36px;
            border-radius: 9px;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid var(--sb-border);
        }

        .logo-text {
            font-size: .85rem;
            font-weight: 700;
            color: var(--sb-text-active);
            letter-spacing: .12em;
            text-transform: uppercase;
            white-space: nowrap;
            opacity: 1;
            transition: opacity var(--transition);
        }

        .sidebar.collapsed .logo-text { opacity: 0; pointer-events: none; }

        /* Module label */
        .sb-module-label {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #3a5a70;
            padding: 1rem 1rem .4rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--transition);
        }

        .sidebar.collapsed .sb-module-label { opacity: 0; }

        /* Nav */
        nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: .5rem 0 1rem;
            scrollbar-width: none;
        }

        nav::-webkit-scrollbar { display: none; }

        /* Nav links */
        nav a, .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1rem;
            color: var(--primary);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            border-radius: 0;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
            transition: background var(--transition), color var(--transition);
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
        }

        nav a::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            border-radius: 0 3px 3px 0;
            background: var(--sb-accent);
            opacity: 0;
            transition: opacity var(--transition);
        }

        nav a:hover, .dropdown-toggle:hover {
            background: var(--sb-bg-hover);
            color: var(--sb-text-hover);
        }

        nav a.active {
            background: var(--sb-bg-active);
            color: var(--sb-text-active);
        }

        nav a.active::before { opacity: 1; }

        nav a i, .dropdown-toggle i:first-child {
            font-size: var(--sb-icon-size);
            flex-shrink: 0;
            width: 20px;
            text-align: center;
            transition: color var(--transition);
        }

        nav a.active i { color: var(--sb-accent-light); }

        nav a span, .dropdown-toggle span {
            opacity: 1;
            transition: opacity var(--transition);
            flex: 1;
        }

        .sidebar.collapsed nav a span,
        .sidebar.collapsed .dropdown-toggle span { opacity: 0; }

        /* Dropdown */
        .sidebar-dropdown { position: relative; }

        .dropdown-toggle {
            justify-content: flex-start;
        }

        .toggle-icon {
            font-size: .7rem !important;
            width: auto !important;
            margin-left: auto;
            transition: transform .25s ease, opacity var(--transition) !important;
        }

        .sidebar.collapsed .toggle-icon { opacity: 0; }
        .sidebar-dropdown.active .toggle-icon { transform: rotate(180deg); }

        .dropdown-menu {
            background: #e8f4f2;
            overflow: hidden;
            padding: 0;
            display: none;
        }

        .dropdown-item {
            display: flex !important;
            align-items: center;
            gap: .65rem;
            padding: .55rem 1rem .55rem 2.75rem !important;
            color: #3a5a70 !important;
            font-size: .8rem !important;
            font-weight: 400;
            text-decoration: none;
            transition: background var(--transition), color var(--transition), padding var(--transition) !important;
            white-space: nowrap;
            overflow: hidden;
        }

        .dropdown-item:hover {
            background: var(--sb-bg-hover) !important;
            color: var(--sb-text-hover) !important;
            padding-left: 3rem !important;
        }

        .dropdown-item i {
            font-size: .8rem;
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }

        .sidebar.collapsed .dropdown-menu { display: none !important; }

        /* Divider */
        .sb-divider {
            border: none;
            border-top: 1px solid var(--sb-border);
            margin: .5rem .75rem;
        }

        /* Logout */
        .sb-logout {
            color: #c0392b !important;
        }

        .sb-logout:hover {
            background: #1f0e0e !important;
            color: #e74c3c !important;
        }

        /* ── Main ── */
        .dashboard-main {
            margin-left: var(--sb-width-col);
            flex: 1;
            transition: margin-left var(--transition);
            min-height: 100vh;
        }

        .sidebar:not(.collapsed) ~ .dashboard-main {
            margin-left: var(--sb-width);
        }

        .main-inner {
            min-height: 100vh;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .topbar { display: flex; }

            .sidebar {
                width: var(--sb-width);
                transform: translateX(-100%);
                transition: transform var(--transition);
                top: 52px;
                height: calc(100vh - 52px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .dashboard-main {
                margin-left: 0;
                padding-top: 52px;
            }

            .sidebar:not(.collapsed) ~ .dashboard-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="dashboard">

<!-- Mobile Topbar -->
<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>
    <div class="title">HR4</div>
</div>

<!-- Sidebar -->
<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <div class="sb-module-label">Main</div>

        <a href="{{ route('admin.hr4.dashboard') }}" class="{{ request()->routeIs('admin.hr4.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <div class="sb-module-label">Modules</div>

        <!-- Core Human Capital Dropdown -->
        <div class="sidebar-dropdown">
            <div class="dropdown-toggle {{ request()->routeIs('hr4.core') ? 'active' : '' }}" onclick="toggleDropdown('core-dropdown')">
                <i class="bi bi-diagram-3"></i>
                <span>Core Human Capital</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
            </div>
            <div id="core-dropdown" class="dropdown-menu">
                <a href="{{ route('hr4.core') }}#employees" class="dropdown-item">
                    <i class="bi bi-people"></i>
                    <span>Employees</span>
                </a>
                <a href="{{ route('hr4.core') }}#departments" class="dropdown-item">
                    <i class="bi bi-building"></i>
                    <span>Departments</span>
                </a>
                <a href="{{ route('hr4.core') }}#positions" class="dropdown-item">
                    <i class="bi bi-briefcase"></i>
                    <span>Positions</span>
                </a>
                <a href="{{ route('hr4.core') }}#neededpositions" class="dropdown-item">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>Needed Positions</span>
                </a>
                <a href="{{ route('hr4.core') }}#availablejobs" class="dropdown-item">
                    <i class="bi bi-card-list"></i>
                    <span>Available Jobs</span>
                </a>
            </div>
        </div>

        <a href="{{ route('hr4.direct_compensation.index') }}" class="{{ request()->routeIs('hr4.direct_compensation.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Direct Compensation</span>
        </a>

        <a href="{{ route('hr4.payroll.index') }}" class="{{ request()->routeIs('hr4.payroll.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Payroll</span>
        </a>

        <a href="{{ route('hr4.ess_requests.index') }}" class="{{ request()->routeIs('hr4.ess_requests.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i>
            <span>Payroll Requests</span>
        </a>

        <a href="{{ route('hr4.job_postings.index') }}" class="{{ request()->routeIs('hr4.job_postings.*') ? 'active' : '' }}">
            <i class="bi bi-briefcase"></i>
            <span>Available Job</span>
        </a>

        <a href="{{ route('hr4.analytics.index') }}" class="{{ request()->routeIs('hr4.analytics.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>HR Analytics</span>
        </a>

        <hr class="sb-divider">

        <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
            @csrf
        </form>

        <a href="#" class="sb-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

<script>
    const sidebar = document.getElementById('sidebar');

    // Default collapsed on desktop
    if (window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
    }

    // Hover expand on desktop
    sidebar.addEventListener('mouseenter', () => {
        if (window.innerWidth > 768) sidebar.classList.remove('collapsed');
    });

    sidebar.addEventListener('mouseleave', () => {
        if (window.innerWidth > 768) sidebar.classList.add('collapsed');
    });

    // Close on outside click (mobile)
    document.addEventListener('click', (e) => {
        const toggle = document.querySelector('.menu-toggle');
        if (!sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });

    // Dropdown toggle
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        const parent = menu.parentElement;
        const isOpen = menu.style.display === 'block';
        menu.style.display = isOpen ? 'none' : 'block';
        parent.classList.toggle('active', !isOpen);
    }
</script>

</body>
</html>