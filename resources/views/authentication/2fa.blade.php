<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Two-Factor Authentication | Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    
    <div class="auth-container">
        <div class="auth-card shadow-lg rounded-lg">
            <h2>Two-Factor Authentication</h2>
            <p class="auth-subtitle">We've sent a 6-digit verification code to your email. Please enter it below to continue.</p>

            {{-- Success/Status --}}
            @if(session('status'))
                <div class="alert alert-success rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger rounded-md">
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('portal.2fa.verify') }}" method="POST" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="otp_code">Verification Code</label>
                    <input type="text" 
                           id="otp_code" 
                           name="otp_code" 
                           maxlength="6"
                           placeholder="123456"
                           class="form-control @error('otp_code') is-invalid @enderror"
                           required 
                           autofocus
                           autocomplete="one-time-code"
                           style="text-align: center; font-size: 24px; letter-spacing: 0.5em;">
                </div>

                <div class="form-options" style="justify-content: center;">
                    <p style="font-size: 0.875rem; color: #666;">
                        Didn't receive the code? 
                        <a href="{{ route('portal.2fa.resend') }}" class="forgot-link" style="font-weight: 600;">Resend Code</a>
                    </p>
                </div>

                <button type="submit" class="btn btn-primary rounded-md">
                    Verify & Sign In
                </button>
            </form>

            <div style="margin-top: 20px; text-align: center;">
                <a href="{{ route('portal.login') }}" style="font-size: 0.875rem; color: #666; text-decoration: none;">&larr; Back to Login</a>
            </div>
        </div>
    </div>

</body>
</html>
