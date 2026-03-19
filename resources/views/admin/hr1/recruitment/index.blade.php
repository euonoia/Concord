@extends('admin.hr1.layouts.app')

@section('content')
<style>
    .gradient-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        min-width: 185px;
        margin-right: 16px;
        margin-bottom: 12px;
    }
    .stat-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .stat-chip-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .stat-chip-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #8a90a0;
        margin-bottom: 1px;
    }
    .stat-chip-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1;
    }
    .table-panel {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-panel-header {
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-input {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 12px 6px 34px;
        font-size: 0.85rem;
        outline: none;
        width: 240px;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #f8f9fa;
    }
    .search-input:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42,82,152,0.08);
        background: #fff;
    }
    .search-wrap {
        position: relative;
    }
    .search-wrap .bi-search {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.8rem;
    }
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin: 0;
    }
    .table-modern thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8a90a0;
        border: none;
        padding: 10px 14px;
        background: #f8f9fa;
    }
    .table-modern tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .table-modern tbody tr:last-child {
        border-bottom: none;
    }
    .table-modern tbody tr:hover {
        background: #f8fbff;
    }
    .table-modern tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        border: none;
        font-size: 0.875rem;
    }
    .badge-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    .badge-soft-info    { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.12); color: #6c757d; }
    .badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .badge-soft-danger  { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .no-results-row { display: none; }
</style>

<div class="container py-4">

    {{-- Page Header --}}
    <div class="gradient-header d-flex justify-content-between align-items-center flex-wrap" style="margin-bottom:24px; gap:12px;">
        <div>
            <h4 class="fw-bold text-white" style="margin:0;"><i class="bi bi-megaphone me-2"></i>Recruitment Dashboard</h4>
            <p class="text-white" style="margin:4px 0 0; opacity:0.85; font-size:0.82rem;">Manage job postings, tracks, and applicant capacities.</p>
        </div>
        <span style="font-size:0.78rem; background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.4); border-radius:20px; padding:5px 14px; backdrop-filter:blur(4px); white-space:nowrap;">
            <i class="bi bi-calendar-check me-1"></i> {{ now()->format('M Y') }} Hiring Cycle
        </span>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 mb-3" style="border-left:4px solid #198754 !important;" role="alert">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Chips --}}
    <div class="d-flex flex-wrap" style="margin-bottom: 28px;">

        <div class="stat-chip" style="border-left:4px solid #0d6efd; padding-top:14px !important; padding-bottom:14px !important; padding-left:20px !important; padding-right:20px !important;">
            <div class="stat-chip-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-chip-label">Total Seats Needed</div>
                <div class="stat-chip-value">{{ $totalNeeded }}</div>
            </div>
        </div>

        <div class="stat-chip" style="border-left:4px solid #198754; padding-top:14px !important; padding-bottom:14px !important; padding-left:20px !important; padding-right:20px !important;">
            <div class="stat-chip-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="stat-chip-label">Active Postings</div>
                <div class="stat-chip-value">{{ $activeCount }}</div>
            </div>
        </div>

        <div class="stat-chip" style="border-left:4px solid #dc3545; padding-top:14px !important; padding-bottom:14px !important; padding-left:20px !important; padding-right:20px !important;">
            <div class="stat-chip-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-pause-circle"></i>
            </div>
            <div>
                <div class="stat-chip-label">Inactive Postings</div>
                <div class="stat-chip-value">{{ $inactiveCount }}</div>
            </div>
        </div>

    </div>

    {{-- HR4 Job Requests Card --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-briefcase me-2"></i>Job Requests from HR4 (Core Human Capital)</h6>
            <span class="badge bg-primary rounded-pill">{{ $hr4Jobs->count() }} Pending Requests</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th class="ps-4">Job Title</th>
                            <th>Department</th>
                            <th class="text-center">Positions</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hr4Jobs as $job)
                            @php
                                $isPublished = $postings->where('hr4_job_id', $job->id)->isNotEmpty();
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $job->title }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 300px;">{{ $job->description }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $job->department }}</span></td>
                                <td class="text-center fw-bold">{{ $job->positions_available }}</td>
                                <td class="text-end pe-4">
                                    @if($isPublished)
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="bi bi-check-circle-fill me-1"></i>Published
                                        </button>
                                    @else
                                        <form action="{{ route('hr1.recruitment.publishHr4', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary shadow-sm px-3">
                                                <i class="bi bi-megaphone-fill me-1"></i>Publish to HR1
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle me-2"></i>No pending job requests from HR4.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Table Panel --}}
    <div class="table-panel">
        <div class="table-panel-header">
            <h6 class="fw-bold text-dark mb-0" style="font-size:0.9rem;"><i class="bi bi-list-ul me-2 text-primary"></i>Current Postings Directory</h6>
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" id="jobSearch" class="search-input" placeholder="Search job title, dept...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle" id="jobTable">
                <thead>
                    <tr>
                        <th style="width:60px;">ID #</th>
                        <th>Job Title</th>
                        <th>Track</th>
                        <th>Dept. Code</th>
                        <th class="text-center">Capacity</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width:90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postings as $p)
                    <tr class="job-row">
                        <td class="text-muted fw-semibold" style="font-size:0.78rem;">#{{ str_pad($p->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-semibold text-dark job-title" style="font-size:0.88rem;">{{ $p->title }}</div>
                            <div class="text-muted text-truncate" style="font-size:0.74rem; max-width:240px;">{{ $p->description }}</div>
                        </td>
                        <td>
                            @php
                                $badge = match($p->track_type) {
                                    'fellowship' => 'badge-soft-primary',
                                    'nursing'    => 'badge-soft-info',
                                    default      => 'badge-soft-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge }} rounded-pill px-2 py-1" style="font-size:0.68rem;">{{ ucfirst($p->track_type) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace job-dept" style="font-size:0.68rem;">{{ $p->dept_code }}</span>
                        </td>
                        <td class="text-center">
                            @if($p->needed_applicants > 0)
                                <span class="fw-bold text-dark" style="font-size:0.85rem;">{{ $p->needed_applicants }}</span>
                            @else
                                <span class="badge bg-danger rounded-pill" style="font-size:0.65rem;"><i class="bi bi-fire me-1"></i>Full</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->is_active)
                                <span class="badge badge-soft-success rounded-pill px-2 py-1" style="font-size:0.68rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.38rem;"></i>Active</span>
                            @else
                                <span class="badge badge-soft-danger rounded-pill px-2 py-1" style="font-size:0.68rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.38rem;"></i>Paused</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('hr1.recruitment.show', $p->id) }}"
                                   class="btn btn-sm btn-light border text-primary rounded-2 py-0 px-2"
                                   data-bs-toggle="tooltip" title="View">
                                    <i class="bi bi-eye-fill" style="font-size:0.85rem;"></i>
                                </a>
                                <form method="POST" action="{{ route('hr1.recruitment.toggle', $p->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm rounded-2 py-0 px-2 {{ $p->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        data-bs-toggle="tooltip" title="{{ $p->is_active ? 'Pause' : 'Activate' }}">
                                        <i class="bi bi-{{ $p->is_active ? 'pause-fill' : 'play-fill' }}" style="font-size:0.85rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">No Job Postings Found</div>
                            <div class="small">There are currently no job postings in the system.</div>
                        </td>
                    </tr>
                    @endforelse
                    <tr class="no-results-row" id="noResultsRow">
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-search d-block mb-2" style="font-size:1.5rem; opacity:0.35;"></i>
                            <div class="fw-semibold small">No results match your search.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.getElementById('jobSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#jobTable .job-row');
    let visible = 0;

    rows.forEach(row => {
        const title = row.querySelector('.job-title')?.textContent.toLowerCase() || '';
        const dept  = row.querySelector('.job-dept')?.textContent.toLowerCase()  || '';
        const match = title.includes(query) || dept.includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    document.getElementById('noResultsRow').style.display = (visible === 0 && query.length > 0) ? '' : 'none';
});
</script>

@endsection
