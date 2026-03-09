@extends('admin.hr2.layouts.app')

@section('title','Learning Materials')

@section('content')
<div class="learning-materials-admin">

   <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Learning Materials</h2>
    <a href="{{ route('learning.index') }}" 
       style="padding: 8px 16px; background: #4b5563; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Modules
    </a>
    </div>

    @if(session('success'))
        <div style="padding:1rem; background:#D1FAE5; color:#065F46; border-radius:5px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="padding:1rem; background:#FEE2E2; color:#991B1B; border-radius:5px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Dropdown Selection -->
    <div style="display:flex; gap:20px; margin-bottom:20px;">
        <div>
            <label>Department</label>
            <select id="deptSelect">
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Specialization</label>
            <select id="specSelect" disabled>
                <option value="">Select Specialization</option>
            </select>
        </div>
        <div>
            <label>Module</label>
            <select id="moduleSelect" disabled>
                <option value="">Select Module</option>
            </select>
        </div>
    </div>

    <!-- Material Form -->
    <form id="materialForm" method="POST" action="" enctype="multipart/form-data" style="display:none; margin-bottom:30px;">
        @csrf
        <input type="hidden" name="module_code" id="moduleCode">

        <div>
            <label>Material Title</label>
            <input type="text" name="title" required>
        </div>
        <div>
            <label>URL (Optional)</label>
            <input type="url" name="url" placeholder="https://example.com">
        </div>
        <div>
            <label>File (Optional)</label>
            <input type="file" name="file">
        </div>
        <div>
            <label>Type</label>
            <select name="type">
                <option value="file">File</option>
                <option value="url">URL</option>
            </select>
        </div>
        <br>
        <button type="submit" style="padding:8px 16px; background:#2563eb; color:white; border:none; border-radius:6px;">Add Material</button>
    </form>

    <!-- Existing Materials Table -->
    <h3>Existing Materials</h3>
    <table border="1" cellpadding="8" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Title</th>
                <th>URL</th>
                <th>File</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="materialsTable">
            <tr><td colspan="5" style="text-align:center;">Select a module to see materials</td></tr>
        </tbody>
    </table>

</div>

<script>
const deptSelect = document.getElementById("deptSelect");
const specSelect = document.getElementById("specSelect");
const moduleSelect = document.getElementById("moduleSelect");
const materialForm = document.getElementById("materialForm");
const moduleCodeInput = document.getElementById("moduleCode");
const materialsTable = document.getElementById("materialsTable");

// Load Specializations
deptSelect.addEventListener("change", function() {
    const dept = this.value;
    specSelect.innerHTML = '<option>Loading...</option>';
    specSelect.disabled = true;
    moduleSelect.innerHTML = '<option>Select Module</option>';
    moduleSelect.disabled = true;
    materialForm.style.display = 'none';

    if(!dept) return;

    fetch(`/admin/hr2/departments/${dept}/specializations`)
        .then(res => res.json())
        .then(data => {
            specSelect.innerHTML = '<option value="">Select Specialization</option>';
            data.forEach(s => specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`);
            specSelect.disabled = false;
        });
});

// Load Modules
specSelect.addEventListener("change", function() {
    const dept = deptSelect.value;
    const spec = this.value;
    moduleSelect.innerHTML = '<option>Loading...</option>';
    moduleSelect.disabled = true;
    materialForm.style.display = 'none';

    if(!dept || !spec) return;

    fetch(`/admin/hr2/modules/${dept}/${encodeURIComponent(spec)}`)
        .then(res => res.json())
        .then(data => {
            moduleSelect.innerHTML = '<option value="">Select Module</option>';
            data.forEach(m => moduleSelect.innerHTML += `<option value="${m.module_code}">${m.module_name} (${m.module_code})</option>`);
            moduleSelect.disabled = false;
        });
});

// Load Materials on module select
moduleSelect.addEventListener("change", function() {
    const moduleCode = this.value;
    if(!moduleCode) {
        materialForm.style.display = 'none';
        materialsTable.innerHTML = '<tr><td colspan="5" style="text-align:center;">Select a module to see materials</td></tr>';
        return;
    }

    materialForm.style.display = 'block';
    moduleCodeInput.value = moduleCode;
    materialForm.action = `/admin/hr2/${moduleCode}/materials`;

    fetch(`/admin/hr2/materials/${moduleCode}/list`)
        .then(res => res.json())
        .then(data => {
            if(data.length === 0){
                materialsTable.innerHTML = '<tr><td colspan="5" style="text-align:center;">No materials added yet.</td></tr>';
            } else {
                materialsTable.innerHTML = '';
                data.forEach(m => {
                    materialsTable.innerHTML += `
                        <tr>
                            <td>${m.title}</td>
                            <td>${m.url ? `<a href="${m.url}" target="_blank">Open</a>` : ''}</td>
                            <td>${m.file_path ? `<a href="/storage/${m.file_path}" target="_blank">Download</a>` : ''}</td>
                            <td>${m.type}</td>
                            <td>
                                <form method="POST" action="/admin/hr2/materials/${m.id}" onsubmit="return confirm('Delete this material?');">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" style="color:red;background:none;border:none;cursor:pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>`;
                });
            }
        });
});
</script>
@endsection