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
    <a href="#userlogs" onclick="showTab('userlogs'); return false;" class="tab-link">User Logs</a>
    <a href="#availablejobs" onclick="showTab('availablejobs'); return false;" class="tab-link">Available Jobs</a>
</div>

{{-- EMPLOYEES --}}
<div id="employees" class="tab-section">

<h3>Employees</h3>

<div style="margin-bottom:15px; display:flex; gap:10px;">

    {{-- Department Filter --}}
    <select id="departmentFilter">
        <option value="">All Departments</option>
        @foreach($departments as $d)
            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
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



{{-- USER LOGS --}}
<div id="userlogs" class="tab-section" style="display:none">

<h3>User Logs</h3>

<table border="1" style="width:100%;border-collapse:collapse">
<thead>
<tr>
<th>Username</th>
<th>Email</th>
<th>User Type</th>
<th>Role Slug</th>
</tr>
</thead>

<tbody>
@foreach($users as $user)
<tr>
<td>{{ $user->username }}</td>
<td>{{ $user->email }}</td>
<td>{{ $user->user_type }}</td>
<td>{{ $user->role_slug }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>



{{-- AVAILABLE JOBS --}}
<div id="availablejobs" class="tab-section" style="display:none">

<h3>Available Jobs</h3>

<div style="margin-bottom:15px; display:flex; gap:10px;">
    <a href="{{ route('hr4.job_postings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center">
        <i class="bi bi-plus-circle mr-2"></i>
        Add New Job
    </a>
</div>

@if($jobPostings->count() > 0)
    <table border="1" style="width:100%;border-collapse:collapse">
    <thead>
    <tr>
    <th>Job Title</th>
    <th>Department</th>
    <th>Status</th>
    <th>Added By</th>
    <th>Added At</th>
    <th>Actions</th>
    </tr>
    </thead>

    <tbody>
    @foreach($jobPostings as $posting)
    <tr>
    <td>{{ $posting->title }}</td>
    <td>{{ $posting->department_name ?? $posting->department }}</td>
    <td>
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $posting->status == 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ ucfirst($posting->status) }}
        </span>
    </td>
    <td>{{ $posting->poster->username ?? 'Unknown' }}</td>
    <td>{{ $posting->posted_at->format('M d, Y') }}</td>
    <td>
        <a href="{{ route('hr4.job_postings.show', $posting) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
            <i class="bi bi-eye"></i> View
        </a>
        <a href="{{ route('hr4.job_postings.edit', $posting) }}" class="text-blue-600 hover:text-blue-900 mr-3">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <form method="POST" action="{{ route('hr4.job_postings.destroy', $posting) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this job posting?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-orange-600 hover:text-orange-900">
                <i class="bi bi-archive"></i> Archive
            </button>
        </form>
    </td>
    </tr>
    @endforeach
    </tbody>
    </table>
@else
    <div class="text-center py-12">
        <i class="bi bi-briefcase text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Available Jobs</h3>
        <p class="text-gray-500 mb-4">Get started by adding your first available job.</p>
        <a href="{{ route('hr4.job_postings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
            Add New Available Job
        </a>
    </div>
@endif

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
    if (hash && ['employees', 'departments', 'positions', 'userlogs', 'availablejobs'].includes(hash)) {
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