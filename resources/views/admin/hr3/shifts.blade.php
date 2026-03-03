@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Shift Management</h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff; padding: 25px; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label"><strong>1. Select Department:</strong></label>
                <select id="dept_selector" class="form-control">
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->department_id }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <form action="{{ route('shifts.store') }}" method="POST" id="shift_form" style="display: none;">
            @csrf
            <div class="row mb-3">
                <div class="col-md-7">
                    <label class="form-label"><strong>2. Select Employee (by Specialization):</strong></label>
                    <select name="employee_id" id="employee_selector" class="form-control" required></select>
                </div>
                <div class="col-md-5">
                    <label class="form-label"><strong>3. Shift Type:</strong></label>
                    <select name="shift_name" class="form-control" required>
                        <option value="Morning Shift">Morning (08:00 - 17:00)</option>
                        <option value="Afternoon Shift">Afternoon (14:00 - 22:00)</option>
                        <option value="Night Shift">Night (22:00 - 06:00)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label"><strong>4. Working Days:</strong></label>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label style="margin-right: 15px; cursor: pointer; font-weight: normal;">
                            <input type="checkbox" name="days[]" value="{{ $day }}"> {{ $day }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="background: #0d6efd; color: white; padding: 10px 25px; border: none; border-radius: 4px;">Confirm Assignment</button>
        </form>
    </div>

    <div class="mt-5">
        <h4>Current Schedules</h4>
        <table border="1" style="width: 100%; border-collapse: collapse; background: #fff; margin-top: 15px;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>Staff Name</th>
                    <th>Shift Type</th>
                    <th>Day</th>
                    <th>Fixed Hours (24h)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $s)
                <tr>
                    <td style="padding: 10px;">{{ $s->employee->last_name }}, {{ $s->employee->first_name }}</td>
                    <td>
                        @php
                            $badgeColor = match($s->shift_name) {
                                'Morning Shift' => '#f39c12',
                                'Afternoon Shift' => '#27ae60',
                                'Night Shift' => '#3f51b5',
                                default => '#333'
                            };
                        @endphp
                        <span style="color: {{ $badgeColor }}; font-weight: bold;">{{ $s->shift_name }}</span>
                    </td>
                    <td>{{ $s->day_of_week }}</td>
                    <td><code>{{ date('H:i', strtotime($s->start_time)) }} - {{ date('H:i', strtotime($s->end_time)) }}</code></td>
                    <td>
                        <form action="{{ route('shifts.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Remove this schedule?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: #e74c3c; background: none; border: none; cursor: pointer;">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; padding: 20px;">No scheduled shifts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('dept_selector').addEventListener('change', function() {
    const deptId = this.value;
    const empSelect = document.getElementById('employee_selector');
    const form = document.getElementById('shift_form');

    if (!deptId) { form.style.display = 'none'; return; }

    fetch(`/admin/hr3/get-employees/${deptId}`)
        .then(res => res.json())
        .then(data => {
            empSelect.innerHTML = '<option value="">-- Choose Staff Member --</option>';
            Object.entries(data).forEach(([spec, emps]) => {
                let group = document.createElement('optgroup');
                group.label = spec;
                emps.forEach(e => {
                    let opt = new Option(`${e.last_name}, ${e.first_name}`, e.employee_id);
                    group.appendChild(opt);
                });
                empSelect.appendChild(group);
            });
            form.style.display = 'block';
        });
});
</script>
@endsection