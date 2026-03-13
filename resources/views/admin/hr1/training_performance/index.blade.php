@extends('admin.hr1.layouts.app')

@section('title','Training Performance')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">Training & Performance</h3>
            <p class="text-white">Monitor employee growth, training completion, and performance evaluations.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
             <span class="text-white small fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Performance Analytics</span>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="hr1-filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <select name="department" id="departmentSelect" class="form-select form-select-sm">
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->department_id }}" {{ request('department') == $d->department_id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Specialization</label>
                <select name="specialization" id="specializationSelect" class="form-select form-select-sm">
                    <option value="">Select Specialization</option>
                    @foreach($specializations as $spec)
                        <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                            {{ $spec }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search Employee</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Name or ID..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm"><i class="bi bi-filter me-1"></i> Search</button>
            </div>
        </form>
    </div>

    {{-- Performance Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6 class="mb-0">Employee Staffing List</h6>
            <div class="small text-muted">Tracking {{ $employees->total() }} evaluations</div>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 120px;">Emp ID</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Specialization</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td class="text-muted font-monospace" style="font-size: 0.8rem;">#{{ $emp->employee_id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                        </td>
                        <td>
                             <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">{{ $emp->department_id }}</span>
                        </td>
                        <td>
                             <span class="small fw-semibold text-muted">{{ $emp->specialization ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('hr1.training.performance.show', $emp->employee_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Report</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No employees found matching the current filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-3 py-2 border-top bg-light">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
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