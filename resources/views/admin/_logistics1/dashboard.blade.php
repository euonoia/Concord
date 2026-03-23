@extends('admin._logistics1.layouts.app')

@section('content')
<div class="dashboard_container">
    <h2>Welcome, {{ Auth::user()->username }}</h2>
    <p>Here’s your Logistics1 summary overview:</p>

    <div class="dashboard_grid">
            <div class="dashboard_card">
                <h3>label</h3>
                <p>count</p>
            </div>
            
    </div>
<div class="mb-4">
    <h4 class="mb-0"><i class="bi bi-house-door me-2"></i>Dashboard</h4>
    <small class="text-muted">Logistics 1 — Overview</small>
</div>

@endsection