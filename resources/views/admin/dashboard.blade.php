@extends('admin.layouts.admin')

@section('content')
<div class="admin-main">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="mb-1">Administrative Dashboard</h2>
            <p class="text-light small">Overview of HR and System Operations</p>
        </div>
        <div class="date-display p-2 bg-white rounded shadow-sm">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            <span class="fw-bold">Feb 16, 2026</span>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="admin-stat-card">
                <div class="icon-wrapper bg-primary text-white">
                    <i class="bi bi-people-fill"></i>
                </div>
                <small class="text-light text-uppercase fw-bold">Total Personnel</small>
                <h2 class="fw-bold mt-1">1,240</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-stat-card">
                <div class="icon-wrapper bg-success text-white">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <small class="text-light text-uppercase fw-bold">Active Residencies</small>
                <h2 class="fw-bold mt-1">86</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-stat-card">
                <div class="icon-wrapper bg-warning text-white">
                    <i class="bi bi-clock-history"></i>
                </div>
                <small class="text-light text-uppercase fw-bold">Pending Approvals</small>
                <h2 class="fw-bold mt-1">14</h2>
            </div>
        </div>
    </div>

    <div class="admin-table-container">
        <h4 class="mb-4">Recent System Logs</h4>
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User Role</th>
                        <th>Module</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Login Success</td>
                        <td><span class="text-info">HR_Admin</span></td>
                        <td>Authentication</td>
                        <td class="text-light">Just now</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection