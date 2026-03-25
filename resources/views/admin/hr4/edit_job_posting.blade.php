@extends('admin.hr4.layouts.app')

@section('title', 'Edit Job Posting - HR4 Admin')

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
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-green-mid:   #b2ddc8;
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-red-mid:     #f4b8c8;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
    }

    .aje * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .aje {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .aje-header {
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

    .aje-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        line-height: 1.1;
    }

    .aje-header h1 em { color: var(--c-teal); font-style: italic; }

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

    /* ── Alert ── */
    .aje-alert {
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
    .aje-card {
        max-width: 820px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        animation: fadeUp .5s .1s ease both;
    }

    .card-section-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin: 1.75rem 0 1rem;
        padding-bottom: .6rem;
        border-bottom: 1px solid var(--c-line);
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .card-section-title:first-child { margin-top: 0; }
    .card-section-title i { color: var(--c-teal); font-size: .85rem; }

    /* ── Form ── */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

    .form-group { margin-bottom: 1.2rem; }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
        display: block;
        font-size: .8rem;
        font-weight: 600;
        color: var(--c-text);
        margin-bottom: .4rem;
    }

    .form-label .req { color: var(--c-red); margin-left: .2rem; }

    .form-control {
        width: 100%;
        padding: .6rem .9rem;
        border: 1.5px solid var(--c-border);
        border-radius: 9px;
        font-size: .85rem;
        color: var(--c-text);
        background: var(--c-surface);
        outline: none;
        font-family: 'DM Sans', sans-serif;
        transition: border-color .2s ease, box-shadow .2s ease;
        appearance: auto;
    }

    .form-control:focus {
        border-color: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(10,124,110,.1);
    }

    textarea.form-control { resize: vertical; min-height: 110px; }

    .form-error {
        font-size: .75rem;
        color: var(--c-red);
        margin-top: .35rem;
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    /* ── Status badge indicator ── */
    .status-row { display: flex; align-items: center; gap: .75rem; }

    .status-dot {
        width: 9px; height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
        transition: background .2s ease;
    }

    /* ── Buttons ── */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--c-line);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .85rem;
        font-weight: 600;
        padding: .6rem 1.4rem;
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

    .btn-cancel {
        background: var(--c-bg);
        color: var(--c-muted);
        border: 1.5px solid var(--c-border);
    }

    .btn-cancel:hover { background: #dce6ed; color: var(--c-text); }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="aje">

    {{-- Header --}}
    <div class="aje-header">
        <h1>Edit <em>Job Posting</em></h1>
        <a href="{{ route('hr4.job_postings.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Jobs
        </a>
    </div>

    {{-- Success alert --}}
    @if(session('success'))
    <div class="aje-alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="aje-card">
        <form method="POST" action="{{ route('hr4.job_postings.update', $jobPosting) }}">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="card-section-title">
                <i class="bi bi-briefcase"></i> Basic Information
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="title">Job Title <span class="req">*</span></label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $jobPosting->title) }}"
                           class="form-control" required>
                    @error('title')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="department">Department <span class="req">*</span></label>
                    <select name="department" id="department" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}"
                                {{ old('department', $jobPosting->department) == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="salary_range">Salary Range</label>
                    <input type="text" name="salary_range" id="salary_range"
                           value="{{ old('salary_range', $jobPosting->salary_range) }}"
                           class="form-control" placeholder="e.g., ₱30,000 – ₱50,000">
                    @error('salary_range')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="positions_available">Positions Available <span class="req">*</span></label>
                    <input type="number" name="positions_available" id="positions_available"
                           value="{{ old('positions_available', $jobPosting->positions_available) }}"
                           class="form-control" min="1" required>
                    @error('positions_available')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span class="req">*</span></label>
                <div class="status-row">
                    <div class="status-dot" id="statusDot"></div>
                    <select name="status" id="status" class="form-control" required style="flex:1">
                        <option value="open"   {{ old('status', $jobPosting->status) == 'open'   ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ old('status', $jobPosting->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                @error('status')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            {{-- Description & Requirements --}}
            <div class="card-section-title">
                <i class="bi bi-card-text"></i> Job Details
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Job Description <span class="req">*</span></label>
                <textarea name="description" id="description" class="form-control" required>{{ old('description', $jobPosting->description) }}</textarea>
                @error('description')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="requirements">Requirements <span class="req">*</span></label>
                <textarea name="requirements" id="requirements" class="form-control" required>{{ old('requirements', $jobPosting->requirements) }}</textarea>
                @error('requirements')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('hr4.job_postings.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-teal">
                    <i class="bi bi-check-circle"></i> Update Job Posting
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // Status dot color indicator
    const statusSelect = document.getElementById('status');
    const statusDot    = document.getElementById('statusDot');

    function updateDot() {
        statusDot.style.background = statusSelect.value === 'open' ? '#1a7a52' : '#be123c';
    }

    statusSelect.addEventListener('change', updateDot);
    updateDot(); // init on load
</script>

@endsection