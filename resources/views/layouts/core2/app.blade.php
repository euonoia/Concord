<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concord | @yield('title', 'Core2')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }

        /* ── SIDEBAR SHELL ── */
        #core2-sidebar {
            width: 288px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 50;
            transition: width 0.25s ease;
            overflow: hidden;
            position: relative;
        }
        #core2-sidebar.collapsed { width: 72px; }

        /* Hide labels & chevrons when collapsed */
        #core2-sidebar.collapsed .core2-label,
        #core2-sidebar.collapsed .core2-chevron,
        #core2-sidebar.collapsed .core2-user-info,
        #core2-sidebar.collapsed .core2-submenu { display: none !important; }

        /* Logo img: full when open, small icon when collapsed */
        #core2-sidebar .core2-logo-full { display: block; max-height: 40px; width: auto; transition: max-height 0.25s ease; }
        #core2-sidebar.collapsed .core2-logo-full { max-height: 40px; }

        /* ── NAV NODES — inactive ── */
        .core2-sidebar-node {
            transition: all 0.2s;
            cursor: pointer;
            color: #1B3C53 !important;
            text-decoration: none;
            display: flex;
            background-color: transparent;
            white-space: nowrap;
        }
        .core2-sidebar-node:hover {
            background-color: #1B3C53;
            color: #ffffff !important;
        }
        .core2-sidebar-node:hover svg,
        .core2-sidebar-node:hover span { color: #ffffff !important; }

        /* ── NAV NODES — active ── */
        .core2-sidebar-node.active {
            background-color: #456882;
            color: #ffffff !important;
            font-weight: 600;
        }
        .core2-sidebar-node.active svg { color: #ffffff !important; }

        /* Chevron rotation */
        .core2-sidebar-node .core2-chevron { transition: transform 0.2s; }
        .core2-sidebar-node.active .core2-chevron { transform: rotate(180deg); }

        /* ── SUB-MENU ── */
        .core2-submenu { display: none; }
        .core2-submenu.show { display: block; animation: core2-slideDown 0.2s ease-out; }
        .core2-sub-node {
            display: block; padding: 10px 16px; border-radius: 12px;
            font-size: 0.72rem; font-weight: 700; color: #456882; margin: 2px 8px;
            transition: all 0.2s; text-transform: uppercase; text-decoration: none;
            white-space: nowrap;
        }
        .core2-sub-node:hover { background-color: #1B3C53; color: #ffffff; }
        .core2-sub-node.active { background-color: #456882; color: #ffffff; font-weight: 800; }

        /* ── MOBILE TOPBAR ── */
        .core2-topbar {
            display: none;
            background: #1B3C53;
            color: #ffffff;
            padding: 15px 20px;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0; left: 0; width: 100%;
            z-index: 200;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .core2-menu-toggle {
            background: #456882;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }

        /* ── MOBILE OVERLAY SIDEBAR ── */
        @media (max-width: 768px) {
            .core2-topbar { display: flex; }
            #core2-sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                width: 288px !important;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                z-index: 150;
            }
            #core2-sidebar.show { transform: translateX(0); }
            #core2-sidebar.collapsed { width: 288px !important; }
            #core2-sidebar.collapsed .core2-label,
            #core2-sidebar.collapsed .core2-chevron,
            #core2-sidebar.collapsed .core2-user-info { display: flex !important; }
            #core2-sidebar.collapsed .core2-submenu.show { display: block !important; }
            #core2-main { margin-top: 56px; }
        }

        .core2-hide-scroll::-webkit-scrollbar { display: none; }
        @keyframes core2-slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes core2-fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .core2-content { animation: core2-fadeIn 0.4s ease-out; }
        .core2-flash-success { animation: core2-fadeIn 0.3s ease-out; }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased overflow-hidden">

{{-- Mobile Topbar --}}
<div class="core2-topbar">
    <button class="core2-menu-toggle" onclick="document.getElementById('core2-sidebar').classList.toggle('show')">☰</button>
    <img src="{{ asset('images/Concord (1).png') }}" alt="Concord" style="max-height:28px; width:auto;">
</div>

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside id="core2-sidebar">
        {{-- Logo --}}
        <div class="p-6 flex items-center justify-center border-b border-slate-100" style="min-height:81px;">
            <img src="{{ asset('images/Concord (1).png') }}" alt="Concord Logo" class="core2-logo-full" style="max-height:40px; width:auto;">
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto core2-hide-scroll pt-4">

            {{-- Command Center --}}
            <a href="{{ route('core2.dashboard') }}"
               class="core2-sidebar-node p-4 rounded-2xl flex items-center gap-4 mb-3 {{ request()->routeIs('core2.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="font-bold text-sm core2-label">Command Center</span>
            </a>

            {{-- PHARMACY --}}
            @php $pharmacyActive = request()->routeIs('core2.pharmacy.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-pharmacy')" class="core2-sidebar-node w-full p-4 rounded-2xl flex items-center justify-between {{ $pharmacyActive ? 'active' : '' }}">
                    <div class="flex items-center gap-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span class="font-bold text-xs uppercase core2-label">Pharmacy</span>
                    </div>
                    <svg class="w-3 h-3 opacity-40 core2-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-pharmacy" class="core2-submenu ml-6 border-l border-slate-100 {{ $pharmacyActive ? 'show' : '' }}">
                    <a href="{{ route('core2.pharmacy.drug-inventory.index') }}" class="core2-sub-node {{ request()->routeIs('core2.pharmacy.drug-inventory.*') ? 'active' : '' }}">Drug Inventory</a>
                    <a href="{{ route('core2.pharmacy.prescription.index') }}" class="core2-sub-node {{ request()->routeIs('core2.pharmacy.prescription.*') ? 'active' : '' }}">Prescription</a>
                    <a href="{{ route('core2.pharmacy.formula-management.index') }}" class="core2-sub-node {{ request()->routeIs('core2.pharmacy.formula-management.*') ? 'active' : '' }}">Formula Management</a>
                </div>
            </div>

            {{-- MEDICAL PACKAGES --}}
            @php $pkgActive = request()->routeIs('core2.medical-packages.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-packages')" class="core2-sidebar-node w-full p-4 rounded-2xl flex items-center justify-between {{ $pkgActive ? 'active' : '' }}">
                    <div class="flex items-center gap-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="font-bold text-xs uppercase core2-label">Medical Packages</span>
                    </div>
                    <svg class="w-3 h-3 opacity-40 core2-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-packages" class="core2-submenu ml-6 border-l border-slate-100 {{ $pkgActive ? 'show' : '' }}">
                    <a href="{{ route('core2.medical-packages.packages.index') }}" class="core2-sub-node {{ request()->routeIs('core2.medical-packages.packages.*') ? 'active' : '' }}">Package Definition &amp; Pricing</a>
                    <a href="{{ route('core2.medical-packages.enrollment.index') }}" class="core2-sub-node {{ request()->routeIs('core2.medical-packages.enrollment.*') ? 'active' : '' }}">Patient Package Enrollment</a>
                </div>
            </div>

            {{-- LABORATORY --}}
            @php $labActive = request()->routeIs('core2.laboratory.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-laboratory')" class="core2-sidebar-node w-full p-4 rounded-2xl flex items-center justify-between {{ $labActive ? 'active' : '' }}">
                    <div class="flex items-center gap-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="font-bold text-xs uppercase core2-label">Laboratory</span>
                    </div>
                    <svg class="w-3 h-3 opacity-40 core2-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-laboratory" class="core2-submenu ml-6 border-l border-slate-100 {{ $labActive ? 'show' : '' }}">
                    <a href="{{ route('core2.laboratory.test-orders.index') }}" class="core2-sub-node {{ request()->routeIs('core2.laboratory.test-orders.*') ? 'active' : '' }}">Test Ordering &amp; Registration</a>
                    <a href="{{ route('core2.laboratory.sample-tracking.index') }}" class="core2-sub-node {{ request()->routeIs('core2.laboratory.sample-tracking.*') ? 'active' : '' }}">Sample Tracking &amp; LIS</a>
                    <a href="{{ route('core2.laboratory.result-validation.index') }}" class="core2-sub-node {{ request()->routeIs('core2.laboratory.result-validation.*') ? 'active' : '' }}">Result Entry &amp; Validation</a>
                </div>
            </div>

            {{-- SURGERY & DIET --}}
            @php $surgActive = request()->routeIs('core2.surgery-diet.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-surgery')" class="core2-sidebar-node w-full p-4 rounded-2xl flex items-center justify-between {{ $surgActive ? 'active' : '' }}">
                    <div class="flex items-center gap-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="font-bold text-xs uppercase core2-label">Surgery &amp; Diet</span>
                    </div>
                    <svg class="w-3 h-3 opacity-40 core2-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-surgery" class="core2-submenu ml-6 border-l border-slate-100 {{ $surgActive ? 'show' : '' }}">
                    <a href="{{ route('core2.surgery-diet.or-booking.index') }}" class="core2-sub-node {{ request()->routeIs('core2.surgery-diet.or-booking.*') ? 'active' : '' }}">Operating Room Booking</a>
                    <a href="{{ route('core2.surgery-diet.nutritional.index') }}" class="core2-sub-node {{ request()->routeIs('core2.surgery-diet.nutritional.*') ? 'active' : '' }}">Nutritional Assessment</a>
                    <a href="{{ route('core2.surgery-diet.utilization.index') }}" class="core2-sub-node {{ request()->routeIs('core2.surgery-diet.utilization.*') ? 'active' : '' }}">Utilization Reporting</a>
                </div>
            </div>

            {{-- BED & LINEN --}}
            @php $bedActive = request()->routeIs('core2.bed-linen.*'); @endphp
            <div>
                <button onclick="toggleMenu('menu-bed')" class="core2-sidebar-node w-full p-4 rounded-2xl flex items-center justify-between {{ $bedActive ? 'active' : '' }}">
                    <div class="flex items-center gap-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="font-bold text-xs uppercase core2-label">Bed &amp; Linen</span>
                    </div>
                    <svg class="w-3 h-3 opacity-40 core2-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-bed" class="core2-submenu ml-6 border-l border-slate-100 {{ $bedActive ? 'show' : '' }}">
                    <a href="{{ route('core2.bed-linen.pending-admissions.index') }}" class="core2-sub-node {{ request()->routeIs('core2.bed-linen.pending-admissions.*') ? 'active' : '' }}">Pending Admissions</a>
                    <a href="{{ route('core2.bed-linen.room-assignment.index') }}" class="core2-sub-node {{ request()->routeIs('core2.bed-linen.room-assignment.*') ? 'active' : '' }}">Room Assignment</a>
                    <a href="{{ route('core2.bed-linen.bed-status.index') }}" class="core2-sub-node {{ request()->routeIs('core2.bed-linen.bed-status.*') ? 'active' : '' }}">Bed Status &amp; Allocation</a>
                    <a href="{{ route('core2.bed-linen.patient-transfer.index') }}" class="core2-sub-node {{ request()->routeIs('core2.bed-linen.patient-transfer.*') ? 'active' : '' }}">Patient Transfer</a>
                    <a href="{{ route('core2.bed-linen.house-keeping.index') }}" class="core2-sub-node {{ request()->routeIs('core2.bed-linen.house-keeping.*') ? 'active' : '' }}">Housekeeping Status</a>
                </div>
            </div>

        </nav>

        {{-- User Footer --}}
        <div class="p-5 bg-slate-50 border-t border-slate-200 core2-user-info">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="core2-label">
                        <p class="font-bold text-xs text-slate-900">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-slate-400 uppercase font-semibold">{{ Auth::user()->role_slug ?? '' }}</p>
                    </div>
                </div>
                <form action="{{ route('portal.logout') }}" method="POST" class="core2-label">
                    @csrf
                    <button type="submit" class="text-xs text-slate-400 hover:text-red-500 transition font-semibold">Logout</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main id="core2-main" class="flex-1 overflow-y-auto bg-[#f8fafc]">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="core2-flash-success mx-10 mt-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-6 py-4 text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-10 mt-6 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-6 py-4 text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="core2-content p-10">
            @yield('content')
        </div>
    </main>
</div>

<script>
    const sidebar = document.getElementById('core2-sidebar');

    // Default: collapsed on desktop
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

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', (e) => {
        const toggle = document.querySelector('.core2-menu-toggle');
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });

    function toggleMenu(id) {
        const el = document.getElementById(id);
        const open = el.classList.contains('show');
        document.querySelectorAll('.core2-submenu').forEach(s => s.classList.remove('show'));
        if (!open) el.classList.add('show');
    }
</script>
@stack('scripts')

</body>
</html>
