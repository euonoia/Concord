@extends('admin.hr3.layouts.app')

@section('content')

<h2><i class="bi bi-book"></i> Training Schedule</h2>

@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

<div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <form action="{{ route('training_schedule.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <div>
                <label style="font-weight: bold;">Select Trainee (Completed Only):</label>
                <select name="employee_id" id="trainee_select" class="form-control" style="width: 100%; padding: 8px;" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($eligibleEmployees as $emp)
                        <option value="{{ $emp->employee_id }}">{{ $emp->employee_id }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Department:</label>
                    <input type="text" id="view_dept" class="form-control" style="width: 100%; padding: 8px; background: #f8f9fa;" readonly>
                </div>
                <div style="flex: 1;">
                    <label>Specialization:</label>
                    <input type="text" id="view_spec" class="form-control" style="width: 100%; padding: 8px; background: #f8f9fa;" readonly>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
            <div>
                <label>Competency:</label>
                <select name="competency_code" id="competency_select" class="form-control" style="width: 100%; padding: 8px;" required>
                    <option value="">-- Select Trainee First --</option>
                </select>
            </div>

            <div>
                <label>Venue:</label>
                <input type="text" name="venue" class="form-control" style="width: 100%; padding: 8px;" placeholder="Room/Location" required>
            </div>

            <div>
                <label>Date:</label>
                <input type="date" name="training_date" class="form-control" style="width: 100%; padding: 8px;" required min="{{ date('Y-m-d') }}">
            </div>

            <div>
                <label>Time:</label>
                <input type="time" name="training_time" class="form-control" style="width: 100%; padding: 8px;" required>
            </div>
        </div>

        <button type="submit" style="margin-top: 20px; background: #28a745; color: white; border: none; padding: 12px 25px; border-radius: 4px; cursor: pointer; font-weight: bold;">
            <i class="bi bi-calendar-check"></i> Confirm Training Schedule
        </button>
    </form>
</div>

<h3 style="margin-top: 40px; margin-bottom: 15px;"><i class="bi bi-list-ul"></i> Active Training List</h3>
<div style="overflow-x: auto;">
 <table width="100%" style="border-collapse: collapse; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <thead>
        <tr style="background: #f4f4f4; border-bottom: 2px solid #ddd;">
            <th style="padding: 12px; text-align: left;">Trainee ID</th>
            <th style="padding: 12px; text-align: left;">Competency</th>
            <th style="padding: 12px; text-align: left;">Date & Time</th>
            <th style="padding: 12px; text-align: left;">Venue</th>
            <th style="padding: 12px; text-align: left;">Trainer Name</th> </tr>
    </thead>
    <tbody>
        @forelse($schedules as $s)
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;">{{ $s->employee_id }}</td>
            <td style="padding: 12px;">
                <span style="background: #e9ecef; padding: 4px 8px; border-radius: 4px;">{{ $s->competency_code }}</span>
            </td>
            <td style="padding: 12px;">
                {{ date('M d, Y', strtotime($s->training_date)) }} | {{ date('h:i A', strtotime($s->training_time)) }}
            </td>
            <td style="padding: 12px;">{{ $s->venue }}</td>
            <td style="padding: 12px; color: #007bff;">
                <strong>
                    @if($s->trainer)
                        {{ $s->trainer->first_name }} {{ $s->trainer->last_name }}
                    @else
                        {{ $s->trainer_id }} @endif
                </strong>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="padding: 20px; text-align: center; color: #888;">No active training schedules found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

<script>
    document.getElementById('trainee_select').addEventListener('change', function() {
        let empId = this.value;
        let compSelect = document.getElementById('competency_select');
        let deptInput = document.getElementById('view_dept');
        let specInput = document.getElementById('view_spec');
        
        if(!empId) {
            compSelect.innerHTML = '<option value="">-- Select Trainee First --</option>';
            deptInput.value = '';
            specInput.value = '';
            return;
        }

        compSelect.innerHTML = '<option>Loading...</option>';

        fetch(`{{ url('admin/hr3/get-verified-competencies') }}/${empId}`)
            .then(res => res.json())
            .then(data => {
                // Update Employee Info
                deptInput.value = data.info ? data.info.department_id : 'N/A';
                specInput.value = data.info ? data.info.specialization : 'N/A';

                // Update Competency Dropdown
                compSelect.innerHTML = '<option value="">-- Select Competency --</option>';
                if(data.competencies && data.competencies.length > 0) {
                    data.competencies.forEach(item => {
                        compSelect.innerHTML += `<option value="${item.competency_code}">${item.competency_code}</option>`;
                    });
                } else {
                    compSelect.innerHTML = '<option value="">No completed competencies</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                compSelect.innerHTML = '<option value="">Error loading data</option>';
            });
    });
</script>

@endsection