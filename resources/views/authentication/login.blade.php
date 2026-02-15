<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In |  Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    
    <div class="auth-container">
        <div class="auth-card shadow-lg rounded-lg">
            <h2>Sign In to Portal</h2>
            <p class="auth-subtitle">Enter your credentials to access the portal.</p>

            {{-- Success Messages --}}
            @if(session('success'))
                <div class="alert alert-success rounded-md">
                    {{ session('success') }}
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

            <form action="{{ route('portal.login.submit') }}" method="POST" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="login">Email or Patient Code</label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           value="{{ old('login') }}" 
                           placeholder="Enter email or PAT-XXXXX"
                           class="form-control @error('login') is-invalid @enderror"
                           required 
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="••••••••"
                           class="form-control"
                           required>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> 
                        <span>Remember Me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary rounded-md">
                    Sign In to Portal
                </button>
            </form>
        </div>
    </div>

</body>
</html>