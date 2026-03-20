@extends('admin.hr4.layouts.app')

@section('title', 'View Job Posting - HR4 Admin')

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
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-amber:       #b45309;
        --c-amber-light: #fef3c7;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
    }

    .ajv * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .ajv {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .ajv-header {
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

    .ajv-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        line-height: 1.1;
    }

    .ajv-header h1 em { color: var(--c-teal); font-style: italic; }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .83rem;
        font-weight: 600;
        padding: .5rem 1.1rem;
        border-radius: 9px;
        background: var(--c-surface);
        color: var(--c-muted);
        border: 1.5px solid var(--c-border);
        text-decoration: none;
        transition: background .2s ease, color .2s ease, transform .2s ease;
    }

    .btn-back:hover {
        background: var(--c-bg);
        color: var(--c-text);
        transform: translateY(-2px);
        text-decoration: none;
    }

    /* ── Layout ── */
    .ajv-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
        animation: fadeUp .5s .1s ease both;
    }

    @media (max-width: 768px) { .ajv-layout { grid-template-columns: 1fr; } }

    /* ── Card ── */
    .ajv-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
    }

    .card-section-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: 1.25rem;
        padding-bottom: .6rem;
        border-bottom: 1px solid var(--c-line);
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .card-section-title i { color: var(--c-teal); font-size: .85rem; }

    /* ── Info rows ── */
    .info-row {
        display: flex;
        flex-direction: column;
        gap: .2rem;
        padding: .7rem 0;
        border-bottom: 1px solid var(--c-line);
    }

    .info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .info-row:first-child { padding-top: 0; }

    .info-label {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--c-muted);
    }

    .info-value {
        font-size: .88rem;
        color: var(--c-text);
        font-weight: 500;
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
    .badge-blue  { background: var(--c-blue-light);  color: var(--c-blue); }

    /* ── Text content boxes ── */
    .content-box {
        background: var(--c-bg);
        border: 1px solid var(--c-line);
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        font-size: .85rem;
        color: var(--c-text);
        line-height: 1.7;
        white-space: pre-line;
        margin-bottom: 1.25rem;
    }

    .content-box:last-child { margin-bottom: 0; }

    /* ── Actions card ── */
    .ajv-actions {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 1.25rem 1.75rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
        animation: fadeUp .5s .2s ease both;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .85rem;
        font-weight: 600;
        padding: .6rem 1.3rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .btn:hover { transform: translateY(-2px); text-decoration: none; }

    .btn-teal {
        background: var(--c-teal);
        color: #fff;
        box-shadow: 0 2px 8px rgba(10,124,110,.25);
    }

    .btn-teal:hover {
        background: #0b9483;
        box-shadow: 0 4px 14px rgba(10,124,110,.35);
        color: #fff;
    }

    .btn-danger {
        background: var(--c-red-light);
        color: var(--c-red);
        border: 1.5px solid #f4b8c8;
    }

    .btn-danger:hover { background: #fad0db; color: var(--c-red); }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="ajv">

    {{-- Header --}}
    <div class="ajv-header">
        <h1>Job Posting <em>Details</em></h1>
        <a href="{{ route('hr4.job_postings.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Jobs
        </a>
    </div>

    {{-- Two-column layout --}}
    <div class="ajv-layout">

        {{-- Left: Job Info --}}
        <div class="ajv-card">
            <div class="card-section-title">
                <i class="bi bi-briefcase"></i> Job Information
            </div>

            <div class="info-row">
                <span class="info-label">Job Title</span>
                <span class="info-value" style="font-size:1rem; font-family:'Instrument Serif',serif;">{{ $jobPosting->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Department</span>
                <span class="info-value">{{ $jobPosting->department_name ?? $jobPosting->department }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="badge {{ $jobPosting->status == 'open' ? 'badge-green' : 'badge-red' }}">
                    <i class="bi bi-circle-fill" style="font-size:.4rem"></i>
                    {{ ucfirst($jobPosting->status) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Salary Range</span>
                <span class="info-value">{{ $jobPosting->salary_range ?? 'Not specified' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Positions Available</span>
                <span class="badge badge-blue">{{ $jobPosting->positions_available }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Posted By</span>
                <span class="info-value">{{ $jobPosting->poster->username ?? 'Unknown' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Posted At</span>
                <span class="info-value">{{ $jobPosting->posted_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        {{-- Right: Description & Requirements --}}
        <div class="ajv-card">
            <div class="card-section-title">
                <i class="bi bi-card-text"></i> Job Details
            </div>

            <div style="font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--c-muted); margin-bottom:.5rem;">Description</div>
            <div class="content-box">{{ $jobPosting->description }}</div>

            <div style="font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--c-muted); margin-bottom:.5rem; margin-top:1rem;">Requirements</div>
            <div class="content-box">{{ $jobPosting->requirements }}</div>
        </div>

    </div>

    {{-- Actions --}}
    <div class="ajv-actions">
        <a href="{{ route('hr4.job_postings.edit', $jobPosting) }}" class="btn btn-teal">
            <i class="bi bi-pencil"></i> Edit Job
        </a>
        <form method="POST" action="{{ route('hr4.job_postings.destroy', $jobPosting) }}"
              style="display:inline"
              onsubmit="return confirm('Archive this job posting?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-archive"></i> Archive Job
            </button>
        </form>
    </div>

</div>

@endsection