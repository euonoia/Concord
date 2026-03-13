@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">Applicant Management</h3>
            <p class="text-white">Review and filter all submitted residency and fellowship applications.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
             <span class="text-white small fw-bold"><i class="bi bi-person-badge-fill me-2"></i>Database Overview</span>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="hr1-filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">-- All Departments --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->department_id }}" {{ ($filters['department'] ?? '') == $d->department_id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Specialization</label>
                <select name="specialization" class="form-select form-select-sm">
                    <option value="">-- All Specializations --</option>
                    @foreach($specializations as $s)
                        <option value="{{ $s->specialization_name }}" {{ ($filters['specialization'] ?? '') == $s->specialization_name ? 'selected' : '' }}>
                            {{ $s->specialization_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Application Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- All Statuses --</option>
                    @foreach(['pending','under_review','interview','accepted','rejected','onboarded'] as $status)
                        <option value="{{ $status }}" {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ',$status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100 rounded-pill"><i class="bi bi-funnel me-1"></i> Apply Filters</button>
            </div>
        </form>
    </div>

    {{-- Applicants Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6>Candidate Pool</h6>
            <div class="small text-muted">{{ $applicants->total() }} total applicants matched</div>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Applicant Details</th>
                        <th>Department</th>
                        <th>Specialization</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $a)
                        <tr>
                            <td class="text-muted font-monospace" style="font-size: 0.75rem;">#{{ $a->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $a->first_name }} {{ $a->last_name }}</div>
                                <div class="small text-muted">{{ $a->email }} | {{ $a->phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">{{ $a->department_name }}</span>
                            </td>
                            <td>
                                <span class="small fw-semibold text-muted">{{ $a->specialization ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($a->application_status) {
                                        'accepted', 'onboarded' => 'hr1-badge-success',
                                        'rejected' => 'hr1-badge-danger',
                                        'interview' => 'hr1-badge-info',
                                        'under_review' => 'hr1-badge-warning',
                                        default => 'hr1-badge-primary',
                                    };
                                @endphp
                                <span class="hr1-badge {{ $statusBadge }}">{{ ucfirst(str_replace('_',' ',$a->application_status)) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('hr1.applicants.show', $a->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Profile</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No applicants found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applicants->hasPages())
            <div class="px-3 py-2 border-top bg-light">
                {{ $applicants->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection