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
            <button class="btn btn-outline-primary" type="button" id="customToggleBtn">
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

    {{-- CREATE COMPETENCY --}}
    <div class="{{ $errors->any() ? '' : 'd-none' }} mb-4" id="formWrapper">
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
                            <input type="text" name="title" class="form-control border-2" placeholder="e.g. Critical Care" required>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label small fw-bold text-uppercase">Description</label>
                            <textarea name="description" class="form-control border-2" rows="1"></textarea>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <form method="GET" class="row g-2 align-items-center">
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
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($competencies as $item)
                    <tr>
                        <td class="ps-4">
                             <div class="fw-bold text-dark">{{ $item->name }}</div>
                             <small class="text-muted">{{ $item->competency_code }}</small>
                        </td>
                        <td>
                            <div class="small">{{ $item->department_name }}</div>
                            <div class="text-muted extra-small">{{ $item->specialization_name }}</div>
                        </td>
                        <td><span class="badge bg-info text-dark fw-normal">{{ $item->competency_group }}</span></td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('competencies.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No competencies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .card { border-radius: 12px; }
    .btn { border-radius: 8px; }
</style>

<script>
/**
 * Logic for Dynamic Dropdowns
 */
function loadSpecs(dept, target, selected = null){
    if(!dept){
        target.innerHTML = '<option value="">All Specializations</option>';
        return;
    }

    fetch(`/admin/hr2/get-specializations/${dept}`)
    .then(res => res.json())
    .then(data => {
        console.log("Debug Data:", data); // Check your F12 console to see exactly what this looks like
        target.innerHTML = '<option value="">All Specializations</option>';
        
        data.forEach(spec => {
            let opt = document.createElement('option');
            
            // Logic to handle both Strings and Objects
            if (typeof spec === 'object' && spec !== null) {
                // If it's an object, get the 'specialization_name' property
                opt.value = spec.specialization_name; 
                opt.textContent = spec.specialization_name;
            } else {
                // If it's just a string, use it directly
                opt.value = spec;
                opt.textContent = spec;
            }
            
            // Match selection
            if(selected && (selected == opt.value)) opt.selected = true;
            
            target.appendChild(opt);
        });
    })
    .catch(err => {
        console.error("Fetch Error:", err);
        target.innerHTML = '<option value="">Error loading</option>';
    });
}

document.addEventListener("DOMContentLoaded", function(){
    const btn = document.getElementById('customToggleBtn');
    const wrapper = document.getElementById('formWrapper');

    /**
     * TOGGLE FIX: Uses e.stopPropagation() to ignore the 
     * sidebar click-away listener in your app.blade.php
     */
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); 
        wrapper.classList.toggle('d-none');
    });

    const deptFilter = document.getElementById("deptFilter");
    const specFilter = document.getElementById("specFilter");
    const deptCreate = document.getElementById("deptCreate");
    const specCreate = document.getElementById("specCreate");

    // Initial load for filters if deptCode is set
    @if(isset($deptCode) && $deptCode)
        loadSpecs("{{ $deptCode }}", specFilter, "{{ $specialization ?? '' }}");
    @endif

    // Event Listeners
    deptFilter.addEventListener("change", function(){ loadSpecs(this.value, specFilter); });
    deptCreate.addEventListener("change", function(){ loadSpecs(this.value, specCreate); });
});
</script>
@endsection