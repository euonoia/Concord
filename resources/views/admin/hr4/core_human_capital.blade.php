@extends('admin.hr4.layouts.app')

@section('title','Core Human Capital')

@section('content')

<h2>Core Human Capital</h2>

<div style="margin-bottom:20px; display:flex; gap:10px; align-items:center;">
    <button onclick="showTab('employees')">Employees</button>
    <button onclick="showTab('departments')">Departments</button>
    <button onclick="showTab('positions')">Positions</button>
    <button onclick="showTab('available')">Available</button>
    <button onclick="showTab('summary')">Department Summary</button>
    <div style="margin-left:auto; font-size:0.9rem; color:#444">Quick search applies to visible table</div>
</div>

<style>
    /* Improved table styling for HR pages */
    .hr-table { width:100%; border-collapse:collapse; font-size:0.95rem; }
    .hr-table th, .hr-table td { padding:8px 10px; border:1px solid #e6e6e6; }
    .hr-table thead th { background:#f7f7fb; cursor:pointer; user-select:none; }
    .hr-table tbody tr:nth-child(odd) { background:#fff; }
    .hr-table tbody tr:nth-child(even) { background:#fbfbfd; }
    .hr-search { margin:8px 0 12px 0; display:flex; gap:8px; align-items:center }
    .hr-search input { padding:6px 10px; border:1px solid #ddd; border-radius:6px; min-width:220px }
    .badge { display:inline-block; padding:2px 8px; border-radius:999px; background:#eef2ff; color:#1e40af; font-weight:600; font-size:0.85rem }
    .sortable:after { content:'\25B2\25BC'; font-size:0.6rem; margin-left:6px; color:#aaa }
</style>

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

<div class="hr-search">
    <label class="badge">Employees</label>
    <input type="text" id="employeeGlobalSearch" placeholder="Search employees by name, id, dept or position">
</div>

<table class="hr-table" id="employeeTable">

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

<div class="hr-search">
    <label class="badge">Departments</label>
    <input type="text" id="departmentSearch" placeholder="Filter departments by name or code">
</div>

<table class="hr-table" id="departmentTable">
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

<div class="hr-search">
    <label class="badge">Positions</label>
    <input type="text" id="positionSearch" placeholder="Filter positions by department or title">
</div>

<table class="hr-table" id="positionTable">
<thead>
<tr>
<th>ID</th>
<th>Department</th>
<th>Position</th>
<th>Rank</th>
<th>Assigned</th>
<th>Max</th>
<th>Available</th>
</tr>
</thead>

<tbody>
@php $seenPos = []; @endphp
@foreach($positions as $p)
    @if(in_array($p->id, $seenPos))
        @continue
    @endif
    @php $seenPos[] = $p->id; @endphp
    <tr data-id="pos-{{ $p->id }}">
        <td>{{ $p->id }}</td>
        <td>{{ $p->department->name ?? 'N/A' }}</td>
        <td>{{ $p->position_title }}</td>
        <td>{{ $p->rank_level }}</td>
        <td>{{ $p->assigned_count ?? \App\Models\Employee::where('position_id', $p->id)->count() }}</td>
        <td>{{ $p->max_slots ?? 1 }}</td>
        <td>{{ $p->available_slots ?? max(0, ($p->max_slots ?? 1) - \App\Models\Employee::where('position_id', $p->id)->count()) }}</td>
    </tr>
@endforeach
</tbody>
</table>

</div>


{{-- AVAILABLE (Specializations / Vacant) --}}
<div id="available" class="tab-section" style="display:none">

<h3>Available Specializations</h3>

<table border="1" style="width:100%;border-collapse:collapse">
<thead>
<tr>
<th>Department</th>
<th>Available Specializations</th>
</tr>
</thead>
<tbody>
@foreach($departments as $d)
<tr>
<td>{{ $d->name }}</td>
<td>
    @php $specs = $availableSpecializations[$d->id] ?? collect(); @endphp
    @if($specs->isEmpty())
        <em>None</em>
    @else
        <ul style="margin:0;padding-left:18px;">
        @foreach($specs as $s)
            <li>{{ $s->specialization_name }}</li>
        @endforeach
        </ul>
    @endif
</td>
</tr>
@endforeach
</tbody>
</table>

<h3 style="margin-top:20px;">Vacant Positions</h3>

<table border="1" style="width:100%;border-collapse:collapse">
<thead>
<tr>
<th>ID</th>
<th>Department</th>
<th>Position</th>
<th>Rank</th>
<th>Assigned</th>
<th>Max</th>
<th>Available</th>
</tr>
</thead>
<tbody id="vacantPositionsTbody">
@php $seenVac = []; @endphp
@foreach($vacantPositions as $p)
    @if(in_array($p->id, $seenVac))
        @continue
    @endif
    @php $seenVac[] = $p->id; @endphp
    <tr data-id="pos-{{ $p->id }}">
        <td>{{ $p->id }}</td>
        <td>{{ $p->department->name ?? 'N/A' }}</td>
        <td>{{ $p->position_title }}</td>
        <td>{{ $p->rank_level }}</td>
        <td>{{ $p->assigned_count ?? \App\Models\Employee::where('position_id', $p->id)->count() }}</td>
        <td>{{ $p->max_slots ?? 1 }}</td>
        <td>{{ $p->available_slots ?? max(0, ($p->max_slots ?? 1) - \App\Models\Employee::where('position_id', $p->id)->count()) }}</td>
    </tr>
@endforeach
</tbody>
</table>

</div>


{{-- DEPARTMENT SUMMARY --}}
<div id="summary" class="tab-section" style="display:none">

<h3>Department Slot Summary</h3>

<div class="hr-search">
    <label class="badge">Summary</label>
    <input type="text" id="summarySearch" placeholder="Filter by department name">
</div>

<table class="hr-table" id="summaryTable">
<thead>
<tr>
    <th>Department</th>
    <th>Assigned</th>
    <th>Max Slots</th>
    <th>Available</th>
    <th>Open Specializations</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($departmentSummary as $dept)
<tr>
    <td>{{ $dept->department_name }}</td>
    <td>{{ $dept->assigned }}</td>
    <td>{{ $dept->max }}</td>
    <td>
        <strong style="color: {{ $dept->available > 0 ? 'green' : 'red' }}">
            {{ $dept->available }}
        </strong>
    </td>
    <td>{{ $dept->available_specializations }}</td>
    <td>
        @if($dept->available > 0)
            <span class="badge" style="background:#dcfce7; color:#166534">Open</span>
        @else
            <span class="badge" style="background:#fee2e2; color:#991b1b">Full</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- Tab Switching ---
    function showTab(tab)
    {
        document.querySelectorAll('.tab-section').forEach(e => {
            e.style.display = 'none';
        });
        const el = document.getElementById(tab);
        if (el) el.style.display = 'block';
    }

    // expose globally for onclick buttons
    window.showTab = showTab;

    // --- Employee Filtering ---
    const departmentFilter = document.getElementById('departmentFilter');
    const employeeSearch = document.getElementById('employeeSearch');

    if(departmentFilter) departmentFilter.addEventListener('change', filterEmployees);
    if(employeeSearch) employeeSearch.addEventListener('keyup', filterEmployees);

    function filterEmployees()
    {
        let department = departmentFilter ? departmentFilter.value : '';
        let search = employeeSearch ? employeeSearch.value.toLowerCase() : '';

        document.querySelectorAll('#employeeTable tbody tr').forEach(row => {
            let rowDepartment = row.dataset.department || '';
            let rowName = row.dataset.name || '';
            let rowEmpID = row.dataset.empid || '';

            let matchDepartment = !department || rowDepartment === department;
            let matchSearch = !search || rowName.includes(search) || rowEmpID.includes(search);

            if(matchDepartment && matchSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Open tab from URL hash (e.g., #available)
    if (location.hash) {
        var tab = location.hash.replace('#','');
        if (document.getElementById(tab)) {
            showTab(tab);
            window.scrollTo(0, 0);
        }
    }

});
</script>

<script>
// Small interactive helpers: search + simple sorting for hr tables
function simpleSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function () {
        const q = input.value.toLowerCase();
        document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
}

function makeSortable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    table.querySelectorAll('thead th').forEach((th, idx) => {
        th.classList.add('sortable');
        th.addEventListener('click', () => {
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
            const asc = !th.dataset.asc || th.dataset.asc === '0';
            rows.sort((a, b) => {
                const aText = a.children[idx].textContent.trim();
                const bText = b.children[idx].textContent.trim();
                const aNum = parseFloat(aText.replace(/[^0-9.-]+/g, ''));
                const bNum = parseFloat(bText.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return asc ? aNum - bNum : bNum - aNum;
                }
                return asc ? aText.localeCompare(bText) : bText.localeCompare(aText);
            });
            rows.forEach(r => tbody.appendChild(r));
            table.querySelectorAll('thead th').forEach(h => h.dataset.asc = '0');
            th.dataset.asc = asc ? '1' : '0';
        });
    });
}

// Remove duplicate rows by data-id within a table
function dedupeTable(tableId) {
    const seen = new Set();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
        const id = row.dataset.id;
        if (!id) return;
        if (seen.has(id)) row.remove(); else seen.add(id);
    });
}

// initialize interactions
simpleSearch('employeeGlobalSearch', 'employeeTable');
simpleSearch('departmentSearch', 'departmentTable');
simpleSearch('positionSearch', 'positionTable');
simpleSearch('employeeGlobalSearch', 'positionTable');
simpleSearch('summarySearch', 'summaryTable');

makeSortable('employeeTable');
makeSortable('departmentTable');
makeSortable('positionTable');
makeSortable('summaryTable');

dedupeTable('positionTable');
dedupeTable('vacantPositionsTbody');

</script>

@endsection