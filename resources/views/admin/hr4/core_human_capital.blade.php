@extends('admin.hr4.layouts.app')

@section('title','Core Human Capital')

@section('content')

<div class="flex gap-4">
<div class="w-3/4">

<h2>Core Human Capital</h2>

<style>
.tab-link {
    padding: 8px 16px;
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    text-decoration: none;
    color: #374151;
    font-weight: 500;
}
.tab-link:hover {
    background-color: #e5e7eb;
}
</style>

<div style="margin-bottom:20px; display:flex; gap:10px;">
    <a href="#employees" onclick="showTab('employees'); return false;" class="tab-link">Employees</a>
    <a href="#departments" onclick="showTab('departments'); return false;" class="tab-link">Departments</a>
    <a href="#positions" onclick="showTab('positions'); return false;" class="tab-link">Positions</a>
</div>

{{-- EMPLOYEES --}}
<div id="employees" class="tab-section">

<h3>Employees</h3>

<div style="margin-bottom:15px; display:flex; gap:10px;">

    {{-- Department Filter --}}
    <select id="departmentFilter">
        <option value="">All Departments</option>
        @foreach($departments as $d)
            <option value="{{ $d->id }}">{{ $d->name }}</option>
        @endforeach
    </select>

    {{-- Employee Search --}}
    <input
        type="text"
        id="employeeSearch"
        placeholder="Search by name or ID..."
        style="padding:5px;width:250px"
    >

</div>

<table border="1" style="width:100%;border-collapse:collapse" id="employeeTable">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Employee ID</th>
<th>Department</th>
<th>Position</th>
<th>Status</th>
</tr>
</thead>

<tbody>
@foreach($employees as $emp)
<tr data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}" data-empid="{{ $emp->employee_id }}" data-department="{{ $emp->department_id }}">
<td>{{ $emp->id }}</td>
<td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
<td>{{ $emp->employee_id }}</td>
<td>{{ $emp->department->name ?? 'N/A' }}</td>
<td>{{ $emp->position->position_title ?? 'N/A' }}</td>
<td>{{ $emp->is_on_duty ? 'On Duty' : 'Off Duty' }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>


{{-- DEPARTMENTS --}}
<div id="departments" class="tab-section" style="display:none">

<h3>Departments</h3>

<table border="1" style="width:100%;border-collapse:collapse">
<thead>
<tr>
<th>ID</th>
<th>Department Code</th>
<th>Name</th>
</tr>
</thead>

<tbody>
@foreach($departments as $d)
<tr>
<td>{{ $d->id }}</td>
<td>{{ $d->department_id }}</td>
<td>{{ $d->name }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>


{{-- POSITIONS --}}
<div id="positions" class="tab-section" style="display:none">

<h3>Positions</h3>

<table border="1" style="width:100%;border-collapse:collapse">
<thead>
<tr>
<th>ID</th>
<th>Department</th>
<th>Position</th>
<th>Rank</th>
</tr>
</thead>

<tbody>
@foreach($positions as $p)
<tr>
<td>{{ $p->id }}</td>
<td>{{ $p->department->name ?? 'N/A' }}</td>
<td>{{ $p->position_title }}</td>
<td>{{ $p->rank_level }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>



<script>

// --- Tab Switching ---
function showTab(tab)
{
    document.querySelectorAll('.tab-section').forEach(e => {
        e.style.display = 'none';
    });
    document.getElementById(tab).style.display = 'block';
}

// Show tab based on URL hash on page load
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.substring(1); // Remove #
    if (hash && ['employees', 'departments', 'positions'].includes(hash)) {
        showTab(hash);
    } else {
        showTab('employees'); // Default to employees
    }
});

// --- Employee Filtering ---
const departmentFilter = document.getElementById('departmentFilter')
const employeeSearch = document.getElementById('employeeSearch')

if(departmentFilter) departmentFilter.addEventListener('change', filterEmployees)
if(employeeSearch) employeeSearch.addEventListener('keyup', filterEmployees)

function filterEmployees()
{
    let department = departmentFilter.value
    let search = employeeSearch.value.toLowerCase()

    document.querySelectorAll('#employeeTable tbody tr').forEach(row => {

        let rowDepartment = row.dataset.department
        let rowName = row.dataset.name
        let rowEmpID = row.dataset.empid

        // match if department matches AND (name OR ID contains search)
        let matchDepartment = !department || rowDepartment === department
        let matchSearch = !search || rowName.includes(search) || rowEmpID.includes(search)

        if(matchDepartment && matchSearch)
        {
            row.style.display = ''
        }
        else
        {
            row.style.display = 'none'
        }

    })
}

</script>

</div>
</div>

@endsection