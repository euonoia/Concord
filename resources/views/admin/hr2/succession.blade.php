@extends('admin.layouts.app')

@section('title', 'Succession Management')

@section('content')
<div class="container p-5 font-sans">
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-gray-800 text-2xl">
            <i class="fas fa-seedling text-green-600 mr-2"></i>Succession Planning
        </h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Section 1: Nomination Form --}}
    <div class="bg-blue-50 p-8 rounded-lg border border-blue-200 mb-10 shadow-sm">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf
            <h3 class="text-blue-700 font-semibold mb-5 flex items-center gap-2">
                <i class="fas fa-user-tie"></i> Strategic Candidate Assessment
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="font-medium text-gray-700">1. Select Department:</label>
                    <select id="dept_select" class="w-full p-2 border rounded mt-1 border-blue-200 outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium text-gray-700">2. Filter Specialty:</label>
                    <select id="spec_select" class="w-full p-2 border rounded mt-1 border-blue-200 outline-none">
                        <option value="">-- Select Department First --</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium text-gray-700">3. Target Position:</label>
                    <select name="position_id" id="position_select" class="w-full p-2 border rounded mt-1 border-blue-200 outline-none" required>
                        <option value="">-- Select Specialization First --</option>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="font-medium text-gray-700">4. Select Candidate (All Dept Employees):</label>
                <input type="text" id="emp_search" placeholder="Search by name or ID..." class="w-full p-2 border rounded-t mt-1 border-blue-200 outline-none text-sm">
                <select name="employee_id" id="employee_select" size="4" class="w-full p-2 border rounded-b border-t-0 border-blue-200 outline-none" required>
                    <option value="">-- Select Department First --</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="font-medium text-gray-700">Readiness:</label>
                    <select name="readiness" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Perf Score (1-10):</label>
                    <input type="number" name="perf_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Pot Score (1-10):</label>
                    <input type="number" name="pot_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Transition Date:</label>
                    <input type="date" name="effective_at" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="font-medium text-gray-700">Retention Risk:</label>
                <select name="retention_risk" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                    <option value="Low">Low Risk</option>
                    <option value="Medium">Medium Risk</option>
                    <option value="High">High Risk</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded font-bold hover:bg-blue-700 transition">
                <i class="fas fa-plus-circle mr-2"></i>Add to Succession Pipeline
            </button>
        </form>
    </div>

    {{-- Section 2: Active Pipeline Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700">Active Succession Pipeline</h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <th class="p-4">Candidate</th>
                    <th class="p-4">Target Role</th>
                    <th class="p-4">Readiness</th>
                    <th class="p-4 text-center">Scores (Perf/Pot)</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $c)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">{{ $c->employee->first_name }} {{ $c->employee->last_name }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $c->employee_id }}</div>
                    </td>
                    <td class="p-4">
                        <div class="text-blue-700 font-semibold">{{ $c->position->position_title }}</div>
                        <div class="text-xs text-gray-500">{{ $c->department_id }}</div>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold 
                            {{ $c->readiness == 'Ready Now' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $c->readiness }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="font-bold text-gray-800">{{ $c->performance_score }}</span> / 
                        <span class="font-bold text-blue-600">{{ $c->potential_score }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            {{-- PROMOTE BUTTON --}}
                            <form action="{{ route('succession.candidate.promote', $c->id) }}" method="POST" onsubmit="return confirm('Promote this employee? This will update their official position ID and specialization.')">
                                @csrf
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">
                                    <i class="fas fa-check-double mr-1"></i>Grant Role
                                </button>
                            </form>

                            {{-- DELETE BUTTON --}}
                            <form action="{{ route('succession.candidate.destroy', $c->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 px-2 py-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400">No active candidates in the pipeline.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
// (Previous JS logic remains the same for AJAX fetching)
document.addEventListener('DOMContentLoaded', () => {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');
    const positionSelect = document.getElementById('position_select');
    const employeeSelect = document.getElementById('employee_select');
    const empSearch = document.getElementById('emp_search');

    let allEmployees = []; 

    deptSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;
        if (!deptCode) return;

        specSelect.innerHTML = '<option>Loading...</option>';
        fetch(`/admin/hr2/departments/${deptCode}/specializations`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">-- Select Specialization --</option>';
                data.forEach(s => specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`);
            });

        fetch(`/admin/hr2/departments/${deptCode}/employees`)
            .then(res => res.json())
            .then(data => {
                allEmployees = data;
                renderEmployees(data);
            });
    });

    specSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;
        const specialization = specSelect.value;
        fetch(`/admin/hr2/departments/${deptCode}/positions?specialization=${encodeURIComponent(specialization)}`)
            .then(res => res.json())
            .then(data => {
                positionSelect.innerHTML = '<option value="">-- Select Target Position --</option>';
                data.forEach(p => positionSelect.innerHTML += `<option value="${p.id}">${p.position_title}</option>`);
            });
    });

    function renderEmployees(list) {
        employeeSelect.innerHTML = list.length ? '' : '<option value="">No employees found</option>';
        list.forEach(e => {
            const option = document.createElement('option');
            option.value = e.employee_id;
            option.textContent = `[ID: ${e.employee_id}] ${e.first_name} ${e.last_name} (${e.specialization || 'General'})`;
            employeeSelect.appendChild(option);
        });
    }

    empSearch.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = allEmployees.filter(emp => 
            emp.first_name.toLowerCase().includes(term) || 
            emp.last_name.toLowerCase().includes(term) || 
            emp.employee_id.toString().includes(term)
        );
        renderEmployees(filtered);
    });
});
</script>
@endsection