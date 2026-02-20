@extends('admin.layouts.app')

@section('title', 'Training Management - HR2 Admin')

@section('content')
<div class="container">
    <h2>Training Management</h2>

    {{-- Success/Error Feedback --}}
    @if(session('success'))
        <div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Updated route to the Resource name: training.store --}}
    <form method="POST" action="{{ route('training.store') }}" style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
        @csrf
        <h3>Add New Training</h3>
        
        <div style="margin-bottom: 10px;">
            <input type="text" name="title" placeholder="Training Title" required style="width: 100%;">
        </div>

        <div style="margin-bottom: 10px;">
            <textarea name="description" placeholder="Training Description" style="width: 100%;"></textarea>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 10px;">
            <div>
                <label>Start Date & Time:</label><br>
                <input type="datetime-local" name="start_datetime" required>
            </div>
            <div>
                <label>End Date & Time:</label><br>
                <input type="datetime-local" name="end_datetime">
            </div>
        </div>

        <div style="margin-bottom: 10px;">
            <input type="text" name="location" placeholder="Location">
            <input type="text" name="trainer" placeholder="Trainer Name">
            <input type="number" name="capacity" placeholder="Capacity" min="1">
        </div>

        <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">
            Add Training
        </button>
    </form>

    <hr style="margin: 30px 0;">

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #eee;">
            <tr>
                <th>Title</th>
                <th>Trainer</th>
                <th>Start</th>
                <th>End</th>
                <th>Location</th>
                <th>Capacity</th>
                <th>Attendees</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $s)
                <tr>
                    <td><strong>{{ $s->title }}</strong></td>
                    <td>{{ $s->trainer ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->start_datetime)->format('M d, Y h:i A') }}</td>
                    <td>{{ $s->end_datetime ? \Carbon\Carbon::parse($s->end_datetime)->format('M d, Y h:i A') : '---' }}</td>
                    <td>{{ $s->location }}</td>
                    <td>{{ $s->capacity }}</td>
                    <td>{{ $s->enrolls_count ?? 0 }}</td>
                    <td>
                        {{-- Updated route to the Resource name: training.destroy --}}
                        <form method="POST" action="{{ route('training.destroy', $s->id) }}" onsubmit="return confirm('Archive this training session?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">
                                Archive
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 20px;">No training sessions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection