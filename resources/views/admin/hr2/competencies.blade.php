@extends('admin.hr2.layouts.app')

@section('title','Competency Framework')

@section('content')
<div class="container p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Competency Framework</h3>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- CREATE COMPETENCY --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Add New Competency</h6>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('competencies.store') }}" method="POST">
                @csrf
                <div class="row g-3">

                    {{-- Department --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Department</label>
                        <select name="dept_code" id="deptSelect" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->department_id }}">
                                    {{ $d->department_id }} - {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Specialization --}}
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Specialization</label>
                        <select name="specialization_name" id="specSelect" class="form-select">
                            <option value="">Select Specialization</option>
                        </select>
                    </div>

                    {{-- Group --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Group</label>
                        <select name="competency_group" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Medical">Medical</option>
                            <option value="Technical">Technical</option>
                            <option value="Leadership">Leadership</option>
                            <option value="Soft Skills">Soft Skills</option>
                            <option value="Administrative">Administrative</option>
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Competency Title</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               placeholder="Emergency Response"
                               required>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Describe the competency..."></textarea>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary px-4">
                            <i class="bi bi-plus-lg me-1"></i>
                            Add Competency
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- COMPETENCY LIST --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Specialization</th>
                        <th>Group</th>
                        <th>Created</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($competencies as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->competency_code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $item->name }}</div>
                            <small class="text-muted">{{ Str::limit($item->description,60) }}</small>
                        </td>
                        <td>{{ $item->department->name ?? '-' }}</td>
                        <td>{{ $item->specialization_name ?? '-' }}</td>
                        <td>{{ $item->competency_group }}</td>
                        <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">
                            <form action="{{ route('competencies.destroy',$item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this competency?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link text-danger p-0">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No competencies registered.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- AJAX Specialization Loader --}}
<script>
document.addEventListener("DOMContentLoaded", function(){
    const deptSelect = document.getElementById("deptSelect");
    const specSelect = document.getElementById("specSelect");

    deptSelect.addEventListener("change", function(){
        let dept = this.value;
        specSelect.innerHTML = '<option value="">Select Specialization</option>';
        if(!dept) return;

        specSelect.innerHTML = '<option>Loading...</option>';
        specSelect.disabled = true;

        fetch(`/admin/hr2/get-specializations/${dept}`)
        .then(res => res.json())
        .then(data => {
            specSelect.innerHTML = '<option value="">Select Specialization</option>';
            if(data.length === 0){
                specSelect.innerHTML = '<option>No specializations found</option>';
            } else {
                data.forEach(function(spec){
                    specSelect.innerHTML += `<option value="${spec}">${spec}</option>`;
                });
            }
            specSelect.disabled = false;
        })
        .catch(error => {
            specSelect.innerHTML = '<option>Error loading</option>';
            specSelect.disabled = false;
            console.error(error);
        });
    });
});
</script>
@endsection