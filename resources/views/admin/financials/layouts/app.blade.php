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
    <div class="title">Financials</div>
</div>

<!-- Sidebar -->
<!-- SIDEBAR -->
<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">HRMS</div>
    </div>

    <nav>
        <a href="{{ route('admin.hr1.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <!-- DISBURSEMENT DROPDOWN -->
        <div class="nav-dropdown">
            <a href="#" onclick="toggleDropdown(event)">
                <i class="bi bi-cash-stack"></i>
                <span>Disbursement</span>
                <i class="bi bi-chevron-down arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('financials.reimbursement.index') }}" class="sub-link">
                    <i class="bi bi-receipt"></i>
                    <span>Reimbursements</span>
                </a>
                <a href="#" class="sub-link">
                    <i class="bi bi-journal-text"></i>
                    <span>Disbursement Ledger</span>
                </a>
            </div>
        </div>

        <!-- COLLECTIONS DROPDOWN -->
        <div class="nav-dropdown">
            <a href="#" onclick="toggleDropdown(event)">
                <i class="bi bi-collection"></i>
                <span>Collections</span>
                <i class="bi bi-chevron-down arrow-icon"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('financials.bills.index') }}" class="sub-link">
                    <i class="bi bi-receipt"></i>
                    <span>Bills Collection</span>
                </a>
                <a href="#" class="sub-link">
                    <i class="bi bi-journal-text"></i>
                    <span>Collection Ledger</span>
                </a>
            </div>
        </div>

        <!-- LOGOUT -->
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

function toggleDropdown(event) {

    event.preventDefault();

    const parent = event.currentTarget.parentElement;

    parent.classList.toggle('open');

}
</script>

</body>
</html>