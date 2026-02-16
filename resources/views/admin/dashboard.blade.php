@extends('admin.layouts.app')

@section('content')
<div class="dashboard_container">
    <h2>Welcome, {{ Auth::user()->username }}</h2>
    <p>Here’s your HR2 summary overview:</p>

    <div class="dashboard_grid">
            <div class="dashboard_card">
                <h3>label</h3>
                <p>count</p>
            </div>
    </div>
</div>
@endsection