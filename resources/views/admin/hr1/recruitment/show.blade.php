@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div class="d-flex align-items-center">
            <div class="bg-white bg-opacity-25 rounded p-3 me-3">
                <i class="bi bi-briefcase text-white fs-4"></i>
            </div>
            <div>
                <h3 class="text-white mb-0">{{ $posting->title }}</h3>
                <p class="text-white opacity-75 small mb-0"><i class="bi bi-building me-1"></i> Department: {{ $posting->dept_code }}</p>
            </div>
        </div>
        <div>
            @if($posting->is_active)
                <span class="badge bg-success border border-light border-opacity-50 px-3 py-2 rounded-pill shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> Active Listing</span>
            @else
                <span class="badge bg-danger border border-light border-opacity-50 px-3 py-2 rounded-pill shadow-sm"><i class="bi bi-eye-slash-fill me-1"></i> Inactive Listing</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show hr1-mb-5" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Details Area --}}
        <div class="col-lg-8">
            <div class="hr1-premium-table-card">
                <div class="hr1-table-header">
                    <h6>Job Specification & Requirements</h6>
                    <span class="hr1-badge hr1-badge-info">Posting #{{ str_pad($posting->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Track Category</label>
                            @php
                                $trackBadge = match($posting->track_type) {
                                    'fellowship' => 'hr1-badge-primary',
                                    'nursing'    => 'hr1-badge-info',
                                    default      => 'hr1-badge-secondary',
                                };
                            @endphp
                            <span class="hr1-badge {{ $trackBadge }} fs-6">{{ ucfirst($posting->track_type) }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Required Capacity</label>
                            <div class="fw-bold text-dark fs-5">
                                {{ $posting->needed_applicants }} <span class="text-muted fw-normal" style="font-size: 0.8rem;">Seats Available</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-2">Role Description</label>
                        <div class="p-3 bg-light rounded-3 text-muted" style="line-height: 1.6; font-size: 0.9rem; border: 1px dashed #dee2e6;">
                            {{ $posting->description ?? 'No specific description provided for this role.' }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="text-muted small"><i class="bi bi-clock me-1"></i> Created On</div>
                            <div class="fw-semibold small text-dark">{{ \Carbon\Carbon::parse($posting->created_at)->format('M d, Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small"><i class="bi bi-pencil-square me-1"></i> Last Refreshed</div>
                            <div class="fw-semibold small text-dark">{{ \Carbon\Carbon::parse($posting->updated_at)->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-lg-4">
            <div class="hr1-premium-table-card">
                <div class="hr1-table-header">
                    <h6>Manage Visibility</h6>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('hr1.recruitment.toggle', $posting->id) }}" class="mb-2">
                        @csrf
                        @if($posting->is_active)
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold mb-3 shadow-sm">
                                <i class="bi bi-pause-fill me-1"></i> Deactivate Listing
                            </button>
                            <div class="alert alert-light border-0 small text-muted mb-0" style="background: rgba(0,0,0,0.02);">
                                <i class="bi bi-info-circle-fill me-2 text-info"></i> Listing will be hidden from the careers portal.
                            </div>
                        @else
                            <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold mb-3 shadow-sm">
                                <i class="bi bi-play-fill me-1"></i> Publish Posting
                            </button>
                            <div class="alert alert-light border-0 small text-muted mb-0" style="background: rgba(0,0,0,0.02);">
                                <i class="bi bi-check-circle-fill me-2 text-success"></i> Listing will be visible to all candidates.
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('hr1.recruitment.index') }}" class="btn btn-outline-secondary w-100 rounded-pill py-2 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Return to Directory
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
