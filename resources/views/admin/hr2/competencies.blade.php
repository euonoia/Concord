@extends('admin.hr2.layouts.app')

@section('title','Competency Framework')

@section('content')
<div class="container-fluid p-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Competency Framework</h3>
            <p class="text-muted small">Manage and define organizational skill standards</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createFormCollapse">
                <i class="bi bi-plus-lg"></i> New Competency
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($errors->any() || session('success'))
        <div class="row">
            <div class="col-12">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- CREATE COMPETENCY (Collapsible for cleaner UI) --}}
    <div class="collapse {{ $errors->any() ? 'show' : '' }} mb-4" id="createFormCollapse">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Define New Competency</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('competencies.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Department</label>
                            <select name="dept_code" id="deptCreate" class="form-select border-2" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Specialization</label>
                            <select name="specialization_name" id="specCreate" class="form-select border-2">
                                <option value="">Select Specialization</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Group</label>
                            <select name="competency_group" class="form-select border-2" required>
                                <option value="">Select Group</option>
                                <option value="Medical">Medical</option>
                                <option value="Technical">Technical</option>
                                <option value="Leadership">Leadership</option>
                                <option value="Soft Skill">Soft Skill</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-uppercase">Title</label>
                            <input type="text" name="title" class="form-control border-2" placeholder="e.g. Critical Care Nursing" required>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label small fw-bold text-uppercase">Description</label>
                            <textarea name="description" class="form-control border-2" rows="1" placeholder="Brief summary of the competency..."></textarea>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-success w-100 py-2 fw-bold">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FILTER & TABLE SECTION --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <h6 class="mb-0 me-3 fw-bold">Filter By:</h6>
                </div>
                <div class="col-md-3">
                    <select name="dept_code" id="deptFilter" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}" {{ ($deptCode ?? '') == $d->department_id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="specialization" id="specFilter" class="form-select form-select-sm">
                        <option value="">All Specializations</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-dark px-3">Apply</button>
                    <a href="{{ route('competencies.index') }}" class="btn btn-sm btn-light border px-3">Reset</a>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Competency</th>
                        <th>Hierarchy</th>
                        <th>Group</th>
                        <th class="text-center">New Hires</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($competencies as $item)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-light text-primary border me-3 p-2">{{ $item->competency_code }}</div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ Str::limit($item->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small"><strong>Dept:</strong> {{ $item->department_name ?? $item->department_id }}</div>
                            <div class="text-muted small"><strong>Spec:</strong> {{ $item->specialization_name }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-info text-dark fw-normal">{{ $item->competency_group }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $item->new_hire_count }}</span>
                        </td>
                        <td class="small text-muted">
                            {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('competencies.destroy',$item->id) }}" method="POST" onsubmit="return confirm('Delete this competency?')">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/box-opened.svg" alt="Empty" style="width: 120px;" class="mb-3 opacity-50">
                            <p class="text-muted">No competencies found matching your criteria.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .table thead th { font-weight: 600; letter-spacing: 0.5px; border-bottom: none; }
    .card { border-radius: 12px; }
    .btn { border-radius: 8px; }
</style>

<script>
// Existing JS logic remains the same - ensure your routes and selectors are correct
function loadSpecs(dept, target, selected = null){
    if(!dept){
        target.innerHTML = '<option value="">All Specializations</option>';
        return;
    }
    fetch(`/admin/hr2/get-specializations/${dept}`)
    .then(res => res.json())
    .then(data => {
        target.innerHTML = '<option value="">All Specializations</option>';
        data.forEach(spec => {
            let opt = document.createElement('option');
            opt.value = spec;
            opt.textContent = spec;
            if(selected && selected === spec) opt.selected = true;
            target.appendChild(opt);
        });
    });
}

document.addEventListener("DOMContentLoaded", function(){
    const deptFilter = document.getElementById("deptFilter");
    const specFilter = document.getElementById("specFilter");
    const deptCreate = document.getElementById("deptCreate");
    const specCreate = document.getElementById("specCreate");

    @if($deptCode)
        loadSpecs("{{ $deptCode }}", specFilter, "{{ $specialization }}");
    @endif

    deptFilter.addEventListener("change", function(){ loadSpecs(this.value, specFilter); });
    deptCreate.addEventListener("change", function(){ loadSpecs(this.value, specCreate); });
});
</script>
@endsection