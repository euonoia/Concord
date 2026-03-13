@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">New Hire Profile</h3>
            <p class="text-white">Comprehensive record for <strong>{{ $newHire->first_name }} {{ $newHire->last_name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('hr1.newhires.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-person-lines-fill me-1"></i> Staff Records
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="hr1-premium-table-card">
                <div class="hr1-table-header">
                    <h6>Employment Master Data</h6>
                    <span class="hr1-badge hr1-badge-success">Internal ID #{{ $newHire->id }}</span>
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Legal Full Name</label>
                            <div class="fw-bold text-dark fs-5">{{ $newHire->first_name }} {{ $newHire->last_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Primary Email</label>
                            <div class="text-dark">{{ $newHire->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Contact Number</label>
                            <div class="text-dark">{{ $newHire->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Assigned Department</label>
                            <div><span class="badge bg-light text-dark border">{{ $newHire->department_name }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Functional Specialization</label>
                            <div class="text-dark fw-semibold">{{ $newHire->specialization ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Academic Track Status</label>
                            <div class="text-dark fw-bold text-primary">{{ ucfirst($newHire->post_grad_status) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Employment Lifecycle Status</label>
                            <div>
                                @php
                                    $statusBadge = match($newHire->status) {
                                        'active' => 'hr1-badge-success',
                                        'onboarding' => 'hr1-badge-info',
                                        'inactive' => 'hr1-badge-danger',
                                        default => 'hr1-badge-primary',
                                    };
                                @endphp
                                <span class="hr1-badge {{ $statusBadge }} fs-6">{{ ucfirst($newHire->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="hr1-premium-table-card shadow-sm border-0 text-center py-5">
                <div class="mb-4 text-primary" style="font-size: 3rem;">
                    <i class="bi bi-file-earmark-person"></i>
                </div>
                <h6 class="mb-4">Employment Documents</h6>
                @if($newHire->resume_path)
                    <a href="{{ route('hr1.newhires.download', $newHire->id) }}" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm" target="_blank">
                        <i class="bi bi-download me-2"></i> Download CV
                    </a>
                @else
                    <div class="alert alert-light mx-4 border-dashed">
                        <i class="bi bi-info-circle me-2"></i> No resume found
                    </div>
                @endif
            </div>

            <div class="card mt-4 border-0 shadow-sm" style="border-radius: 12px; background: #fff;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-sm btn-outline-info rounded-pill py-2">Email Onboarding Link</button>
                        <button class="btn btn-sm btn-outline-dark rounded-pill py-2">Archive Employee</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection