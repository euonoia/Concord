<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR2 Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard" data-page="@yield('page')">

<!-- Mobile Topbar -->
<div class="dashboard-topbar topbar">
    <button class="menu-toggle">
        ☰
    </button>
    <div class="title">HR</div>
</div>

<!-- Sidebar -->
<div class="dashboard-sidebar sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="HR Logo">
        <div class="logo-text">Human Resource</div>
    </div>

    <nav>
        <a href="{{ route('hr.dashboard') }}" 
        class="{{ request()->routeIs('hr2.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('user.competencies.index') }}"
           class="{{ request()->routeIs('user.competencies.index') ? 'active' : '' }}">
            <i class="bi bi-lightbulb"></i>
            <span>Competencies</span>
        </a>

        <a href="{{ route('user.learning.index') }}"
           class="{{ request()->routeIs('user.learning.index') ? 'active' : '' }}">     
            <i class="bi bi-book"></i>
            <span>Learning</span>
        </a>

        <a href="{{ route('user.training.index') }}"
            class="{{ request()->routeIs('user.training.index') ? 'active' : '' }}">
            <i class="bi bi-mortarboard"></i>
            <span>Training</span>
        </a>
        
        <a href="{{ route('user.succession.index') }}"
            class="{{ request()->routeIs('user.succession.index') ? 'active' : '' }}">
                <i class="bi bi-tree"></i>
                <span>Succession</span>
        </a>

        <a href="{{ route('user.ess.index') }}"
            class="{{ request()->routeIs('user.ess.index') ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i>
            <span>ESS</span>
        </a>

        <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" style="display:none;">
            @csrf
        </form>

        <a href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
</body>
</html>