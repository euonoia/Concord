@extends('admin.hr4.layouts.app')

@section('title','Core Human Capital')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:          #eef3f7;
        --c-surface:     #ffffff;
        --c-border:      #d4e3ee;
        --c-teal:        #0a7c6e;
        --c-teal-light:  #e4f4f1;
        --c-teal-mid:    #b8e0da;
        --c-blue:        #1a5f8a;
        --c-blue-light:  #e8f2f9;
        --c-amber:       #b45309;
        --c-amber-light: #fef3c7;
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
        --shadow-md:     0 4px 20px rgba(10,50,80,.10);
    }

    .chc * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .chc {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .chc-header {
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

    .chc-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        line-height: 1.1;
    }

    .chc-header h1 em { color: var(--c-teal); font-style: italic; }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--c-teal);
        color: #fff;
        font-size: .85rem;
        font-weight: 600;
        padding: .55rem 1.2rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
        box-shadow: 0 2px 8px rgba(10,124,110,.25);
    }

    .btn-primary:hover {
        background: #0b9483;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(10,124,110,.35);
        color: #fff;
        text-decoration: none;
    }

    /* ── Alert ── */
    .chc-alert {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .9rem 1.2rem;
        background: var(--c-green-light);
        border: 1px solid var(--c-teal-mid);
        border-left: 4px solid var(--c-teal);
        border-radius: 10px;
        color: var(--c-teal);
        font-size: .88rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        animation: fadeUp .4s ease both;
    }

    /* ── Tabs ── */
    .chc-tabs {
        display: flex;
        gap: .4rem;
        flex-wrap: wrap;
        margin-bottom: 1.75rem;
        animation: fadeUp .45s .1s ease both;
    }

    .tab-link {
        padding: .5rem 1.1rem;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: var(--c-surface);
        color: var(--c-muted);
        font-size: .83rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, color .2s ease, border-color .2s ease, transform .2s cubic-bezier(.22,.68,0,1.2);
    }

    .tab-link:hover {
        background: var(--c-teal-light);
        border-color: var(--c-teal-mid);
        color: var(--c-teal);
        transform: translateY(-2px);
        text-decoration: none;
    }

    .tab-link.active {
        background: var(--c-teal);
        border-color: var(--c-teal);
        color: #fff;
        font-weight: 600;
    }

    /* ── Tab Panel ── */
    .tab-section { display: none; animation: fadeUp .35s ease both; }
    .tab-section.active { display: block; }

    /* ── Card wrapper ── */
    .chc-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
    }

    .chc-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--c-line);
    }

    .chc-card-header h3 {
        font-family: 'Instrument Serif', serif;
        font-size: 1.2rem;
        color: var(--c-text);
        margin: 0;
    }

    /* ── Filters ── */
    .filter-row {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .filter-row select,
    .filter-row input {
        padding: .5rem .9rem;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        font-size: .83rem;
        color: var(--c-text);
        background: var(--c-bg);
        outline: none;
        transition: border-color .2s ease;
        font-family: 'DM Sans', sans-serif;
    }

    .filter-row select:focus,
    .filter-row input:focus {
        border-color: var(--c-teal);
    }

    .filter-row input { width: 240px; }

    /* ── Table ── */
    .chc-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem;
    }

    .chc-table thead th {
        background: var(--c-bg);
        color: var(--c-muted);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding: .7rem 1rem;
        text-align: left;
        border-bottom: 1.5px solid var(--c-line);
        white-space: nowrap;
    }

    .chc-table tbody tr {
        border-bottom: 1px solid var(--c-line);
        transition: background .18s ease;
    }

    .chc-table tbody tr:hover { background: var(--c-teal-light); }
    .chc-table tbody tr:last-child { border-bottom: none; }

    .chc-table tbody td {
        padding: .75rem 1rem;
        color: var(--c-text);
        vertical-align: middle;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .7rem;
        border-radius: 99px;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .02em;
    }

    .badge-green  { background: var(--c-green-light);  color: var(--c-green); }
    .badge-red    { background: var(--c-red-light);    color: var(--c-red); }
    .badge-amber  { background: var(--c-amber-light);  color: var(--c-amber); }
    .badge-teal   { background: var(--c-teal-light);   color: var(--c-teal); }
    .badge-blue   { background: var(--c-blue-light);   color: var(--c-blue); }

    /* ── Needed Positions summary cards ── */
    .summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: .75rem 1.2rem;
        min-width: 120px;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: transform .2s ease;
    }

    .summary-card:hover { transform: translateY(-3px); }

    .summary-card strong {
        display: block;
        font-family: 'Instrument Serif', serif;
        font-size: 1.6rem;
        color: var(--c-text);
        line-height: 1;
        margin-bottom: .2rem;
    }

    .summary-card.danger strong { color: var(--c-red); }
    .summary-card small { font-size: .75rem; color: var(--c-muted); }

    /* ── Table row states for needed positions ── */
    .row-needed { background: #fffbeb !important; }
    .row-filled { background: #f0fdf4 !important; }

    /* ── Action links ── */
    .action-link {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        padding: .3rem .65rem;
        border-radius: 6px;
        transition: background .2s ease, color .2s ease;
    }

    .action-link.view   { color: var(--c-blue);  background: var(--c-blue-light); }
    .action-link.edit   { color: var(--c-teal);  background: var(--c-teal-light); }
    .action-link.danger { color: var(--c-red);   background: var(--c-red-light); border: none; cursor: pointer; }

    .action-link:hover { filter: brightness(.92); text-decoration: none; }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--c-muted);
    }

    .empty-state i { font-size: 3rem; display: block; margin-bottom: .75rem; opacity: .3; }
    .empty-state h4 { font-size: 1rem; font-weight: 600; color: var(--c-text); margin: 0 0 .3rem; }
    .empty-state p  { font-size: .83rem; margin: 0 0 1rem; }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="chc">

    {{-- Header --}}
    <div class="chc-header">
        <h1>Core <em>Human Capital</em></h1>
        <form method="POST" action="{{ route('hr4.core.process_hired') }}">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="bi bi-arrow-repeat"></i> Process New Hires
            </button>
        </form>
    </div>

    {{-- Success alert --}}
    @if(session('success'))
    <div class="chc-alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Tabs --}}
    <div class="chc-tabs">
        <a class="tab-link" onclick="showTab('employees')"       href="#employees">Employees</a>
        <a class="tab-link" onclick="showTab('departments')"     href="#departments">Departments</a>
        <a class="tab-link" onclick="showTab('positions')"       href="#positions">Positions</a>
        <a class="tab-link" onclick="showTab('neededpositions')" href="#neededpositions">Needed Positions</a>
        <a class="tab-link" onclick="showTab('userlogs')"        href="#userlogs">User Logs</a>
        <a class="tab-link" onclick="showTab('availablejobs')"   href="#availablejobs">
            Available Jobs
            <span class="badge badge-teal" style="margin-left:.3rem;">{{ $availableJobsCount }}</span>
        </a>
    </div>

    {{-- ── EMPLOYEES ── --}}
    <div id="employees" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>Employees</h3>
                <div class="filter-row" style="margin:0">
                    <select id="departmentFilter">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="employeeSearch" placeholder="Search by name or ID…">
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="chc-table" id="employeeTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}"
                            data-empid="{{ $emp->employee_id }}"
                            data-department="{{ $emp->department_id }}">
                            <td>{{ $emp->id }}</td>
                            <td><strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong></td>
                            <td><span class="badge badge-blue">{{ $emp->employee_id }}</span></td>
                            <td>{{ $emp->department->name ?? 'N/A' }}</td>
                            <td>{{ $emp->position->position_title ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $emp->is_on_duty ? 'badge-green' : 'badge-red' }}">
                                    <i class="bi bi-circle-fill" style="font-size:.45rem"></i>
                                    {{ $emp->is_on_duty ? 'On Duty' : 'Off Duty' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── DEPARTMENTS ── --}}
    <div id="departments" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>Departments</h3>
            </div>
            <div style="overflow-x:auto">
                <table class="chc-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Department Code</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $d)
                        <tr>
                            <td>{{ $d->id }}</td>
                            <td><span class="badge badge-teal">{{ $d->department_id }}</span></td>
                            <td><strong>{{ $d->name }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── POSITIONS ── --}}
    <div id="positions" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>Positions</h3>
            </div>
            <div style="overflow-x:auto">
                <table class="chc-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->department->name ?? 'N/A' }}</td>
                            <td><strong>{{ $p->position_title }}</strong></td>
                            <td><span class="badge badge-blue">{{ $p->rank_level }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── NEEDED POSITIONS ── --}}
    <div id="neededpositions" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>Needed Positions Recommendation</h3>
                <div class="filter-row" style="margin:0">
                    <select id="neededDeptFilter">
                        <option value="">All Departments</option>
                        @foreach(array_unique(array_map(fn($np) => $np['department'], $needed_positions)) as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @php
                $totalNeeded = array_sum(array_column($needed_positions, 'needed'));
                $departmentsWithNeeds = array_filter($needed_positions, fn($np) => $np['needed'] > 0);
                $deptSummary = [];
                foreach ($departmentsWithNeeds as $np) {
                    $dept = $np['department'];
                    if (!isset($deptSummary[$dept])) $deptSummary[$dept] = 0;
                    $deptSummary[$dept] += $np['needed'];
                }
            @endphp

            <div class="summary-grid">
                <div class="summary-card danger">
                    <strong>{{ $totalNeeded }}</strong>
                    <small>Total Needed</small>
                </div>
                @foreach($deptSummary as $dept => $count)
                <div class="summary-card">
                    <strong>{{ $count }}</strong>
                    <small>{{ $dept }}</small>
                </div>
                @endforeach
            </div>

            <div style="overflow-x:auto">
                <table class="chc-table" id="neededPositionsTable">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Position</th>
                            <th style="text-align:center">Required</th>
                            <th style="text-align:center">Current</th>
                            <th style="text-align:center">Needed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($needed_positions as $np)
                        <tr data-department="{{ $np['department'] }}"
                            class="{{ $np['needed'] > 0 ? 'row-needed' : 'row-filled' }}">
                            <td>{{ $np['department'] }}</td>
                            <td><strong>{{ $np['position'] }}</strong></td>
                            <td style="text-align:center">{{ $np['required'] }}</td>
                            <td style="text-align:center">{{ $np['current'] }}</td>
                            <td style="text-align:center">
                                @if($np['needed'] > 0)
                                    <span class="badge badge-amber">{{ $np['needed'] }}</span>
                                @else
                                    <span class="badge badge-green">Filled</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if(count($needed_positions) === 0)
                        <tr><td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-check2-circle"></i>
                                <h4>All positions are sufficiently filled</h4>
                            </div>
                        </td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── USER LOGS ── --}}
    <div id="userlogs" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>User Logs</h3>
            </div>
            <div style="overflow-x:auto">
                <table class="chc-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Role Slug</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><strong>{{ $user->username }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge badge-blue">{{ $user->user_type }}</span></td>
                            <td><span class="badge badge-teal">{{ $user->role_slug }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── AVAILABLE JOBS ── --}}
    <div id="availablejobs" class="tab-section">
        <div class="chc-card">
            <div class="chc-card-header">
                <h3>Available Jobs</h3>
                <a href="{{ route('hr4.job_postings.create') }}" class="btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Job
                </a>
            </div>

            @if($jobPostings->count() > 0)
            <div style="overflow-x:auto">
                <table class="chc-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th style="text-align:center">Slots</th>
                            <th>Status</th>
                            <th>Posted By</th>
                            <th>Posted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobPostings as $posting)
                        <tr>
                            <td><strong>{{ $posting->title }}</strong></td>
                            <td>{{ $posting->department_name ?? $posting->department }}</td>
                            <td style="text-align:center">
                                <span class="badge badge-blue">{{ $posting->positions_available }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $posting->status == 'open' ? 'badge-green' : 'badge-red' }}">
                                    <i class="bi bi-circle-fill" style="font-size:.45rem"></i>
                                    {{ ucfirst($posting->status) }}
                                </span>
                            </td>
                            <td>{{ $posting->poster->username ?? 'Unknown' }}</td>
                            <td>{{ $posting->posted_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                                    <a href="{{ route('hr4.job_postings.show', $posting) }}" class="action-link view">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('hr4.job_postings.edit', $posting) }}" class="action-link edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('hr4.job_postings.destroy', $posting) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Archive this job posting?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-link danger">
                                            <i class="bi bi-archive"></i> Archive
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <i class="bi bi-briefcase"></i>
                <h4>No Available Jobs</h4>
                <p>Get started by adding your first available job.</p>
                <a href="{{ route('hr4.job_postings.create') }}" class="btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Available Job
                </a>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
    // ── Tab switching ──
    function showTab(tab) {
        document.querySelectorAll('.tab-section').forEach(e => e.classList.remove('active'));
        document.querySelectorAll('.tab-link').forEach(e => e.classList.remove('active'));
        const section = document.getElementById(tab);
        if (section) section.classList.add('active');
        document.querySelectorAll(`.tab-link[href="#${tab}"]`).forEach(e => e.classList.add('active'));
    }

    function handleTabFromHash() {
        const hash = window.location.hash.substring(1);
        const valid = ['employees','departments','positions','neededpositions','userlogs','availablejobs'];
        showTab(valid.includes(hash) ? hash : 'employees');
    }

    document.addEventListener('DOMContentLoaded', handleTabFromHash);
    window.addEventListener('hashchange', () => setTimeout(handleTabFromHash, 10));

    // ── Employee filter ──
    const deptFilter   = document.getElementById('departmentFilter');
    const empSearch    = document.getElementById('employeeSearch');

    function filterEmployees() {
        const dept   = deptFilter?.value ?? '';
        const search = empSearch?.value.toLowerCase() ?? '';
        document.querySelectorAll('#employeeTable tbody tr').forEach(row => {
            const matchDept   = !dept   || row.dataset.department === dept;
            const matchSearch = !search || row.dataset.name.includes(search) || row.dataset.empid?.toLowerCase().includes(search);
            row.style.display = (matchDept && matchSearch) ? '' : 'none';
        });
    }

    deptFilter?.addEventListener('change', filterEmployees);
    empSearch?.addEventListener('keyup', filterEmployees);

    // ── Needed positions filter ──
    const neededFilter = document.getElementById('neededDeptFilter');
    neededFilter?.addEventListener('change', function () {
        const dept = this.value;
        document.querySelectorAll('#neededPositionsTable tbody tr').forEach(row => {
            row.style.display = (!dept || row.dataset.department === dept) ? '' : 'none';
        });
    });
</script>

@endsection