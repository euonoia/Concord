@extends('admin.hr4.layouts.app')

@section('title', 'Available Jobs - HR4 Admin')

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
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-green-mid:   #b2ddc8;
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
        --shadow-hover:  0 10px 36px rgba(10,124,110,.13);
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
        margin: 0;
        line-height: 1.1;
    }

    .aj-header h1 em { color: var(--c-teal); font-style: italic; }

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

    /* ── Alert ── */
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

    /* ── Card ── */
    .aj-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeUp .5s .1s ease both;
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

    .badge-green { background: var(--c-green-light); color: var(--c-green); }
    .badge-red   { background: var(--c-red-light);   color: var(--c-red); }

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
        transition: filter .18s ease;
        border: none;
        cursor: pointer;
    }

    .action-link:hover { filter: brightness(.9); text-decoration: none; }
    .action-link.view    { background: var(--c-blue-light);  color: var(--c-blue); }
    .action-link.edit    { background: var(--c-teal-light);  color: var(--c-teal); }
    .action-link.archive { background: var(--c-red-light);   color: var(--c-red); }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: var(--c-muted);
    }

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

    .empty-state p { font-size: .83rem; margin: 0 0 1.25rem; }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="aj">

    {{-- Header --}}
    <div class="aj-header">
        <h1><em>Available</em> Jobs</h1>
        <a href="{{ route('hr4.job_postings.create') }}" class="btn-teal">
            <i class="bi bi-plus-circle"></i> Add New Job
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="aj-alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="aj-card">
        @if($jobPostings->count() > 0)
        <div style="overflow-x:auto">
            <table class="aj-table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Added By</th>
                        <th>Added At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobPostings as $posting)
                    <tr>
                        <td><strong>{{ $posting->title }}</strong></td>
                        <td>{{ $posting->department_name ?? $posting->department }}</td>
                        <td>
                            <span class="badge {{ $posting->status == 'open' ? 'badge-green' : 'badge-red' }}">
                                <i class="bi bi-circle-fill" style="font-size:.4rem"></i>
                                {{ ucfirst($posting->status) }}
                            </span>
                        </td>
                        <td>{{ $posting->poster->username ?? 'Unknown' }}</td>
                        <td>{{ $posting->posted_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display:flex; gap:.4rem; flex-wrap:wrap">
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
                                    <button type="submit" class="action-link archive">
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
            <a href="{{ route('hr4.job_postings.create') }}" class="btn-teal">
                <i class="bi bi-plus-circle"></i> Add New Available Job
            </a>
        </div>
        @endif
    </div>

</div>

@endsection