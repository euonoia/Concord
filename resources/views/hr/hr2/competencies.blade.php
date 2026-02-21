@extends('layouts.dashboard.app')

@section('title', 'My Competencies')

@section('content')
<div class="container">
    <div class="header-box" style="margin-bottom: 20px;">
        <h2>Available Competencies</h2>
        <p>Review the skills and qualifications mapped for your department.</p>
    </div>

    <div class="competency-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($competencies as $competency)
            <div class="card" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #fff; box-shadow: 2px 2px 5px rgba(0,0,0,0.05);">
                <span style="font-size: 0.8rem; color: #666; font-weight: bold;">{{ $competency->code }}</span>
                <h3 style="margin: 10px 0; color: #333;">{{ $competency->title }}</h3>
                <p style="color: #555; font-size: 0.9rem; min-height: 50px;">
                    {{ $competency->description ?: 'No description provided.' }}
                </p>
                <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                    <small>Group: <strong>{{ $competency->competency_group }}</strong></small>
                </div>
            </div>
        @empty
            <p>No competencies have been assigned to this module yet.</p>
        @endforelse
    </div>
</div>
@endsection