@extends('admin.hr1.layouts.app')

@section('content')
<style>
    .gradient-header-sm {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 1.25rem;
    }
    .detail-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.15rem;
    }
    .detail-value {
        font-size: 0.95rem;
        color: #2b2b2b;
        font-weight: 500;
        margin-bottom: 1.15rem;
    }
    .badge-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .badge-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
</style>

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('hr1.recruitment.index') }}" class="text-decoration-none fw-semibold"><i class="bi bi-arrow-left-short align-middle"></i> Directory</a></li>
            <li class="breadcrumb-item active text-muted" aria-current="page">View Posting #{{ str_pad($posting->id, 3, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success fade show border-0 shadow-sm py-2" style="border-left: 4px solid #198754 !important;" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            {{ session('success') }}
            </div>
    @endif

    <div class="row g-3">

        {{-- Posting Details Card --}}
        <div class="col-lg-8">
            
            <div class="gradient-header-sm d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded p-2 me-3">
                        <i class="bi bi-briefcase text-white fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">{{ $posting->title }}</h5>
                        <div class="small mt-1" style="color: rgba(255,255,255,0.85); font-size: 0.8rem;">
                            <i class="bi bi-building me-1"></i> Department: <span class="font-monospace text-white">{{ $posting->dept_code }}</span>
                        </div>
                    </div>
                </div>
                <div>
                     @if($posting->is_active)
                        <span class="badge bg-success border border-light border-opacity-50 px-3 py-1 rounded-pill shadow-sm fw-normal"><i class="bi bi-check-circle-fill me-1"></i> Active</span>
                    @else
                        <span class="badge bg-danger border border-light border-opacity-50 px-3 py-1 rounded-pill shadow-sm fw-normal"><i class="bi bi-eye-slash-fill me-1"></i> Inactive</span>
                    @endif
                </div>
            </div>

            <div class="card detail-card h-100">
                <div class="card-body p-3 p-md-4">
                    
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2 text-primary"></i> Listing Information</h6>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="detail-label">Track Type</div>
                            <div class="detail-value">
                                @php
                                    $badge = match($posting->track_type) {
                                        'fellowship' => 'badge-soft-primary',
                                        'nursing'    => 'badge-soft-info',
                                        default      => 'badge-soft-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} px-2 py-1 fs-7 rounded-pill fw-semibold">{{ ucfirst($posting->track_type) }}</span>
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="detail-label">Required Capacity</div>
                            <div class="detail-value">
                                @if($posting->needed_applicants > 0)
                                    <span class="text-primary fw-bolder fs-5">{{ $posting->needed_applicants }}</span> <span class="text-muted fs-7">seats remaining</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 fs-7 rounded-pill"><i class="bi bi-fire me-1"></i> High Demand</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <div class="detail-label">Job Description / Requirements</div>
                            <div class="detail-value p-3 bg-light rounded-3 text-muted" style="line-height: 1.5; font-size: 0.9rem;">
                                {{ $posting->description ?? 'No specific description provided for this role.' }}
                            </div>
                        </div>

                        <div class="col-sm-6 mt-3">
                            <div class="detail-label"><i class="bi bi-clock-history me-1"></i> Date Created</div>
                            <div class="detail-value text-muted" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($posting->created_at)->format('M d, Y \a\t h:i A') }}</div>
                        </div>

                        <div class="col-sm-6 mt-3">
                            <div class="detail-label"><i class="bi bi-pencil-square me-1"></i> Last Modified</div>
                            <div class="detail-value text-muted" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($posting->updated_at)->format('M d, Y \a\t h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Card --}}
        <div class="col-lg-4">
            <div class="card detail-card sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-lightning-charge text-warning me-2"></i> Quick Actions</h6>
                </div>
                <div class="card-body p-3 d-flex flex-column gap-2">

                    {{-- Toggle Active/Inactive --}}
                    <form method="POST" action="{{ route('hr1.recruitment.toggle', $posting->id) }}" class="mb-1">
                        @csrf
                        @if($posting->is_active)
                            <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold rounded-3 shadow-sm btn-sm">
                                <i class="bi bi-pause-circle me-1"></i> Pause Posting
                            </button>
                            <div class="text-muted mt-2 text-center bg-light p-2 rounded" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Pausing hides listing from public view.
                            </div>
                        @else
                            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold rounded-3 shadow-sm btn-sm">
                                <i class="bi bi-play-circle me-1"></i> Activate Posting
                            </button>
                            <div class="text-muted mt-2 text-center bg-light p-2 rounded" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Activating makes listing visible.
                            </div>
                        @endif
                    </form>

                    <hr class="text-muted opacity-25 my-1">

                    <a href="{{ route('hr1.recruitment.index') }}" class="btn btn-light border text-muted w-100 py-1 fw-semibold rounded-3 pb-2 pt-2 shadow-sm hover-primary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Directory
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
