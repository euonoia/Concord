@extends('admin.hr2.layouts.app')

@section('title', 'Learning Management')

@section('content')
<h2>Learning Management</h2>
<form method="POST" action="{{ route('learning.store') }}">
    @csrf

    <input type="text" name="module_name" placeholder="Module Name" required value="{{ old('module_name') }}">

    {{-- Department --}}
    <select name="dept_code" id="deptSelect" required>
        <option value="">Select Department</option>
        @foreach($departments as $d)
            <option value="{{ $d->department_id }}">
                {{ $d->department_id }} - {{ $d->name }}
            </option>
        @endforeach
    </select>

    {{-- Specialization --}}
    <select name="specialization_name" id="specSelect" required>
        <option value="">Select Specialization</option>
    </select>

    {{-- Auto-generated module code --}}
    <input type="hidden" name="module_code" id="moduleCode" readonly placeholder="Auto Generated Code">

    {{-- Module Type --}}
    <select name="module_type">
        <option value="Compliance">Compliance</option>
        <option value="Clinical">Clinical</option>
        <option value="Simulation">Simulation</option>
        <option value="Research">Research</option>
        <option value="Other">Other</option>
    </select>

    {{-- Duration --}}
    <div class="mb-3">
    <label class="font-medium text-gray-700">Duration:</label>
    <select name="duration_hours" class="w-full p-2 border rounded mt-1" required>
        <option value="1">1 hour</option>
        <option value="2">2 hours</option>
        <option value="3">3 hours</option>
        <option value="4">4 hours</option>
        <option value="5">5 hours</option>
        <option value="6">6 hours</option>
        <option value="8">8 hours</option>
    </select>
    </div>

    <textarea name="description" placeholder="Module Description" rows="3" required class="w-full p-2 border rounded mb-3">{{ old('description') }}</textarea>

    {{-- Friendly is_mandatory toggle --}}
    <label for="is_mandatory" class="block mb-2 font-medium text-gray-700">Module Requirement:</label>
    <select name="is_mandatory" id="is_mandatory" class="p-2 border rounded">
        <option value="1" {{ old('is_mandatory') == 1 ? 'selected' : '' }}>Mandatory (Must Complete)</option>
        <option value="0" {{ old('is_mandatory') == 0 ? 'selected' : '' }}>Optional (Recommended)</option>
    </select>

    <button type="submit" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Add Module
    </button>
</form>
@if(session('success'))
    <div style="padding: 10px; background: #d4edda; color: #155724;">{{ session('success') }}</div>
@endif

<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Dept</th>
            <th>Specialization</th>
            <th>Type</th>
            <th>Duration (hrs)</th>
            <th>Enrollments</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($modules as $m)
        <tr>
            <td>{{ $m->id }}</td>
            <td>{{ $m->module_code }}</td>
            <td>{{ $m->module_name }}</td>
            <td>{{ $m->description }}</td>
            <td>{{ $m->dept_code }}</td>
            <td>{{ $m->specialization_name }}</td>
            <td>{{ $m->module_type }}</td>
            <td>{{ $m->duration_hours }}</td>
            <td>{{ $m->enrolls_count ?? 0 }}</td>
            <td>
                <form method="POST" action="{{ route('learning.destroy',$m->id) }}" onsubmit="return confirm('Archive this module?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="color:red;">Archive</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;">No modules available.</td></tr>
        @endforelse
    </tbody>
</table>


<script>

document.addEventListener("DOMContentLoaded", function(){

    const deptSelect = document.getElementById("deptSelect");
    const specSelect = document.getElementById("specSelect");
    const moduleCode = document.getElementById("moduleCode");

    deptSelect.addEventListener("change", function(){

        let dept = this.value;

        // reset specialization
        specSelect.innerHTML = '<option value="">Select Specialization</option>';
        moduleCode.value = "";

        if(!dept) return;

        // show loading state
        specSelect.innerHTML = '<option>Loading specializations...</option>';
        specSelect.disabled = true;

        fetch(`/admin/hr2/departments/${dept}/specializations`)
        .then(res => res.json())
        .then(data => {

            specSelect.innerHTML = "";

            if(data.length === 0){
                specSelect.innerHTML = '<option>No specializations found</option>';
            }else{

                specSelect.innerHTML = '<option value="">Select Specialization</option>';

                data.forEach(function(s){
                    specSelect.innerHTML += `
                        <option value="${s.specialization_name}">
                            ${s.specialization_name}
                        </option>
                    `;
                });

            }

            specSelect.disabled = false;

        })
        .catch(error => {

            specSelect.innerHTML = '<option>Error loading specializations</option>';
            specSelect.disabled = false;

            console.error(error);

        });

    });


    specSelect.addEventListener("change", function(){

        let dept = deptSelect.value;
        let spec = this.value;

        if(!dept || !spec) return;

        moduleCode.value = "Generating...";

        fetch(`/admin/hr2/generate-module-code/${dept}/${encodeURIComponent(spec)}`)
        .then(res => res.json())
        .then(data => {

            moduleCode.value = data.code;

        })
        .catch(error => {

            moduleCode.value = "Error";
            console.error(error);

        });

    });

});

</script>
@endsection