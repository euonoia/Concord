@extends('admin.hr4.layouts.app')

@section('title','Core Human Capital')

@section('content')

<div class="flex gap-4">
<div class="w-3/4">

<h2>Core Human Capital</h2>

{{-- Process Hired Users Button --}}
<form method="POST" action="{{ route('hr4.core.process_hired') }}" style="display:inline; margin-left:20px;">
    @csrf
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
        Process New Hires
    </button>
</form>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
        <i class="bi bi-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Needed Positions Recommendation Section (moved to top, visible by default) -->

<div style="margin-bottom:20px; display:flex; gap:10px;">
    <a href="#employees" onclick="window.location.hash='employees'; return false;" class="tab-link">Employees</a>
    <a href="#departments" onclick="window.location.hash='departments'; return false;" class="tab-link">Departments</a>
    <a href="#positions" onclick="window.location.hash='positions'; return false;" class="tab-link">Positions</a>
    <a href="#neededpositions" onclick="window.location.hash='neededpositions'; return false;" class="tab-link">Needed Positions</a>
    <a href="#userlogs" onclick="window.location.hash='userlogs'; return false;" class="tab-link">User Logs</a>
    <a href="#availablejobs" onclick="window.location.hash='availablejobs'; return false;" class="tab-link">Available Jobs ({{ $availableJobsCount }})</a>
</div>

{{-- NEEDED POSITIONS RECOMMENDATION (now in its own tab, hidden by default) --}}
<div id="neededpositions" class="tab-section" style="display:none; margin-bottom: 30px;">
    <h2 style="margin-top: 30px; color:#1d4ed8;">Needed Positions Recommendation</h2>

    {{-- Summary Section --}}
    <div style="background:#f8fafc; padding:15px; border-radius:8px; margin-bottom:20px; border:1px solid #e2e8f0;">
        <h3 style="margin:0 0 10px 0; color:#1e293b; font-size:16px;">Summary</h3>
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            @php
                $totalNeeded = array_sum(array_column($needed_positions, 'needed'));
                $departmentsWithNeeds = array_filter($needed_positions, fn($np) => $np['needed'] > 0);
                $deptSummary = [];
                foreach ($departmentsWithNeeds as $np) {
                    $dept = $np['department'];
                    if (!isset($deptSummary[$dept])) $deptSummary[$dept] = 0;
                    $deptSummary[$dept] += $np['needed'];
                }
            @endphp
            <div style="background:white; padding:10px; border-radius:6px; border:1px solid #cbd5e1; min-width:120px;">
                <strong style="color:#dc2626;">{{ $totalNeeded }}</strong><br>
                <small style="color:#64748b;">Total Needed</small>
            </div>
            @foreach($deptSummary as $dept => $count)
                <div style="background:white; padding:10px; border-radius:6px; border:1px solid #cbd5e1; min-width:120px;">
                    <strong style="color:#2563eb;">{{ $count }}</strong><br>
                    <small style="color:#64748b;">{{ $dept }}</small>
                </div>
            @endforeach
        </div>
    </div>

    <p style="margin-bottom:15px; color:#374151; font-size:15px;">
        <strong>Note:</strong> This table shows all positions and departments for monitoring. Use the filter to view by department.
    </p>
    <div style="margin-bottom: 15px;">
        <label for="neededDeptFilter" style="font-weight:500; margin-right:8px;">Filter by Department:</label>
        <select id="neededDeptFilter" style="padding:5px 10px; border-radius:4px; border:1px solid #d1d5db;">
            <option value="">All Departments</option>
            @foreach(array_unique(array_map(fn($np) => $np['department'], $needed_positions)) as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
            @endforeach
        </select>
    </div>
    <table border="1" style="width:100%;border-collapse:collapse; margin-bottom: 20px;" id="neededPositionsTable">
        <thead>
            <tr>
                <th>Department</th>
                <th>Position</th>
                <th>Required</th>
                <th>Current</th>
                <th style="color:#d97706">Needed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($needed_positions as $np)
                <tr data-department="{{ $np['department'] }}" style="background:{{ $np['needed'] > 0 ? '#fffbe6' : '#f3f4f6' }}">
                    <td>{{ $np['department'] }}</td>
                    <td>{{ $np['position'] }}</td>
                    <td style="text-align:center">{{ $np['required'] }}</td>
                    <td style="text-align:center">{{ $np['current'] }}</td>
                    <td style="text-align:center; color:#d97706; font-weight:bold;">
                        {{ $np['needed'] }}
                    </td>
                </tr>
            @endforeach
            @if(count($needed_positions) === 0)
                <tr><td colspan="5" style="text-align:center; color:#10b981;">No positions found.</td></tr>
            @endif
        </tbody>
    </table>
</div>

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



{{-- EMPLOYEES --}}
<div id="employees" class="tab-section" style="display:none">

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




{{-- NEEDED POSITIONS RECOMMENDATION --}}
<div id="neededpositions" class="tab-section" style="display:block; margin-bottom: 30px;">
    <h2 style="margin-top: 30px; color:#1d4ed8;">Needed Positions Recommendation</h2>
    <p style="margin-bottom:15px; color:#374151; font-size:15px;">
        <strong>Note:</strong> This table shows only the positions and departments that are lacking staff. Use this as your basis before posting new available jobs to HR1. No employee details are shown here—focus is on what is needed only.
    </p>
    <table border="1" style="width:100%;border-collapse:collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th>Department</th>
                <th>Position</th>
                <th>Required</th>
                <th>Current</th>
                <th style="color:#d97706">Needed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($needed_positions as $np)
                @if($np['needed'] > 0)
                <tr style="background:#fffbe6">
                    <td>{{ $np['department'] }}</td>
                    <td>{{ $np['position'] }}</td>
                    <td style="text-align:center">{{ $np['required'] }}</td>
                    <td style="text-align:center">{{ $np['current'] }}</td>
                    <td style="text-align:center; color:#d97706; font-weight:bold;">
                        {{ $np['needed'] }}
                    </td>
                </tr>
                @endif
            @endforeach
            @if(!collect($needed_positions)->where('needed','>',0)->count())
                <tr><td colspan="5" style="text-align:center; color:#10b981;">All positions are sufficiently filled.</td></tr>
            @endif
        </tbody>
    </table>
</div>
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
    <th>Positions Available</th>
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
    <td class="text-center font-semibold">{{ $posting->positions_available }}</td>
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
function handleTabFromHash() {
    const hash = window.location.hash.substring(1); // Remove #
    if (hash && ['employees', 'departments', 'positions', 'neededpositions', 'userlogs', 'availablejobs'].includes(hash)) {
        showTab(hash);
    } else {
        showTab('employees'); // Default to employees
    }
}

document.addEventListener('DOMContentLoaded', handleTabFromHash);
window.addEventListener('hashchange', function() {
    // Small delay to ensure DOM is ready
    setTimeout(handleTabFromHash, 10);
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
        // ...existing code...
    });
}

// --- Needed Positions Filtering ---
const neededDeptFilter = document.getElementById('neededDeptFilter');
if (neededDeptFilter) {
    neededDeptFilter.addEventListener('change', function() {
        let dept = this.value;
        document.querySelectorAll('#neededPositionsTable tbody tr').forEach(row => {
            if (!dept || row.dataset.department === dept) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}
// (Stray code removed)


</script>

</div>
</div>

@endsection