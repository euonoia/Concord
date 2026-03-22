@extends('admin.hr4.layouts.app')

@section('title', 'Edit Employee - HR4 Admin')

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

    .eec * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .eec {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .eec-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .eec-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0 0 .25rem;
        line-height: 1.1;
    }

    .eec-header h1 em { color: var(--c-teal); font-style: italic; }
    .eec-header p { font-size: .88rem; color: var(--c-muted); margin: 0; }

    /* ── Card ── */
    .eec-card {
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
    }

    .form-control:focus {
        border-color: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(10,124,110,.1);
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

    .btn-danger {
        background: var(--c-red);
        color: #fff;
        box-shadow: 0 2px 8px rgba(190,18,60,.25);
    }

    .btn-danger:hover {
        background: #a11d3a;
        box-shadow: 0 4px 14px rgba(190,18,60,.35);
        color: #fff;
    }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="eec">

    <div class="eec-header">
        <h1>Edit <em>Employee</em></h1>
        <p>Update employee information and status.</p>
    </div>

    <div class="eec-card">

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

        <form method="POST" action="{{ route('hr4.employees.update', $employee) }}">
            @csrf
            @method('PUT')

            {{-- Basic Information --}}
            <div class="card-section-title">
                <i class="bi bi-person"></i> Basic Information
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="employee_id">Employee ID <span class="req">*</span></label>
                    <input type="text" name="employee_id" id="employee_id" class="form-control"
                           value="{{ old('employee_id', $employee->employee_id) }}" required>
                    @error('employee_id')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $employee->phone) }}">
                    @error('phone')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control"
                           value="{{ old('first_name', $employee->first_name) }}" required>
                    @error('first_name')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                           value="{{ old('last_name', $employee->last_name) }}" required>
                    @error('last_name')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Employment Details --}}
            <div class="card-section-title">
                <i class="bi bi-building"></i> Employment Details
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}"
                                    {{ old('department_id', $employee->department_id) == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="position_id">Position</label>
                    <select name="position_id" id="position_id" class="form-control">
                        <option value="">Select Position</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}"
                                    {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->position_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="specialization">Specialization</label>
                    <input type="text" name="specialization" id="specialization" class="form-control"
                           value="{{ old('specialization', $employee->specialization) }}">
                    @error('specialization')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="hire_date">Hire Date</label>
                    <input type="date" name="hire_date" id="hire_date" class="form-control"
                           value="{{ old('hire_date', $employee->hire_date) }}">
                    @error('hire_date')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Status & Duty --}}
            <div class="card-section-title">
                <i class="bi bi-toggle-on"></i> Status & Duty
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status <span class="req">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" {{ old('status', $employee->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $employee->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="resigned" {{ old('status', $employee->status ?? 'active') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="terminated" {{ old('status', $employee->status ?? 'active') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                    @error('status')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_on_duty">On Duty</label>
                    <select name="is_on_duty" id="is_on_duty" class="form-control">
                        <option value="0" {{ old('is_on_duty', $employee->is_on_duty ?? 0) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_on_duty', $employee->is_on_duty ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error('is_on_duty')
                        <p class="form-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <a href="{{ route('hr4.core') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-teal">
                    <i class="bi bi-check-circle"></i> Update Employee
                </button>
            </div>

        </form>

        {{-- Delete Form --}}
        <form method="POST" action="{{ route('hr4.employees.delete', $employee) }}"
              onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.')"
              style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--c-line);">
            @csrf
            @method('DELETE')

            <div class="form-actions" style="justify-content: space-between;">
                <span style="font-size: .8rem; color: var(--c-muted);">
                    <i class="bi bi-exclamation-triangle"></i> Deleting an employee will permanently remove their record.
                </span>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Delete Employee
                </button>
            </div>
        </form>

    </div>
</div>

@endsection