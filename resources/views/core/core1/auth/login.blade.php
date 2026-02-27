<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hospital Management System</title>
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="core1-auth-body">
    <div class="core1-auth-card">
        <div class="core1-flex-gap-2 mb-20 justify-center">
            <i class="fas fa-hospital text-blue core1-icon-xl"></i>
            <div>
                <h1 class="core1-title">HMS Portal</h1>
                <p class="core1-subtitle">Hospital Management System</p>
            </div>
        </div>

        <form action="{{ route('login.submit') }}" method="POST" class="d-flex flex-col gap-4">
            @csrf
            
            <div>
                <label class="core1-label">Select Role</label>
                <div class="core1-role-grid">
                    @php
                        $roles = [
                            ['value' => 'admin', 'label' => 'Administrator'],
                            ['value' => 'doctor', 'label' => 'Doctor'],
                            ['value' => 'nurse', 'label' => 'Nurse'],
                            ['value' => 'patient', 'label' => 'Patient'],
                            ['value' => 'receptionist', 'label' => 'Receptionist'],
                            ['value' => 'billing', 'label' => 'Billing Officer'],
                        ];
                    @endphp
                    @foreach($roles as $role)
                        <button
                            type="button"
                            onclick="document.getElementById('role').value='{{ $role['value'] }}'; updateRoleSelection(this, '{{ $role['value'] }}');"
                            class="core1-role-btn {{ $role['value'] }}"
                            data-role="{{ $role['value'] }}"
                        >
                            {{ $role['label'] }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="role" id="role" value="admin" required>
            </div>

            <div>
                <label for="email" class="core1-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="core1-input @error('email') core1-input-error @enderror"
                    placeholder="Enter your email"
                    required
                >
                @error('email')
                    <p class="core1-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="core1-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="core1-input @error('password') core1-input-error @enderror"
                    placeholder="Enter your password"
                    required
                >
                @error('password')
                    <p class="core1-error-text">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="core1-auth-btn">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        </form>

        <div class="core1-auth-notice">
            <p>
                Demo Mode: Select any role and click Sign In to access the dashboard
            </p>
        </div>
    </div>

    <script>
        function updateRoleSelection(btn, selectedRole) {
            document.querySelectorAll('.core1-role-btn').forEach(b => {
                b.classList.remove('active');
            });
            btn.classList.add('active');
        }
        
        // Initialize with admin selected
        window.onload = function() {
            const adminBtn = document.querySelector('.core1-role-btn.admin');
            if(adminBtn) updateRoleSelection(adminBtn, 'admin');
        };
    </script>
</body>
</html>
