@extends('layouts.dashboard.app')

@section('content')
<div class="container" style="padding: 20px;">

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <h2 style="margin-bottom: 20px;">My Learning Materials</h2>

    @forelse($materials as $material)
        <div style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
            <h4 style="margin-bottom: 5px;">{{ $material->title }}</h4>
            <p>Module: {{ $material->module_code }}</p>

            @if($material->type === 'url')
                <a href="{{ $material->url }}" target="_blank" style="color: #1B3C53; font-weight: bold;">View Lesson</a>
            @elseif($material->type === 'file')
                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" style="color: #1B3C53; font-weight: bold;">Download File</a>
            @endif
        </div>
    @empty
        <p style="color: #999;">There are no Learning Materials available yet.</p>
    @endforelse

</div>
@endsection