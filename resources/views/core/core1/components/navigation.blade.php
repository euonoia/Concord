@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
    
    $navItems = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-house-door', 'roles' => ['admin', 'admin_core1', 'doctor', 'nurse', 'head_nurse', 'patient', 'receptionist', 'billing_officer'], 'route' => 'core1.' . (in_array($user->role_slug, ['admin', 'admin_core1']) ? 'admin' : ($user->role_slug === 'head_nurse' ? 'nurse' : $user->role_slug)) . '.dashboard'],
        ['id' => 'patients', 'label' => 'Patient Management', 'icon' => 'bi-people', 'roles' => ['admin', 'admin_core1', 'doctor', 'nurse', 'head_nurse', 'receptionist'], 'route' => 'core1.patients.index'],
        ['id' => 'appointments', 'label' => 'Appointments', 'icon' => 'bi-calendar', 'roles' => ['admin', 'admin_core1', 'doctor', 'patient', 'receptionist'], 'route' => 'core1.appointments.index'],
        ['id' => 'outpatient', 'label' => 'Outpatient Care', 'icon' => 'bi-heart-pulse', 'roles' => ['admin', 'admin_core1', 'doctor'], 'route' => 'core1.outpatient.index'],
        ['id' => 'inpatient', 'label' => 'Inpatient Care', 'icon' => 'bi-hospital', 'roles' => ['admin', 'admin_core1', 'doctor', 'nurse', 'head_nurse'], 'route' => 'core1.inpatient.index'],
        ['id' => 'medical-records', 'label' => 'Medical Records', 'icon' => 'bi-file-text', 'roles' => ['admin', 'admin_core1', 'doctor', 'nurse', 'head_nurse', 'patient'], 'route' => 'core1.medical-records.index'],
        ['id' => 'billing', 'label' => 'Billing & Payments', 'icon' => 'bi-credit-card', 'roles' => ['admin', 'admin_core1', 'billing_officer', 'patient'], 'route' => 'core1.billing.index'],
        ['id' => 'discharge', 'label' => 'Discharge Management', 'icon' => 'bi-clipboard-check', 'roles' => ['admin', 'admin_core1', 'doctor', 'billing_officer'], 'route' => 'core1.discharge.index'],
        ['id' => 'reports', 'label' => 'Reports & Analytics', 'icon' => 'bi-graph-up', 'roles' => ['admin', 'admin_core1'], 'route' => 'core1.reports.index'],
        ['id' => 'staff', 'label' => 'Staff Management', 'icon' => 'bi-person-gear', 'roles' => ['admin', 'admin_core1'], 'route' => 'core1.staff.index'],
        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'bi-gear', 'roles' => ['admin', 'admin_core1', 'doctor', 'nurse', 'head_nurse', 'patient', 'receptionist', 'billing_officer'], 'route' => 'core1.settings.index'],
    ];
    
    $filteredNavItems = array_filter($navItems, function($item) use ($user) {
        return in_array($user->role_slug, $item['roles']);
    });
@endphp

<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="{{ asset('images/Concord (1).png') }}" alt="Concord Logo" onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
        <div class="logo-text d-none">Core 1</div>
    </div>

    <nav>
        @foreach($filteredNavItems as $item)
            @php
                $isActive = $currentRoute === $item['route'] || 
                            ($item['id'] !== 'dashboard' && str_starts_with($currentRoute, 'core1.' . $item['id'] . '.') && !str_ends_with($currentRoute, '.dashboard') && !str_ends_with($currentRoute, '.overview')) || 
                            ($item['id'] === 'dashboard' && (str_ends_with($currentRoute, '.dashboard') || str_ends_with($currentRoute, '.overview')));
            @endphp
            <a href="{{ route($item['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

       <form id="logout-form" method="POST" action="{{ route('portal.logout') }}" class="d-none">
            @csrf
        </form>

        <a href="#"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>
