@extends('admin.hr4.layouts.app')

@section('title', 'Add Available Job - HR4 Admin')

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
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-red-mid:     #f4b8c8;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
    }

    .ajc * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .ajc {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .ajc-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .ajc-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0 0 .25rem;
        line-height: 1.1;
    }

    .ajc-header h1 em { color: var(--c-teal); font-style: italic; }
    .ajc-header p { font-size: .88rem; color: var(--c-muted); margin: 0; }

    /* ── Card ── */
    .ajc-card {
        max-width: 720px;
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

    .form-control:disabled {
        background: var(--c-bg);
        color: var(--c-muted);
        cursor: not-allowed;
    }

    textarea.form-control { resize: vertical; min-height: 100px; }

    .form-error {
        font-size: .75rem;
        color: var(--c-red);
        margin-top: .35rem;
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    /* ── Two-col grid ── */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

    /* ── Error alert ── */
    .error-alert {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .9rem 1.2rem;
        background: var(--c-red-light);
        border: 1px solid var(--c-red-mid);
        border-left: 4px solid var(--c-red);
        border-radius: 10px;
        color: var(--c-red);
        font-size: .83rem;
        margin-bottom: 1.5rem;
    }

    .error-alert ul { margin: 0; padding-left: 1rem; }
    .error-alert li { margin-bottom: .2rem; }

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

<div class="ajc">

    <div class="ajc-header">
        <h1>Add <em>Available Job</em></h1>
        <p>Fill in the details to post a new job for HR1 to fetch.</p>
    </div>

    <div class="ajc-card">

        {{-- Errors --}}
        @if($errors->any())
        <div class="error-alert">
            <i class="bi bi-exclamation-circle-fill" style="margin-top:.1rem; flex-shrink:0"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('hr4.job_postings.store') }}">
            @csrf

            {{-- Department & Position --}}
            <div class="card-section-title">
                <i class="bi bi-building"></i> Department & Position
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="dept_code">Department Code <span class="req">*</span></label>
                    <select id="dept_code" class="form-control" disabled>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->dept_code }}" {{ old('dept_code') == $dept->dept_code ? 'selected' : '' }}>
                                {{ $dept->dept_code }} — {{ $dept->specialization_name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="dept_code" id="dept_code_hidden" value="{{ old('dept_code') }}">
                    @error('dept_code')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="position_id">Position <span class="req">*</span></label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        <option value="">Select Position</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" data-salary="{{ $pos->base_salary }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->position_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="specialization_name">Specialization <span class="req">*</span></label>
                <select id="specialization_name" class="form-control" disabled>
                    <option value="">Select Position First</option>
                    @foreach($specializations as $spec)
                        <option value="{{ $spec->specialization_name }}" {{ old('specialization_name') == $spec->specialization_name ? 'selected' : '' }}>
                            {{ $spec->specialization_name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="specialization_name" id="specialization_name_hidden" value="{{ old('specialization_name') }}">
                @error('specialization_name')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            {{-- Job Details --}}
            <div class="card-section-title">
                <i class="bi bi-card-text"></i> Job Details
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description <span class="req">*</span></label>
                <textarea name="description" id="description" class="form-control" placeholder="Describe the job responsibilities…" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="competency_id">Requirements (Competency)</label>
                <select name="competency_id" id="competency_code" class="form-control" disabled>
                    <option value="">Select Position First</option>
                </select>
                @error('competency_id')
                    <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            {{-- Compensation & Slots --}}
            <div class="card-section-title">
                <i class="bi bi-cash-stack"></i> Compensation & Slots
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="salary_range">Salary Range</label>
                    <input type="text" name="salary_range" id="salary_range" class="form-control"
                           value="{{ old('salary_range') }}" placeholder="e.g., ₱30,000 – ₱50,000">
                    @error('salary_range')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="positions_available">Positions Available <span class="req">*</span></label>
                    <input type="number" name="positions_available" id="positions_available"
                           class="form-control" value="{{ old('positions_available', 1) }}" min="1" required>
                    @error('positions_available')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('hr4.job_postings.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-teal">
                    <i class="bi bi-plus-circle"></i> Add Available Job
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('position_id').addEventListener('change', function () {
    const positionId  = this.value;
    const deptSelect  = document.getElementById('dept_code');
    const deptHidden  = document.getElementById('dept_code_hidden');
    const specSelect  = document.getElementById('specialization_name');
    const specHidden  = document.getElementById('specialization_name_hidden');
    const competencySelect = document.getElementById('competency_code');

    if (!positionId) {
        deptSelect.value = '';
        deptHidden.value = '';
        specSelect.innerHTML = '<option value="">Select Position First</option>';
        specHidden.value = '';
        competencySelect.innerHTML = '<option value="">Select Position First</option>';
        competencySelect.disabled = true;
        return;
    }

    // Fetch position details
    fetch(`/admin/hr4/job-postings/${positionId}/details`)
        .then(res => res.json())
        .then(data => {
            if (data) {
                // Set dept_code
                deptSelect.value = data.department_id;
                deptHidden.value = data.department_id;
                // Set specialization
                specSelect.innerHTML = `<option value="${data.specialization_name}" selected>${data.specialization_name}</option>`;
                specHidden.value = data.specialization_name;

                // Fetch competencies
                competencySelect.innerHTML = '<option value="">Loading…</option>';
                competencySelect.disabled = false;

                fetch(`/admin/hr4/job-postings/competencies?specialization=${encodeURIComponent(data.specialization_name)}&department_id=${encodeURIComponent(data.department_id)}`)
                    .then(res => res.json())
                    .then(compData => {
                        competencySelect.innerHTML = '<option value="">Select Competency</option>';
                        if (compData.length === 0) {
                            competencySelect.innerHTML += '<option value="" disabled>No competencies available</option>';
                        } else {
                            compData.forEach(comp => {
                                const opt = document.createElement('option');
                                opt.value = comp.id;
                                opt.textContent = comp.competency_code + ' — ' + comp.description;
                                competencySelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(() => {
                        competencySelect.innerHTML = '<option value="">Error loading competencies</option>';
                    });
            } else {
                deptSelect.value = '';
                deptHidden.value = '';
                specSelect.innerHTML = '<option value="">Error loading details</option>';
                specHidden.value = '';
                competencySelect.innerHTML = '<option value="">Select Position First</option>';
                competencySelect.disabled = true;
            }
        })
        .catch(() => {
            deptSelect.value = '';
            deptHidden.value = '';
            specSelect.innerHTML = '<option value="">Error loading details</option>';
            specHidden.value = '';
            competencySelect.innerHTML = '<option value="">Select Position First</option>';
            competencySelect.disabled = true;
        });
});
</script>

@endsection