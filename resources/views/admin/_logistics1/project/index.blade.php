@extends('admin._logistics1.layouts.app')

@section('content')

<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-header h4 { font-size: 1.3rem; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }
    .page-header p  { font-size: 0.82rem; color: #94a3b8; margin: 0; }

    .btn-add {
        background: #1e293b; color: #fff; border: none;
        padding: 0.45rem 1.1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 6px; transition: background 0.18s; cursor: pointer;
    }
    .btn-add:hover { background: #334155; color: #fff; }

    /* Tabs */
    .proc-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 5px; border-radius: 10px; margin-bottom: 1.5rem; width: fit-content; }
    .proc-tab  { padding: 0.45rem 1rem; border-radius: 7px; font-size: 0.82rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; white-space: nowrap; }
    .proc-tab:hover  { color: #1e293b; background: #e2e8f0; }
    .proc-tab.active { background: #1e293b; color: #fff; box-shadow: 0 2px 8px rgba(30,41,59,0.18); }

    .tab-desc { font-size: 0.82rem; color: #64748b; margin-bottom: 1.1rem; }

    .filter-bar {
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 10px; padding: 0.9rem 1rem; margin-bottom: 1.25rem;
    }
    .filter-bar .form-control,
    .filter-bar .form-select {
        border: 1px solid #e2e8f0; border-radius: 7px;
        font-size: 0.82rem; background: #fff; color: #1e293b;
        padding: 0.4rem 0.75rem; box-shadow: none;
    }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .btn-filter {
        background: #1e293b; color: #fff; border: none;
        border-radius: 7px; font-size: 0.82rem; font-weight: 600;
        padding: 0.4rem 1rem; cursor: pointer;
    }
    .btn-filter:hover { background: #334155; color: #fff; }

    .data-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
    .data-table { margin: 0; font-size: 0.82rem; }
    .data-table thead tr { background: #1e293b; }
    .data-table thead th {
        color: #94a3b8; font-weight: 600; font-size: 0.72rem;
        text-transform: uppercase; letter-spacing: 0.6px;
        padding: 0.85rem 1rem; border: none; white-space: nowrap;
    }
    .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody td { padding: 0.75rem 1rem; color: #334155; border: none; vertical-align: middle; }
    .row-num   { color: #94a3b8; font-size: 0.75rem; }
    .code-pill { background: #f1f5f9; color: #475569; border-radius: 5px; padding: 2px 8px; font-size: 0.73rem; font-family: monospace; font-weight: 600; }
    .row-title { font-weight: 600; color: #1e293b; }
    .row-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .badge-pill { border-radius: 20px; padding: 3px 10px; font-size: 0.72rem; font-weight: 600; display: inline-block; }
    .bp-planned   { background: #f1f5f9; color: #475569; }
    .bp-ongoing   { background: #dbeafe; color: #1d4ed8; }
    .bp-completed { background: #dcfce7; color: #16a34a; }
    .bp-on_hold   { background: #fef9c3; color: #a16207; }
    .bp-low       { background: #dcfce7; color: #16a34a; }
    .bp-normal    { background: #dbeafe; color: #1d4ed8; }
    .bp-high      { background: #ffedd5; color: #c2410c; }
    .bp-critical  { background: #fee2e2; color: #b91c1c; }

    .btn-act { width: 30px; height: 30px; border-radius: 7px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; transition: all 0.15s; cursor: pointer; }
    .btn-act-edit   { background: #fef9c3; color: #a16207; }
    .btn-act-edit:hover   { background: #fde68a; }
    .btn-act-delete { background: #fee2e2; color: #b91c1c; }
    .btn-act-delete:hover { background: #fecaca; }

    .empty-state { padding: 3.5rem 1rem; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2.2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }

    /* ── Planning timeline bar ── */
    .timeline-bar-wrap { background: #f1f5f9; border-radius: 6px; height: 8px; min-width: 100px; overflow: hidden; }
    .timeline-bar      { height: 8px; border-radius: 6px; background: #3b82f6; }

    /* ── Reporting stat cards ── */
    .stat-cards { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .stat-card {
        flex: 1; min-width: 130px;
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 10px; padding: 0.9rem 1rem;
    }
    .stat-card .sc-label { font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-card .sc-value { font-size: 1.4rem; font-weight: 700; color: #1e293b; margin-top: 2px; }
    .stat-card .sc-sub   { font-size: 0.72rem; color: #94a3b8; margin-top: 1px; }


    .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header  { background: #1e293b; border-radius: 14px 14px 0 0; padding: 1rem 1.25rem; border: none; }
    .modal-header .modal-title { color: #f8fafc; font-size: 0.95rem; font-weight: 700; }
    .modal-header .btn-close   { filter: invert(1) brightness(2); }
    .modal-body  { padding: 1.25rem; }
    .modal-footer { padding: 0.9rem 1.25rem; border-top: 1px solid #f1f5f9; }
    .modal-body .form-label { font-size: 0.78rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .modal-body .form-control,
    .modal-body .form-select { font-size: 0.83rem; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; padding: 0.45rem 0.75rem; box-shadow: none; }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .modal-body .form-control:disabled { background: #f8fafc; color: #94a3b8; }
    .btn-mc { background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 1rem; cursor: pointer; }
    .btn-mc:hover { background: #e2e8f0; }
    .btn-ms { background: #1e293b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-ms:hover { background: #334155; color: #fff; }
    .btn-mu { background: #f59e0b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-mu:hover { background: #d97706; }
</style>

{{-- Page Header --}}
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-kanban me-2"></i>Project Management</h4>
        <p>Track and manage all logistics projects</p>
    </div>

</div>

{{-- Tab Navigation --}}
<div class="proc-tabs">
    <a class="proc-tab {{ $activeTab === 'planning_scheduling' ? 'active' : '' }}" href="{{ route('admin.logistics1.project_management.index', ['tab' => 'planning_scheduling']) }}">
        <i class="bi bi-calendar3"></i> Planning &amp; Scheduling
    </a>
    <a class="proc-tab {{ $activeTab === 'reporting_monitoring' ? 'active' : '' }}" href="{{ route('admin.logistics1.project_management.index', ['tab' => 'reporting_monitoring']) }}">
        <i class="bi bi-bar-chart-line"></i> Reporting &amp; Monitoring
    </a>
    <a class="proc-tab {{ $activeTab === 'communication_collaboration' ? 'active' : '' }}" href="{{ route('admin.logistics1.project_management.index', ['tab' => 'communication_collaboration']) }}">
        <i class="bi bi-chat-dots"></i> Communication &amp; Collaboration
    </a>
</div>

{{-- ================================================================
     TAB 2: PLANNING & SCHEDULING
================================================================ --}}
@if($activeTab === 'planning_scheduling')

<div class="d-flex justify-content-between align-items-center mb-2">
    <p class="tab-desc mb-0">Active and upcoming projects ordered by nearest start date, with timeline progress.</p>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addProjectModal">
        <i class="bi bi-plus-lg"></i> New Project
    </button>
</div>

<form method="GET" action="{{ route('admin.logistics1.project_management.index') }}">
    <input type="hidden" name="tab" value="planning_scheduling">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="planning_status" class="form-select">
                <option value="">All Statuses</option>
                <option value="planned"  {{ request('planning_status')==='planned'  ?'selected':'' }}>Planned</option>
                <option value="ongoing"  {{ request('planning_status')==='ongoing'  ?'selected':'' }}>Ongoing</option>
                <option value="on_hold"  {{ request('planning_status')==='on_hold'  ?'selected':'' }}>On Hold</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="planning_priority" class="form-select">
                <option value="">All Priorities</option>
                <option value="low"      {{ request('planning_priority')==='low'      ?'selected':'' }}>Low</option>
                <option value="normal"   {{ request('planning_priority')==='normal'   ?'selected':'' }}>Normal</option>
                <option value="high"     {{ request('planning_priority')==='high'     ?'selected':'' }}>High</option>
                <option value="critical" {{ request('planning_priority')==='critical' ?'selected':'' }}>Critical</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn-filter w-100">Go</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Project Name</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Timeline</th>
                <th>Budget</th>
            </tr>
        </thead>
        <tbody>
            @forelse($planning as $project)
            @php
                $start    = \Carbon\Carbon::parse($project->start_date);
                $end      = $project->end_date ? \Carbon\Carbon::parse($project->end_date) : null;
                $today    = now();
                $total    = $end ? $start->diffInDays($end) : 0;
                $elapsed  = $start->diffInDays($today, false);
                $progress = ($total > 0) ? min(100, max(0, round(($elapsed / $total) * 100))) : 0;
            @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($planning->currentPage()-1) * $planning->perPage() }}</span></td>
                <td><span class="code-pill">{{ $project->project_code }}</span></td>
                <td>
                    <div class="row-title">{{ $project->project_name }}</div>
                    @if($project->description)<div class="row-sub">{{ Str::limit($project->description, 50) }}</div>@endif
                </td>
                <td><span class="badge-pill bp-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
                <td><span class="badge-pill bp-{{ $project->priority }}">{{ ucfirst($project->priority) }}</span></td>
                <td>{{ $start->format('M d, Y') }}</td>
                <td>{{ $end ? $end->format('M d, Y') : '—' }}</td>
                <td style="min-width:120px;">
                    <div class="timeline-bar-wrap">
                        <div class="timeline-bar" style="width:{{ $progress }}%;
                            background: {{ $progress >= 100 ? '#16a34a' : ($progress >= 60 ? '#3b82f6' : '#f59e0b') }};"></div>
                    </div>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:2px;">{{ $progress }}% elapsed</div>
                </td>
                <td>₱{{ number_format($project->budget, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="9"><div class="empty-state"><i class="bi bi-calendar3"></i><p>No active or upcoming projects found.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $planning->withQueryString()->links() }}</div>

@endif

{{-- ================================================================
     TAB 3: REPORTING & MONITORING
================================================================ --}}
@if($activeTab === 'reporting_monitoring')

<p class="tab-desc">Budget vs actual cost tracking and project status summary.</p>

{{-- Summary Cards --}}
<div class="stat-cards">
    <div class="stat-card">
        <div class="sc-label">Total Projects</div>
        <div class="sc-value">{{ $reportingStats->total_projects }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">Planned</div>
        <div class="sc-value">{{ $reportingStats->total_planned }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">Ongoing</div>
        <div class="sc-value">{{ $reportingStats->total_ongoing }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">Completed</div>
        <div class="sc-value">{{ $reportingStats->total_completed }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">On Hold</div>
        <div class="sc-value">{{ $reportingStats->total_on_hold }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">Total Budget</div>
        <div class="sc-value" style="font-size:1rem;">₱{{ number_format($reportingStats->total_budget, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="sc-label">Total Actual Cost</div>
        <div class="sc-value" style="font-size:1rem;">₱{{ number_format($reportingStats->total_actual_cost, 2) }}</div>
        @php $variance = $reportingStats->total_budget - $reportingStats->total_actual_cost; @endphp
        <div class="sc-sub" style="color: {{ $variance >= 0 ? '#16a34a' : '#b91c1c' }}">
            {{ $variance >= 0 ? '▼ Under' : '▲ Over' }} by ₱{{ number_format(abs($variance), 2) }}
        </div>
    </div>
</div>

<form method="GET" action="{{ route('admin.logistics1.project_management.index') }}">
    <input type="hidden" name="tab" value="reporting_monitoring">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search by name or code..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <select name="reporting_status" class="form-select">
                <option value="">All Statuses</option>
                <option value="planned"   {{ request('reporting_status')==='planned'   ?'selected':'' }}>Planned</option>
                <option value="ongoing"   {{ request('reporting_status')==='ongoing'   ?'selected':'' }}>Ongoing</option>
                <option value="completed" {{ request('reporting_status')==='completed' ?'selected':'' }}>Completed</option>
                <option value="on_hold"   {{ request('reporting_status')==='on_hold'   ?'selected':'' }}>On Hold</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Go</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Project Name</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Budget</th>
                <th>Actual Cost</th>
                <th>Variance</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reporting as $project)
            @php $var = $project->budget - $project->actual_cost; @endphp
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($reporting->currentPage()-1) * $reporting->perPage() }}</span></td>
                <td><span class="code-pill">{{ $project->project_code }}</span></td>
                <td><div class="row-title">{{ $project->project_name }}</div></td>
                <td><span class="badge-pill bp-{{ $project->status }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></td>
                <td><span class="badge-pill bp-{{ $project->priority }}">{{ ucfirst($project->priority) }}</span></td>
                <td>₱{{ number_format($project->budget, 2) }}</td>
                <td>₱{{ number_format($project->actual_cost, 2) }}</td>
                <td style="color: {{ $var >= 0 ? '#16a34a' : '#b91c1c' }}; font-weight:600;">
                    {{ $var >= 0 ? '+' : '' }}₱{{ number_format($var, 2) }}
                </td>
                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</td>
                <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="10"><div class="empty-state"><i class="bi bi-bar-chart-line"></i><p>No data available.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $reporting->withQueryString()->links() }}</div>

@endif

{{-- ================================================================
     TAB 4: COMMUNICATION & COLLABORATION
================================================================ --}}
@if($activeTab === 'communication_collaboration')

<div class="d-flex justify-content-between align-items-center mb-2">
    <p class="tab-desc mb-0">Team messaging and collaboration tools for active projects.</p>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#postUpdateModal">
        <i class="bi bi-send"></i> Post Update
    </button>
</div>

<div class="data-card">
    <div class="empty-state">
        <i class="bi bi-chat-dots"></i>
        <p>No updates yet. Use the <strong>Post Update</strong> button to share a message with your team.</p>
    </div>
</div>

{{-- POST UPDATE MODAL --}}
<div class="modal fade" id="postUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-send me-2"></i>Post Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Project</label>
                        <select class="form-select" name="project_id">
                            <option value="">— Select a project —</option>
                            @foreach($projectOptions as $option)
                            <option value="{{ $option->id }}">{{ $option->project_code }} — {{ $option->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="note">Note</option>
                            <option value="update">Update</option>
                            <option value="alert">Alert</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" rows="4" required placeholder="Write your update or note here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-ms"><i class="bi bi-send me-1"></i>Post</button>
            </div>
        </div>
    </div>
</div>

@endif

{{-- ================================================================
     MODALS (Projects tab)
================================================================ --}}

{{-- ADD MODAL --}}
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.project_management.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-kanban me-2"></i>New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control" required maxlength="255" placeholder="Enter project name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="planned">Planned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Budget (₱)</label>
                            <input type="number" name="budget" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Actual Cost (₱)</label>
                            <input type="number" name="actual_cost" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-ms"><i class="bi bi-check-lg me-1"></i>Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editProjectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Project Code</label>
                            <input type="text" class="form-control" id="edit_project_code" disabled>
                            <small style="font-size:0.72rem;color:#94a3b8;">Auto-generated, cannot be changed.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="edit_project_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="planned">Planned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="edit_priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Budget (₱)</label>
                            <input type="number" name="budget" id="edit_budget" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Actual Cost (₱)</label>
                            <input type="number" name="actual_cost" id="edit_actual_cost" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-mu"><i class="bi bi-check-lg me-1"></i>Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editProjectModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const form = document.getElementById('editProjectForm');
    form.action = `/admin/logistics1/project-management/update/${btn.dataset.id}`;
    document.getElementById('edit_project_code').value  = btn.dataset.project_code;
    document.getElementById('edit_project_name').value  = btn.dataset.project_name;
    document.getElementById('edit_description').value   = btn.dataset.description ?? '';
    document.getElementById('edit_start_date').value    = btn.dataset.start_date;
    document.getElementById('edit_end_date').value      = btn.dataset.end_date ?? '';
    document.getElementById('edit_status').value        = btn.dataset.status;
    document.getElementById('edit_priority').value      = btn.dataset.priority;
    document.getElementById('edit_budget').value        = btn.dataset.budget;
    document.getElementById('edit_actual_cost').value   = btn.dataset.actual_cost;
});
</script>

@endsection     