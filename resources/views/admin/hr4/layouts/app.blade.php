<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR4 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-dropdown .dropdown-menu {
            padding-left: 20px;
        }
        .sidebar-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            text-decoration: none;
            color: #ccc;
            font-size: 14px;
        }
        .sidebar-dropdown .dropdown-item:hover {
            background-color: #333;
            color: #fff;
        }
        .sidebar-dropdown .dropdown-item i {
            margin-right: 8px;
        }
        .dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        .toggle-icon {
            transition: transform 0.3s;
        }
        .sidebar-dropdown.active .toggle-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="dashboard">

<!-- Mobile Topbar -->
<div class="dashboard-topbar topbar">
    <button class="menu-toggle"
        onclick="document.querySelector('.dashboard .sidebar').classList.toggle('show')">
        ☰
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
        <a href="{{ route('admin.hr4.dashboard') }}" class="{{ request()->routeIs('admin.hr4.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <!-- Core Human Capital Dropdown -->
        <div class="sidebar-dropdown">
            <a href="{{ route('hr4.core') }}" class="dropdown-toggle {{ request()->routeIs('hr4.core') ? 'active' : '' }}" onclick="toggleDropdown('core-dropdown')">
                <i class="bi bi-diagram-3"></i>
                <span>CoreHuman</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
            </a>
            <div id="core-dropdown" class="dropdown-menu" style="display: none;">
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
            </div>
        </div>

        <a href="{{ route('hr4.direct_compensation.index') }}" class="{{ request()->routeIs('hr4.direct_compensation.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Direct Compensation</span>
        </a>

        <a href="{{ route('hr4.job_postings.index') }}" class="{{ request()->routeIs('hr4.job_postings.*') ? 'active' : '' }}">
            <i class="bi bi-briefcase"></i>
            <span>Available Jobs</span>
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

// Dropdown toggle function
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const parent = menu.parentElement;
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        parent.classList.add('active');
    } else {
        menu.style.display = 'none';
        parent.classList.remove('active');
    }
}
</script>

</body>
</html>