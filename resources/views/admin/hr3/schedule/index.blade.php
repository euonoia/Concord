@extends('admin.hr3.layouts.app')

@section('title','Interview Schedule')

@section('content')

<h2>Interview Schedule</h2>

@if(session('success'))
    <div style="color:green; padding: 10px; border: 1px solid green; margin-bottom: 10px;">
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
                {{-- Ensure department_id matches your DB column name --}}
                <option value="{{ $dept->department_id }}">
                    {{ $dept->name }}
                </option>
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
        <label>Applicant (Status: Interview):</label><br>
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

    <button type="submit">Schedule Interview</button>
</form>

<hr>

<h3>Scheduled Interviews</h3>
<table border="1" width="100%" style="border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Applicant</th>
            <th>Date</th>
            <th>Time</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        @forelse($schedules as $schedule)
            <tr>
                <td>{{ $schedule->applicant->first_name ?? 'N/A' }} {{ $schedule->applicant->last_name ?? '' }}</td>
                <td>{{ $schedule->schedule_date }}</td>
                <td>{{ $schedule->schedule_time }}</td>
                <td>{{ $schedule->location }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center;">No interviews scheduled yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    // This helper automatically generates the correct path based on your route nesting
    const apiBase = "{{ url('admin/hr3') }}";

    // 1. Department Change -> Fetch Specializations
    document.getElementById('department').addEventListener('change', function() {
        let deptId = this.value;
        let specSelect = document.getElementById('specialization');
        let appSelect = document.getElementById('applicant');

        specSelect.innerHTML = '<option value="">Loading...</option>';
        appSelect.innerHTML = '<option value="">Select Applicant</option>';

        if (!deptId) {
            specSelect.innerHTML = '<option value="">Select Specialization</option>';
            return;
        }

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
            })
            .catch(err => {
                console.error('Error:', err);
                specSelect.innerHTML = '<option value="">Error loading</option>';
            });
    });

document.getElementById('specialization').addEventListener('change', function() {
    const deptId = document.getElementById('department').value;
    const specValue = this.value; // "Pulmonology / Respiratory Medicine"
    const appSelect = document.getElementById('applicant');

    if (!specValue) return;

    appSelect.innerHTML = '<option value="">Loading...</option>';

    // We build the URL using a query string (?) instead of a slash (/) for the spec
    const url = `{{ url('admin/hr3/get-interview-applicants') }}/${deptId}?spec=${encodeURIComponent(specValue)}`;

    fetch(url, {
        headers: { "Accept": "application/json" }
    })
    .then(res => {
        if (!res.ok) throw new Error('Server error');
        return res.json();
    })
    .then(data => {
        appSelect.innerHTML = '<option value="">Select Applicant</option>';
        
        if (data.length === 0) {
            appSelect.innerHTML = '<option value="">No applicants found with status "interview"</option>';
        } else {
            data.forEach(app => {
                let opt = document.createElement('option');
                opt.value = app.id;
                opt.textContent = `${app.first_name} ${app.last_name}`;
                appSelect.appendChild(opt);
            });
        }
    })
    .catch(err => {
        console.error(err);
        appSelect.innerHTML = '<option value="">Error loading applicants</option>';
    });
});
</script>

@endsection