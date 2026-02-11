<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
</head>
<body>
    <h1>Welcome to the Patient Portal</h1>
    <p>Logged in as: {{ Auth::user()->email }}</p>
    
    <form action="{{ route('portal.logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>