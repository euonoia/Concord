
@extends('admin.layouts.app')

@section('title', 'Learning Management')

@section('content')
<h2>Learning Management</h2>

<form method="POST" action="{{ route('learning.store') }}">
    @csrf
    <h3>Add Learning Module</h3>
    
    <div>
        <input type="text" name="title" placeholder="Title" required value="{{ old('title') }}">
        @error('title') <span style="color:red">{{ $message }}</span> @enderror
    </div>

    <textarea name="description" placeholder="Description">{{ old('description') }}</textarea>
    
    <input type="number" name="competency_id" placeholder="Competency ID">
    
    <select name="learning_type">
        <option value="Online">Online</option>
        <option value="Workshop">Workshop</option>
        <option value="Seminar">Seminar</option>
        <option value="Coaching">Coaching</option>
    </select>
    
    <input type="text" name="duration" placeholder="Duration (e.g., 2 hours)">
    
    <button type="submit">Add Module</button>
</form>

<hr>

{{-- Alerts --}}
@if(session('success'))
    <div style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div style="padding: 10px; background: #d1ecf1; color: #0c5460; margin-bottom: 10px;">
        {{ session('info') }}
    </div>
@endif

{{-- Data Table --}}
<table border="1" style="width:100%; border-collapse: collapse; text-align: left;">
    <thead>
        <tr style="background: #f4f4f4;">
            <th>ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Duration</th>
            <th>Enrollments</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($courses as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td><strong>{{ $c->title }}</strong></td>
            <td>{{ $c->learning_type }}</td>
            <td>{{ $c->duration }}</td>
            <td>{{ $c->enrolls_count ?? 0 }}</td>
            <td>
                {{-- Updated route to the Resource name: learning.destroy --}}
                <form method="POST" action="{{ route('learning.destroy', $c->id) }}" onsubmit="return confirm('Archive this module?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red; background:none; border:none; cursor:pointer; font-weight: bold;">
                        Archive
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; padding: 20px;">No modules available.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection