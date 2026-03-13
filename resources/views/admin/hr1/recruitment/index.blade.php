@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">Recruitment Hub</h3>
            <p class="text-white">Manage job postings, tracks, and applicant capacities.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
             <span class="text-white small fw-bold"><i class="bi bi-calendar-check me-2"></i>Mar 2026 Hiring Cycle</span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show hr1-mb-5" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Row --}}
    <div class="hr1-metrics-container">
        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-accent);">
            <div class="hr1-metric-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="hr1-metric-label">Total Seats<br>Needed</div>
            <div class="hr1-metric-value">{{ $totalNeeded }}</div>
        </div>

        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-success);">
            <div class="hr1-metric-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="hr1-metric-label">Active<br>Postings</div>
            <div class="hr1-metric-value">{{ $activeCount }}</div>
        </div>

        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-danger);">
            <div class="hr1-metric-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="hr1-metric-label">Inactive<br>Postings</div>
            <div class="hr1-metric-value">{{ $inactiveCount }}</div>
        </div>
    </div>

    {{-- Job Postings Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6 class="mb-0">Job Openings</h6>
            <div class="d-flex" style="gap: 10px;">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="jobSearch" class="form-control border-start-0" placeholder="Search postings...">
                </div>
                <a href="#" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-plus-lg me-1"></i> New Post</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0" id="jobsTable">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Dept Code</th>
                        <th>Track</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postings as $job)
                    <tr class="job-row">
                        <td>
                            <div class="fw-bold text-dark job-title">{{ $job->title }}</div>
                            <div class="small text-muted">Posted {{ \Carbon\Carbon::parse($job->created_at)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <code class="text-primary fw-bold dept-code">{{ $job->dept_code }}</code>
                        </td>
                        <td>
                            <span class="text-capitalize small fw-semibold">{{ str_replace('_', ' ', $job->track_type) }}</span>
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $job->needed_applicants }}
                        </td>
                        <td>
                            @if($job->is_active)
                                <span class="hr1-badge hr1-badge-success">Active</span>
                            @else
                                <span class="hr1-badge hr1-badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('hr1.recruitment.show', $job->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No job postings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('jobSearch').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        let rows = document.querySelectorAll('.job-row');
        
        rows.forEach(row => {
            let title = row.querySelector('.job-title').textContent.toLowerCase();
            let dept = row.querySelector('.dept-code').textContent.toLowerCase();
            
            if (title.includes(val) || dept.includes(val)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
