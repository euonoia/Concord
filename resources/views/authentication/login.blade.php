@if(session('success'))
    <div style="color: green; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="color: red; margin-bottom: 10px;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('portal.login.submit') }}" method="POST">
    @csrf
    
    @if($errors->has('login'))
        <div style="color: red; margin-bottom: 10px; font-size: 0.875rem;">
            {{ $errors->first('login') }}
        </div>
    @endif

    <div>
        <label for="login">Email or Patient Code:</label>
        <input type="text" 
               id="login" 
               name="login" 
               value="{{ old('login') }}" 
               placeholder="Enter email or PAT-XXXXX"
               style="display: block; width: 100%; margin-top: 5px;"
               required 
               autofocus>
    </div>

    <br>

    <div>
        <label for="password">Password:</label>
        <input type="password" 
               id="password" 
               name="password" 
               placeholder="••••••••"
               style="display: block; width: 100%; margin-top: 5px;"
               required>
    </div>

    <br>

    <div class="form-options">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="remember"> Remember Me
        </label>
    </div>

    <br>

    <button type="submit" style="width: 100%; padding: 10px; cursor: pointer;">
        Sign In to Portal
    </button>
</form>

<p>
    Don't have a patient account? <a href="{{ route('portal.register') }}">Register here</a>
</p>