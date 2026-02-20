@extends('layouts.hr2.admin.app')

@section('title', 'Learning Management')

@section('content')
<h2>Learning Management</h2>

<form method="POST" action="{{ route('admin.learning.store') }}">
    @csrf
    <h3>Add Learning Module</h3>
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="number" name="competency_id" placeholder="Competency ID">
    <select name="learning_type">
        <option value="Online">Online</option>
        <option value="Workshop">Workshop</option>
        <option value="Seminar">Seminar</option>
        <option value="Coaching">Coaching</option>
    </select>
    <input type="text" name="duration" placeholder="Duration (e.g., 2 hours)">
    <button type="submit">Add</button>
</form>

@if(session('success'))
<p style="color:green;">{{ session('success') }}</p>
@endif

<table>
    <thead>
        <tr>
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
            <td>{{ $c->title }}</td>
            <td>{{ $c->learning_type }}</td>
            <td>{{ $c->duration }}</td>
            <td>{{ $c->enrolls_count }}</td>
            <td>
                <form method="POST" action="{{ route('admin.learning.destroy', $c->id) }}" onsubmit="return confirm('Archive this module?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">Archive</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;">No modules available.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
