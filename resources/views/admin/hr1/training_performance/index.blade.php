@extends('admin.hr1.layouts.app')

@section('title','Training Performance')

@section('content')
<div class="container p-5">

    <h2 class="mb-4">Training Performance</h2>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Department</label>
            <select name="department" id="departmentSelect" class="form-select">
                <option value="">Select Department</option>
                @foreach($departments as $d)
                <option value="{{ $d->department_id }}" {{ request('department') == $d->department_id ? 'selected' : '' }}>
                    {{ $d->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Specialization</label>
            <select name="specialization" id="specializationSelect" class="form-select">
                <option value="">Select Specialization</option>
                @foreach($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                        {{ $spec }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search name or ID..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Specialization</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>{{ $emp->employee_id }}</td>
                    <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                    <td>{{ $emp->department_id }}</td>
                    <td>{{ $emp->specialization ?? '-' }}</td>
                    <td>
                        <a href="{{ route('hr1.training.performance.show', $emp->employee_id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $employees->links() }}
    </div>
</div>

{{-- AJAX Specializations --}}
<script>
document.addEventListener("DOMContentLoaded", function(){
    const deptSelect = document.getElementById("departmentSelect");
    const specSelect = document.getElementById("specializationSelect");

    deptSelect.addEventListener("change", function(){
        let dept = this.value;
        specSelect.innerHTML = '<option value="">Loading...</option>';
        if(!dept) return;

        fetch(`/admin/hr1/training-performance/get-specializations/${dept}`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">Select Specialization</option>';
                data.forEach(spec => {
                    const opt = document.createElement('option');
                    opt.value = spec;
                    opt.textContent = spec;
                    specSelect.appendChild(opt);
                });
            });
    });
});
</script>
@endsection