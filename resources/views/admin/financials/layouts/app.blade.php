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
    <a href="{{ route('finance.dashboard') }}"
       class="{{ request()->routeIs('finance.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Dashboard</span>
    </a>

    <div class="nav-dropdown {{ request()->is('financials/apar*') ? 'open' : '' }}">
        <a href="#" onclick="toggleDropdown(event)">
            <i class="bi bi-calculator"></i>
            <span>AP & AR</span>
            <i class="bi bi-chevron-down arrow-icon"></i>
        </a>
        <div class="dropdown-container">
            <a href="{{ route('financials.apar.index') }}" 
               class="sub-link {{ request()->routeIs('financials.apar.index') ? 'active' : '' }}">
                <i class="bi bi-arrow-down-left-circle"></i>
                <span>Bills Receivable</span>
            </a>
            <a href="#" class="sub-link">
                <i class="bi bi-arrow-up-right-circle"></i>
                <span>Accounts Payable</span>
            </a>
        </div>
    </div>

    <div class="nav-dropdown {{ request()->is('financials/bills*') || request()->is('financials/disbursement*') ? 'open' : '' }}">
        <a href="#" onclick="toggleDropdown(event)">
            <i class="bi bi-cash-stack"></i>
            <span>Transactions</span>
            <i class="bi bi-chevron-down arrow-icon"></i>
        </a>
        <div class="dropdown-container">
            <a href="{{ route('financials.bills.index') }}" 
               class="sub-link {{ request()->routeIs('financials.bills.index') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Bills Collection</span>
            </a>
            <a href="{{ route('financials.reimbursement.index') }}" 
               class="sub-link {{ request()->routeIs('financials.reimbursement.index') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Reimbursements</span>
            </a>
        </div>
    </div>

    <div class="nav-dropdown {{ request()->is('financials/ledger*') ? 'open' : '' }}">
        <a href="#" onclick="toggleDropdown(event)">
            <i class="bi bi-book"></i>
            <span>General Ledger</span>
            <i class="bi bi-chevron-down arrow-icon"></i>
        </a>
        <div class="dropdown-container">
            <a href="#" class="sub-link">
                <i class="bi bi-journal-text"></i>
                <span>Collection Ledger</span>
            </a>
            <a href="#" class="sub-link">
                <i class="bi bi-journal-check"></i>
                <span>Disbursement Ledger</span>
            </a>
            <a href="#" class="sub-link">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Financial Summary</span>
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