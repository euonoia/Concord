@extends('admin.layouts.app')

@section('title','Core Human Capital')

@section('content')

<h2>Core Human Capital</h2>

<div style="margin-bottom:20px; display:flex; gap:10px;">
    <button onclick="showTab('employees')">Employees</button>
    <button onclick="showTab('departments')">Departments</button>
    <button onclick="showTab('positions')">Positions</button>
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
@foreach($employees as $e)
<tr
    data-department="{{ $e->position->department->id ?? '' }}"
    data-name="{{ strtolower($e->first_name.' '.$e->last_name) }}"
    data-empid="{{ strtolower($e->employee_id) }}"
>
<td>{{ $e->id }}</td>
<td>{{ $e->first_name }} {{ $e->last_name }}</td>
<td>{{ $e->employee_id }}</td>
<td>{{ $e->position->department->name ?? 'N/A' }}</td>
<td>{{ $e->position->position_title ?? 'N/A' }}</td>
<td>{{ $e->is_on_duty ? 'On Duty' : 'Off Duty' }}</td>
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

@endsection