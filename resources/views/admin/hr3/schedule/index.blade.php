@extends('admin.hr3.layouts.app')

@section('title','Interview Schedule')

@section('content')

<h2>Interview Schedule</h2>

@if(session('success'))
    <div style="color:green; padding: 10px; border: 1px solid green; margin-bottom: 15px;">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('schedule.store') }}">
    @csrf
    <div style="margin-bottom: 15px;">
        <label>Department:</label><br>
        <select id="department" required>
            <option value="">Select Department</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label>Specialization:</label><br>
        <select id="specialization" required>
            <option value="">Select Specialization</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label>Applicant:</label><br>
        <select name="applicant_id" id="applicant" required>
            <option value="">Select Applicant</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label>Date & Time:</label><br>
        <input type="date" name="schedule_date" required>
        <input type="time" name="schedule_time" required>
    </div>

    <div style="margin-bottom: 15px;">
        <input type="text" name="location" placeholder="Location" style="width: 100%; max-width: 300px;">
    </div>

    <div style="margin-bottom: 15px;">
        <textarea name="notes" placeholder="Notes" style="width: 100%; max-width: 300px;"></textarea>
    </div>

    <button type="submit" style="padding: 8px 20px; cursor: pointer;">Schedule Interview</button>
</form>

<hr>

<h3>Scheduled Interviews</h3>
<table border="1" width="100%" style="border-collapse: collapse; text-align: left;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="padding: 8px;">Application ID</th>
            <th style="padding: 8px;">Name</th>
            <th style="padding: 8px;">Date</th>
            <th style="padding: 8px;">Time</th>
            <th style="padding: 8px;">Location</th>
            <th style="padding: 8px;">Validated By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($schedules as $schedule)
            <tr>
                <td style="padding: 8px;">{{ $schedule->applicant->application_id ?? 'N/A' }}</td>
                <td style="padding: 8px;">{{ $schedule->applicant->first_name ?? '' }} {{ $schedule->applicant->last_name ?? '' }}</td>
                <td style="padding: 8px;">{{ $schedule->schedule_date }}</td>
                <td style="padding: 8px;">{{ $schedule->schedule_time }}</td>
                <td style="padding: 8px;">{{ $schedule->location }}</td>
                <td style="padding: 8px;">{{ $schedule->validator->first_name ?? 'Unknown' }} {{ $schedule->validator->last_name ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 10px;">No interviews scheduled yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    const apiBase = "{{ url('admin/hr3') }}";

    // Handle Specialization Loading
    document.getElementById('department').addEventListener('change', function() {
        let deptId = this.value;
        let specSelect = document.getElementById('specialization');
        let appSelect = document.getElementById('applicant');

        specSelect.innerHTML = '<option value="">Loading...</option>';
        appSelect.innerHTML = '<option value="">Select Applicant</option>';

        if (!deptId) return;

        fetch(`${apiBase}/get-specializations/${deptId}`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">Select Specialization</option>';
                data.forEach(item => {
                    let opt = document.createElement('option');
                    opt.value = item.specialization;
                    opt.textContent = item.specialization;
                    specSelect.appendChild(opt);
                });
            });
    });

    // Handle Applicant Loading
    document.getElementById('specialization').addEventListener('change', function() {
        const deptId = document.getElementById('department').value;
        const specValue = this.value;
        const appSelect = document.getElementById('applicant');

        if (!specValue) return;
        appSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`{{ url('admin/hr3/get-interview-applicants') }}/${deptId}?spec=${encodeURIComponent(specValue)}`)
        .then(res => res.json())
        .then(data => {
            appSelect.innerHTML = '<option value="">Select Applicant</option>';
            data.forEach(app => {
                let opt = document.createElement('option');
                opt.value = app.id; // Primary key for form submission
                // Using application_id from your table schema
                opt.textContent = `${app.application_id} - ${app.first_name} ${app.last_name}`;
                appSelect.appendChild(opt);
            });
        });
    });
</script>
@endsection