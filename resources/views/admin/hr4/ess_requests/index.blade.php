@extends('admin.hr4.layouts.app')

@section('title', 'ESS Payroll Requests - HR4')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:           #eef3f7;
        --c-surface:      #ffffff;
        --c-border:       #d4e3ee;
        --c-teal:         #0a7c6e;
        --c-teal-light:   #e4f4f1;
        --c-teal-mid:     #b8e0da;
        --c-blue:         #1a5f8a;
        --c-blue-light:   #e8f2f9;
        --c-green:        #1a7a52;
        --c-green-light:  #e4f5ed;
        --c-green-mid:    #b2ddc8;
        --c-red:          #be123c;
        --c-red-light:    #fce7ef;
        --c-yellow:       #92400e;
        --c-yellow-light: #fefce8;
        --c-yellow-border:#fde68a;
        --c-text:         #1b2b3a;
        --c-muted:        #5c798e;
        --c-line:         #dde8f0;
        --shadow-sm:      0 1px 4px rgba(10,50,80,.07);
        --shadow-hover:   0 10px 36px rgba(10,124,110,.13);
    }

    .aj * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .aj {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .aj-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .aj-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        line-height: 1.1;
        margin: 0;
    }

    .aj-header h1 em { color: var(--c-teal); font-style: italic; }

    .aj-header p {
        font-size: .85rem;
        color: var(--c-muted);
        margin-top: .3rem;
        margin-bottom: 0;
    }

    /* ── Buttons ── */
    .btn-teal {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: var(--c-teal);
        color: #fff;
        font-size: .83rem;
        font-weight: 600;
        padding: .55rem 1.2rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(10,124,110,.25);
        transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .btn-teal:hover {
        background: #0b9483;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(10,124,110,.35);
        color: #fff;
        text-decoration: none;
    }

    .btn-filter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        background: var(--c-blue);
        color: #fff;
        font-size: .8rem;
        font-weight: 600;
        padding: .52rem 1rem;
        border-radius: 7px;
        border: none;
        cursor: pointer;
        flex: 1;
        transition: background .2s;
    }

    .btn-filter:hover { background: #154f75; }

    .btn-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--c-bg);
        color: var(--c-muted);
        font-size: .8rem;
        font-weight: 600;
        padding: .52rem 1rem;
        border-radius: 7px;
        border: 1px solid var(--c-border);
        cursor: pointer;
        flex: 1;
        text-decoration: none;
        transition: background .2s;
    }

    .btn-clear:hover { background: #dde8f0; text-decoration: none; color: var(--c-muted); }

    /* ── Alerts ── */
    .aj-alert {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .9rem 1.2rem;
        background: var(--c-green-light);
        border: 1px solid var(--c-green-mid);
        border-left: 4px solid var(--c-teal);
        border-radius: 10px;
        color: var(--c-teal);
        font-size: .88rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        animation: fadeUp .4s ease both;
    }

    .aj-alert-error {
        background: var(--c-red-light);
        border-color: #f9a8c0;
        border-left-color: var(--c-red);
        color: var(--c-red);
    }

    /* ── Filter Box ── */
    .aj-filter-box {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        animation: fadeUp .4s ease both;
    }

    .aj-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .aj-filter-box label {
        display: block;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--c-muted);
        margin-bottom: .4rem;
    }

    .aj-filter-box select,
    .aj-filter-box input[type="date"] {
        width: 100%;
        border: 1px solid var(--c-border);
        border-radius: 7px;
        padding: .5rem .75rem;
        font-size: .82rem;
        color: var(--c-text);
        background: var(--c-bg);
        outline: none;
        transition: border .2s;
    }

    .aj-filter-box select:focus,
    .aj-filter-box input:focus { border-color: var(--c-teal); }

    /* ── Stats Grid ── */
    .aj-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
        animation: fadeUp .45s .1s ease both;
    }

    .aj-stat-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1rem 1.2rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .aj-stat-card .stat-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: .25rem;
    }

    .aj-stat-card .stat-val {
        font-size: 1.8rem;
        font-weight: 600;
        line-height: 1;
    }

    .aj-stat-card i { font-size: 2rem; opacity: .35; }

    .stat-pending .stat-label,
    .stat-pending .stat-val  { color: #92400e; }
    .stat-pending i          { color: #f59e0b; }

    .stat-approved .stat-label,
    .stat-approved .stat-val { color: var(--c-green); }
    .stat-approved i         { color: var(--c-green); }

    .stat-rejected .stat-label,
    .stat-rejected .stat-val { color: var(--c-red); }
    .stat-rejected i         { color: var(--c-red); }

    .stat-total .stat-label,
    .stat-total .stat-val    { color: var(--c-blue); }
    .stat-total i            { color: var(--c-blue); }

    /* ── Card ── */
    .aj-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeUp .5s .15s ease both;
    }

    /* ── Table ── */
    .aj-table { width: 100%; border-collapse: collapse; font-size: .83rem; }

    .aj-table thead th {
        background: var(--c-bg);
        color: var(--c-muted);
        font-size: .71rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding: .8rem 1.1rem;
        text-align: left;
        border-bottom: 1.5px solid var(--c-line);
        white-space: nowrap;
    }

    .aj-table tbody tr {
        border-bottom: 1px solid var(--c-line);
        transition: background .18s ease;
    }

    .aj-table tbody tr:hover { background: var(--c-teal-light); }
    .aj-table tbody tr:last-child { border-bottom: none; }

    .aj-table tbody td {
        padding: .85rem 1.1rem;
        color: var(--c-text);
        vertical-align: middle;
    }

    /* ── Employee Avatar ── */
    .emp-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--c-blue-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-blue);
        font-size: .9rem;
        flex-shrink: 0;
    }

    .emp-name { font-weight: 500; font-size: .85rem; color: var(--c-text); }
    .emp-id   { font-size: .72rem; color: var(--c-muted); }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .7rem;
        border-radius: 99px;
        font-size: .71rem;
        font-weight: 600;
    }

    .badge-blue   { background: var(--c-blue-light);   color: var(--c-blue); }
    .badge-green  { background: var(--c-green-light);  color: var(--c-green); }
    .badge-red    { background: var(--c-red-light);    color: var(--c-red); }
    .badge-yellow { background: var(--c-yellow-light); color: var(--c-yellow); border: 1px solid var(--c-yellow-border); }

    /* ── Action Links ── */
    .action-link {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        padding: .3rem .65rem;
        border-radius: 6px;
        transition: filter .18s ease;
        border: none;
        cursor: pointer;
    }

    .action-link:hover { filter: brightness(.9); text-decoration: none; }
    .action-link.view    { background: var(--c-blue-light);  color: var(--c-blue); }
    .action-link.approve { background: var(--c-green-light); color: var(--c-green); }
    .action-link.reject  { background: var(--c-red-light);   color: var(--c-red); }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 4rem 1rem; color: var(--c-muted); }

    .empty-state i {
        font-size: 2.8rem;
        display: block;
        margin-bottom: .75rem;
        opacity: .25;
        color: var(--c-teal);
    }

    .empty-state h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--c-text);
        margin: 0 0 .3rem;
    }

    .empty-state p { font-size: .83rem; margin: 0; }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity: 0; transform: translateY(18px);  } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="aj">

    {{-- ── Header ── --}}
    <div class="aj-header">
        <div>
            <h1><em>Payroll</em> Requests</h1>
            <p>Manage employee self-service payroll requests</p>
        </div>
        <button onclick="syncRequests()" class="btn-teal">
            <i class="bi bi-arrow-clockwise"></i> Sync from HR2
        </button>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('success'))
        <div class="aj-alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="aj-alert aj-alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Filter Box ── --}}
    <div class="aj-filter-box">
        <form method="GET" action="{{ route('hr4.ess_requests.index') }}">
            <div class="aj-filter-grid">
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label>Request Type</label>
                    <select name="type">
                        <option value="">All Types</option>
                        <option value="payroll"   {{ request('type') == 'payroll'   ? 'selected' : '' }}>Payroll</option>
                        <option value="bonus"     {{ request('type') == 'bonus'     ? 'selected' : '' }}>Bonus</option>
                        <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>Deduction</option>
                    </select>
                </div>
                <div>
                    <label>Date Range</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('hr4.ess_requests.index') }}" class="btn-clear">Clear</a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Stats Grid ── --}}
    <div class="aj-stats-grid">
        <div class="aj-stat-card stat-pending">
            <div>
                <div class="stat-label">Pending</div>
                <div class="stat-val">{{ $pendingCount ?? 0 }}</div>
            </div>
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="aj-stat-card stat-approved">
            <div>
                <div class="stat-label">Approved</div>
                <div class="stat-val">{{ $approvedCount ?? 0 }}</div>
            </div>
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="aj-stat-card stat-rejected">
            <div>
                <div class="stat-label">Rejected</div>
                <div class="stat-val">{{ $rejectedCount ?? 0 }}</div>
            </div>
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="aj-stat-card stat-total">
            <div>
                <div class="stat-label">Total</div>
                <div class="stat-val">{{ $totalCount ?? 0 }}</div>
            </div>
            <i class="bi bi-files"></i>
        </div>
    </div>

    {{-- ── Requests Table ── --}}
    <div class="aj-card">
        <table class="aj-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th>Details</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests ?? [] as $request)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:.6rem;">
                                <div class="emp-avatar">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div class="emp-name">
                                        {{ $request->employee->first_name ?? 'N/A' }} {{ $request->employee->last_name ?? '' }}
                                    </div>
                                    <div class="emp-id">ID: {{ $request->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ ucfirst($request->request_type) }}</span>
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge badge-yellow">
                                    <i class="bi bi-clock-history" style="font-size:.65rem;"></i> Pending
                                </span>
                            @elseif($request->status === 'approved')
                                <span class="badge badge-green">
                                    <i class="bi bi-check-circle" style="font-size:.65rem;"></i> Approved
                                </span>
                            @else
                                <span class="badge badge-red">
                                    <i class="bi bi-x-circle" style="font-size:.65rem;"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td style="color:var(--c-muted); font-size:.8rem;">
                            {{ $request->requested_date?->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td style="color:var(--c-muted); font-size:.8rem;">
                            <span style="display:block; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ Str::limit($request->details, 30) }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; gap:.4rem; justify-content:center;">
                                <a href="{{ route('hr4.ess_requests.show', $request->id) }}" class="action-link view">
                                    <i class="bi bi-eye" style="font-size:.75rem;"></i> View
                                </a>
                                @if($request->status === 'pending')
                                    <button onclick="approveRequest({{ $request->id }})" class="action-link approve">
                                        <i class="bi bi-check" style="font-size:.75rem;"></i> Approve
                                    </button>
                                    <button onclick="rejectRequest({{ $request->id }})" class="action-link reject">
                                        <i class="bi bi-x" style="font-size:.75rem;"></i> Reject
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h4>No ESS requests found</h4>
                                <p>No payroll requests match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @if(isset($requests) && $requests->hasPages())
        <div style="margin-top:1.5rem;">
            {{ $requests->links() }}
        </div>
    @endif

</div>

{{-- ── Hidden Forms ── --}}
<form id="approveForm" method="POST" style="display:none;">@csrf</form>
<form id="rejectForm"  method="POST" style="display:none;">@csrf</form>
<form id="syncForm"    method="POST" action="{{ route('hr4.ess_requests.sync') }}" style="display:none;">@csrf</form>

<script>
    function approveRequest(id) {
        if (confirm('Are you sure you want to approve this request?')) {
            const form = document.getElementById('approveForm');
            form.action = `/admin/hr4/ess-requests/${id}/approve`;
            form.submit();
        }
    }

    function rejectRequest(id) {
        const reason = prompt('Enter reason for rejection:');
        if (reason !== null) {
            const form = document.getElementById('rejectForm');
            form.action = `/admin/hr4/ess-requests/${id}/reject`;
            const reasonInput = document.createElement('input');
            reasonInput.type  = 'hidden';
            reasonInput.name  = 'reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);
            form.submit();
        }
    }

    function syncRequests() {
        if (confirm('This will sync all pending ESS requests from HR2. Continue?')) {
            document.getElementById('syncForm').submit();
        }
    }
</script>

@endsection