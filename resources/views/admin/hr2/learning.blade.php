@extends('admin.hr2.layouts.app')

@section('title', 'Learning Management')

@section('content')
<div class="learning-hr2-admin">
     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Learning Modules</h2>
        <a href="{{ route('learning.materials.selector') }}" 
        style="padding: 8px 16px; background: #4b5563; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
            Learning Materials
        </a>
        </div>

    @if(session('success'))
        <div style="padding: 1rem; margin-bottom: 1.5rem; background: var(--color-green-50); color: var(--color-green-800); border-radius: 6px; border: 1px solid var(--color-green-200);">
            {{ session('success') }}
        </div>
    @endif

<form method="POST" action="{{ route('learning.store') }}" class="module-form">
    @csrf

    <div class="form-section">
        <h3 class="section-title">Module Identity</h3>
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="module_name">Module Name</label>
                <input type="text" name="module_name" id="module_name" placeholder="e.g., Advanced Clinical Workflow" required value="{{ old('module_name') }}">
                <small class="help-text">Give the module a clear, descriptive title.</small>
            </div>

            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3" required placeholder="Outline the learning objectives and course content...">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <div class="form-section">
        <h3 class="section-title">Categorization & Logic</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="deptSelect">Department</label>
                <select name="dept_code" id="deptSelect" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->department_id }}">{{ $d->department_id }} - {{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="specSelect">Specialization</label>
                <select name="specialization_name" id="specSelect" required>
                    <option value="">Select Specialization</option>
                </select>
            </div>

            <div class="form-group">
                <label for="module_type">Module Type</label>
                <select name="module_type" id="module_type">
                    <option value="Compliance">Compliance</option>
                    <option value="Clinical">Clinical</option>
                    <option value="Simulation">Simulation</option>
                    <option value="Research">Research</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="duration_hours">Estimated Duration</label>
                <select name="duration_hours" id="duration_hours" required>
                    @foreach([1,2,3,4,5,6,8] as $h)
                        <option value="{{ $h }}">{{ $h }} Hour{{ $h > 1 ? 's' : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <div class="form-section">
        <div class="form-grid">
            <div class="form-group">
                <label for="is_mandatory">Requirement Status</label>
                <div class="select-wrapper">
                    <select name="is_mandatory" id="is_mandatory">
                        <option value="1" {{ old('is_mandatory') == 1 ? 'selected' : '' }}>Mandatory (Required for all)</option>
                        <option value="0" {{ old('is_mandatory') == 0 ? 'selected' : '' }}>Optional (Recommended)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="module_code" id="moduleCode">

    <div class="form-actions">
        <button type="button" class="btn-secondary" onclick="window.history.back()">Cancel</button>
        <button type="submit" class="btn-submit">Register New Module</button>
    </div>
</form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Dept / Spec</th>
                    <th>Type</th>
                    <th>Hrs</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modules as $m)
                <tr>
                    <td style="font-family: monospace; font-weight: 600; color: var(--color-blue-700);">{{ $m->module_code }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $m->module_name }}</div>
                        <div style="font-size: 0.75rem; color: var(--color-gray-500);">ID: #{{ $m->id }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem;">{{ $m->dept_code }}</div>
                        <div style="font-size: 0.75rem; color: var(--color-gray-500);">{{ $m->specialization_name }}</div>
                    </td>
                    <td><span class="badge-type" style="padding: 2px 8px; background: var(--color-blue-50); color: var(--color-blue-700); border-radius: 4px; font-size: 0.75rem;">{{ $m->module_type }}</span></td>
                    <td>{{ $m->duration_hours }}h</td>
                    <td>
                        @if($m->is_mandatory)
                            <span class="badge-mandatory">Mandatory</span>
                        @else
                            <span class="badge-optional">Optional</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('learning.destroy',$m->id) }}" onsubmit="return confirm('Archive this module?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: var(--color-red-500); background: none; border: none; cursor: pointer; font-size: 0.85rem; font-weight: 500;">Archive</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--color-gray-400);">No modules found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function(){
    const deptSelect = document.getElementById("deptSelect");
    const specSelect = document.getElementById("specSelect");
    const moduleCode = document.getElementById("moduleCode");

    deptSelect.addEventListener("change", function(){
        let dept = this.value;
        specSelect.innerHTML = '<option value="">Select Specialization</option>';
        moduleCode.value = "";

        if(!dept) return;

        specSelect.innerHTML = '<option>Loading...</option>';
        specSelect.disabled = true;

        fetch(`/admin/hr2/departments/${dept}/specializations`)
        .then(res => res.json())
        .then(data => {
            specSelect.innerHTML = '<option value="">Select Specialization</option>';
            if(data.length === 0){
                specSelect.innerHTML = '<option>No specializations found</option>';
            } else {
                data.forEach(function(s){
                    specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`;
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