@extends('admin.hr2.layouts.app')

@section('title', 'Learning Management')

@section('content')
<h2>Learning Management</h2>

<form method="POST" action="{{ route('learning.store') }}">
    @csrf
    <input type="text" name="module_code" placeholder="Module Code" required value="{{ old('module_code') }}">
    <input type="text" name="module_name" placeholder="Module Name" required value="{{ old('module_name') }}">
    <input type="text" name="dept_code" placeholder="Department Code" required value="{{ old('dept_code') }}">
    <input type="text" name="specialization_name" placeholder="Specialization" required value="{{ old('specialization_name') }}">
    
    <select name="module_type">
        <option value="Compliance">Compliance</option>
        <option value="Clinical">Clinical</option>
        <option value="Simulation">Simulation</option>
        <option value="Research">Research</option>
        <option value="Other">Other</option>
    </select>
    
    <input type="number" name="duration_hours" placeholder="Duration Hours" min="1" value="{{ old('duration_hours',1) }}">
    
    <label>
        <input type="checkbox" name="is_mandatory" value="1" checked> Mandatory
    </label>
    
    <button type="submit">Add Module</button>
</form>

@if(session('success'))
    <div style="padding: 10px; background: #d4edda; color: #155724;">{{ session('success') }}</div>
@endif

<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Dept</th>
            <th>Specialization</th>
            <th>Type</th>
            <th>Duration (hrs)</th>
            <th>Enrollments</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($modules as $m)
        <tr>
            <td>{{ $m->id }}</td>
            <td>{{ $m->module_code }}</td>
            <td>{{ $m->module_name }}</td>
            <td>{{ $m->dept_code }}</td>
            <td>{{ $m->specialization_name }}</td>
            <td>{{ $m->module_type }}</td>
            <td>{{ $m->duration_hours }}</td>
            <td>{{ $m->enrolls_count ?? 0 }}</td>
            <td>
                <form method="POST" action="{{ route('learning.destroy',$m->id) }}" onsubmit="return confirm('Archive this module?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="color:red;">Archive</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;">No modules available.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection