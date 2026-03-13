@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">Applicant Detail</h3>
            <p class="text-white">Full profile and application management for <strong>{{ $applicant->first_name }} {{ $applicant->last_name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('hr1.applicants.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Pool
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show hr1-mb-5" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Side: Details --}}
        <div class="col-lg-8">
            <div class="hr1-premium-table-card hr1-mb-5">
                <div class="hr1-table-header">
                    <h6>Personal & Professional Information</h6>
                    <span class="hr1-badge hr1-badge-info">Record #{{ $applicant->id }}</span>
                </div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Full Name</label>
                            <div class="fw-bold text-dark fs-5">{{ $applicant->first_name }} {{ $applicant->last_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Email Address</label>
                            <div class="text-dark">{{ $applicant->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Phone Number</label>
                            <div class="text-dark">{{ $applicant->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Department</label>
                            <div><span class="badge bg-light text-dark border">{{ $applicant->department_name }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Specialization</label>
                            <div class="text-dark fw-semibold">{{ $applicant->specialization ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold text-uppercase">Status</label>
                            <div>
                                @php
                                    $statusBadge = match($applicant->application_status) {
                                        'accepted', 'onboarded' => 'hr1-badge-success',
                                        'rejected' => 'hr1-badge-danger',
                                        'interview' => 'hr1-badge-info',
                                        'under_review' => 'hr1-badge-warning',
                                        default => 'hr1-badge-primary',
                                    };
                                @endphp
                                <span class="hr1-badge {{ $statusBadge }}">{{ ucfirst(str_replace('_',' ',$applicant->application_status)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interview Details --}}
            @if($applicant->schedule_date)
            <div class="hr1-premium-table-card border-start border-info border-4">
                <div class="hr1-table-header" style="background: rgba(13, 202, 240, 0.03);">
                    <h6><i class="bi bi-calendar-event me-2 text-info"></i>Interview Schedule</h6>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Date & Time</label>
                            <div class="fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($applicant->schedule_date)->format('F d, Y') }}<br>
                                <span class="text-info">{{ \Carbon\Carbon::parse($applicant->schedule_time)->format('h:i A') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Location</label>
                            <div class="text-dark">{{ $applicant->interview_location ?? 'No location set' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold text-uppercase">Validator</label>
                            <div class="text-dark">{{ $applicant->validator_first }} {{ $applicant->validator_last }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Side: Actions --}}
        <div class="col-lg-4">
            <div class="hr1-premium-table-card hr1-mb-5">
                <div class="hr1-table-header">
                    <h6>Decision Center</h6>
                </div>
                <div class="p-4">
                    @php
                        $interviewDateTime = $applicant->schedule_date 
                            ? \Carbon\Carbon::parse($applicant->schedule_date . ' ' . $applicant->schedule_time) 
                            : null;
                        $isInterviewFinished = $interviewDateTime && $interviewDateTime->isPast();
                    @endphp

                    <form action="{{ route('hr1.applicants.updateStatus', $applicant->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold text-uppercase">Transition Status</label>
                            <select name="application_status" class="form-select form-select-sm rounded-pill" required>
                                @foreach(['pending','under_review','interview','rejected'] as $status)
                                    <option value="{{ $status }}" {{ $applicant->application_status == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ',$status)) }}
                                    </option>
                                @endforeach

                                @if($isInterviewFinished)
                                    <option value="accepted" {{ $applicant->application_status == 'accepted' ? 'selected' : '' }}>
                                        Accepted (Post-Interview)
                                    </option>
                                @else
                                    <option value="" disabled>--- Accept Locked ---</option>
                                @endif
                            </select>

                            @if(!$isInterviewFinished && $applicant->schedule_date)
                                <div class="mt-2" style="font-size: 0.7rem; color: var(--hr1-danger);">
                                    <i class="bi bi-lock-fill"></i> Accept is locked until interview completes.
                                </div>
                            @elseif(!$applicant->schedule_date)
                                 <div class="mt-2" style="font-size: 0.7rem; color: var(--hr1-warning);">
                                    <i class="bi bi-exclamation-triangle-fill"></i> No interview scheduled.
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm py-2 fw-bold">Update Candidate Status</button>
                    </form>
                </div>
            </div>

            {{-- Document Card --}}
            <div class="hr1-premium-table-card shadow-sm border-0">
                <div class="p-4 text-center">
                    <div class="mb-3 text-muted" style="font-size: 2rem;">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h6>Curriculum Vitae</h6>
                    @if($applicant->resume_path)
                        <a href="{{ route('hr1.applicants.download', $applicant->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 mt-2" target="_blank">
                            View Document
                        </a>
                    @else
                        <div class="text-muted small mt-2">No CV available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection