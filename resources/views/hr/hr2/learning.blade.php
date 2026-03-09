@extends('layouts.dashboard.app')

@section('title', 'Learning Management')

@section('content')
<div class="container">
    <div class="header-box mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2>Available Courses</h2>
            <p>Enroll in training programs to enhance your competencies.</p>
        </div>
        {{-- Optional navigation button --}}
        <div>
            <a href="{{ route('user.learning.materials.index') }}" class="btn btn-success btn-sm">
                My Courses / Materials
            </a>
        </div>
    </div>

    {{-- Success & Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Course Grid --}}
    <div class="course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($modules as $module)
            <div class="card p-3" style="border: 1px solid #ddd; border-radius: 8px; background: #fff; box-shadow: 2px 2px 5px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">

                {{-- Module Info --}}
                <div>
                    <span class="text-primary font-weight-bold" style="font-size: 0.8rem;">{{ $module->module_type }}</span>
                    <h3 style="margin: 10px 0; color: #333;">{{ $module->module_name }}</h3>
                    <p style="color: #555; font-size: 0.9rem;">{{ $module->description ?? 'No description provided.' }}</p>
                    <p style="font-size: 0.85rem; color: #666; margin:0;">
                        <strong>Department:</strong> {{ $module->dept_code }} |
                        <strong>Specialization:</strong> {{ $module->specialization_name }}
                    </p>
                    <small style="color: #888;">Duration: {{ $module->duration_hours }} hrs</small>

                    @if($module->is_mandatory)
                        <p style="color: #e53e3e; font-size: 0.85rem; margin:2px 0;">Mandatory</p>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    @php
                        $enrolled = in_array($module->module_code, $enrolledModuleCodes);
                    @endphp

                    @if($enrolled)
                        {{-- View Materials button --}}
                        <a href="{{ route('user.learning.materials.show', $module->module_code) }}" class="btn btn-success btn-sm w-100">
                            <i class="fa fa-book"></i> View Materials
                        </a>
                    @else
                        {{-- Enroll button --}}
                        <form action="{{ route('user.learning.enroll', $module->module_code) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                Enroll Now
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align:center;">No learning modules available at the moment.</p>
        @endforelse
    </div>
</div>
@endsection