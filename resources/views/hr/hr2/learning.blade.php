@extends('layouts.dashboard.app')

@section('title', 'Learning Management')

@section('content')
<div class="container">
    <div class="header-box" style="margin-bottom: 20px;">
        <h2>Available Courses</h2>
        <p>Enroll in training programs to enhance your competencies.</p>
    </div>

   <div class="course-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    @forelse($modules as $module)
        <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff; box-shadow: 2px 2px 5px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="font-size: 0.8rem; color: #007bff; font-weight: bold;">{{ $module->learning_type }}</span>
                <h3 style="margin: 10px 0; color: #333;">{{ $module->title }}</h3>
                <p style="color: #555; font-size: 0.9rem;">{{ $module->description }}</p>
                <small style="color: #888;">Duration: {{ $module->duration }}</small>
            </div>

            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                @if($module->enrolls->isNotEmpty())
                    <button class="btn btn-success btn-sm" style="width: 100%;" disabled>
                        <i class="fa fa-check"></i> Enrolled
                    </button>
                @else
                    <form action="{{ route('user.learning.enroll', $module->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                            Enroll Now
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p>No learning modules available at the moment.</p>
    @endforelse
</div>
</div>
@endsection