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
    
    <div>
        <label for="email">Email Address:</label>
        <input type="email" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               placeholder="name@hospital.com"
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
               required>
    </div>

    <br>

    <div class="form-options">
        <label>
            <input type="checkbox" name="remember"> Remember Me
        </label>
    </div>

    <br>

    <button type="submit">Sign In to Portal</button>
</form>

<p>
    Don't have a patient account? <a href="{{ route('portal.register') }}">Register here</a>
</p>