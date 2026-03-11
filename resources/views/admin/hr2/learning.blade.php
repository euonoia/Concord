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
        <div style="padding: 1rem; margin-bottom: 1.5rem; background: #d1fae5; color: #065f46; border-radius: 6px; border: 1px solid #a7f3d0;">
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
                <label for="competencySelect">Competency</label>
                <select name="competency_code" id="competencySelect">
                    <option value="">Select Competency</option>
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
                <select name="is_mandatory" id="is_mandatory">
                    <option value="1" {{ old('is_mandatory') == 1 ? 'selected' : '' }}>Mandatory</option>
                    <option value="0" {{ old('is_mandatory') == 0 ? 'selected' : '' }}>Optional</option>
                </select>
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
                <td>{{ $m->module_code }}</td>
                <td>{{ $m->module_name }}</td>
                <td>{{ $m->dept_code }} / {{ $m->specialization_name }}</td>
                <td>{{ $m->module_type }}</td>
                <td>{{ $m->duration_hours }}h</td>
                <td>@if($m->is_mandatory) Mandatory @else Optional @endif</td>
                <td>
                    <form method="POST" action="{{ route('learning.destroy',$m->id) }}" onsubmit="return confirm('Archive this module?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-archive">Archive</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7">No modules found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const deptSelect = document.getElementById("deptSelect");
    const specSelect = document.getElementById("specSelect");
    const competencySelect = document.getElementById("competencySelect");
    const moduleCode = document.getElementById("moduleCode");

    deptSelect.addEventListener("change", function(){
        const dept = this.value;
        specSelect.innerHTML = '<option value="">Select Specialization</option>';
        competencySelect.innerHTML = '<option value="">Select Competency</option>';
        moduleCode.value = '';

        if(!dept) return;

        specSelect.innerHTML = '<option>Loading...</option>';
        specSelect.disabled = true;

        fetch(`/admin/hr2/departments/${dept}/specializations`)
        .then(res => res.json())
        .then(data => {
            specSelect.innerHTML = '<option value="">Select Specialization</option>';
            if (data.length === 0) {
                specSelect.innerHTML = '<option value="">No specializations found</option>';
            } else {
                data.forEach(s => {
                    // Check if s is an object or string to prevent [object Object]
                    let val = (typeof s === 'object' && s !== null) ? s.specialization_name : s;
                    let opt = document.createElement('option');
                    opt.value = val;
                    opt.textContent = val;
                    specSelect.appendChild(opt);
                });
            }
        })
        .catch(err => {
            specSelect.innerHTML = '<option value="">Error loading</option>';
            console.error(err);
        })
        .finally(() => { specSelect.disabled = false; });
    });

    specSelect.addEventListener("change", function(){
        const dept = deptSelect.value;
        const spec = this.value;
        if(!dept || !spec) return;

        // Update Module Code
        moduleCode.value = "Generating...";
        fetch(`/admin/hr2/generate-module-code/${dept}/${encodeURIComponent(spec)}`)
            .then(res => res.json())
            .then(data => { moduleCode.value = data.code; })
            .catch(error => { moduleCode.value = "Error"; console.error(error); });

        // Load Competencies
        competencySelect.innerHTML = '<option>Loading...</option>';
        fetch(`/admin/hr2/departments/${dept}/${encodeURIComponent(spec)}/competencies`)
            .then(res => res.json())
            .then(data => {
                competencySelect.innerHTML = '<option value="">Select Competency</option>';
                if(!data || data.length === 0){
                    competencySelect.innerHTML = '<option value="">No competencies found</option>';
                } else {
                    data.forEach(c => {
                        // For competencies, we usually need the code as value and name as label
                        let name = c.name || c.title || "Unnamed Competency";
                        let code = c.competency_code || c.code;
                        
                        let opt = document.createElement('option');
                        opt.value = code;
                        opt.textContent = name;
                        competencySelect.appendChild(opt);
                    });
                }
            })
            .catch(error => { 
                competencySelect.innerHTML = '<option value="">Error loading</option>'; 
                console.error(error); 
            });
    });
});
</script>

<style>
    /* Added a small style for the archive button for better UI */
    .btn-archive {
        background: transparent;
        color: #ef4444;
        border: 1px solid #ef4444;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: 0.2s;
    }
    .btn-archive:hover {
        background: #ef4444;
        color: white;
    }
</style>
@endsection