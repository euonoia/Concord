<h1>Staff & HR Management Dashboard</h1>
<p>Welcome, {{ Auth::guard('employee')->user()->firstname }}!</p>

<form action="{{ route('staff.logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>