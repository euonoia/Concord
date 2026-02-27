@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container p-5 font-sans">
    <h2 class="font-bold text-gray-800 mb-6 text-2xl">
        <i class="fas fa-seedling text-green-600 mr-2"></i>Succession Planning
    </h2>

    <div class="bg-blue-50 p-8 rounded-lg border border-blue-200 mb-10 shadow-sm">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf

            <h3 class="text-blue-700 font-semibold mb-5 flex items-center gap-2">
                <i class="fas fa-user-tie"></i> Strategic Candidate Assessment
            </h3>

            {{-- Row 1: Dept, Spec, Target --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="font-medium text-gray-700">1. Select Department:</label>
                    <select id="dept_select" class="w-full p-2 border rounded mt-1 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium text-gray-700">2. Filter Position Specialty:</label>
                    <select id="spec_select" class="w-full p-2 border rounded mt-1 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">-- Select Department First --</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium text-gray-700">3. Target Position:</label>
                    <select name="position_id" id="position_select" class="w-full p-2 border rounded mt-1 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none" required>
                        <option value="">-- Select Specialization First --</option>
                    </select>
                </div>
            </div>

            {{-- Row 2: Employee Selection with Search --}}
            <div class="grid grid-cols-1 gap-5 mb-5">
                <div class="relative">
                    <label class="font-medium text-gray-700">4. Select Candidate (All Dept Employees):</label>
                    <input type="text" id="emp_search" placeholder="Search by name or ID..." 
                           class="w-full p-2 border rounded-t mt-1 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none text-sm">
                    
                    <select name="employee_id" id="employee_select" size="5" 
                            class="w-full p-2 border rounded-b border-t-0 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none" required>
                        <option value="">-- Select Department First --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1 italic">Format: [ID] Name (Specialization)</p>
                </div>
            </div>

            {{-- Readiness, Scores, Dates --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="font-medium text-gray-700">Readiness Level:</label>
                    <select name="readiness" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency Only</option>
                    </select>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Performance Score (1-10):</label>
                    <input type="number" name="perf_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Potential Score (1-10):</label>
                    <input type="number" name="pot_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="font-medium text-gray-700">Retention Risk:</label>
                    <select name="retention_risk" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Low">Low Risk</option>
                        <option value="Medium" selected>Medium Risk</option>
                        <option value="High">High Risk</option>
                    </select>
                </div>
                <div>
                    <label class="font-medium text-gray-700">Target Transition Date:</label>
                    <input type="date" name="effective_at" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
            </div>

            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded font-bold hover:bg-green-700 transition shadow-md">
                <i class="fas fa-user-check mr-2"></i>Finalize Candidate Selection
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');
    const positionSelect = document.getElementById('position_select');
    const employeeSelect = document.getElementById('employee_select');
    const empSearch = document.getElementById('emp_search');

    let allEmployees = []; // To store data for searching

    deptSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;
        if (!deptCode) return;

        // Reset
        specSelect.innerHTML = '<option>Loading...</option>';
        employeeSelect.innerHTML = '<option>Loading...</option>';

        // 1. Load Specializations for Position filtering
        fetch(`/admin/hr2/departments/${deptCode}/specializations`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">-- Select Specialization --</option>';
                data.forEach(s => specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`);
            });

        // 2. Load Employees for the Candidate Pool
        fetch(`/admin/hr2/departments/${deptCode}/employees`)
            .then(res => res.json())
            .then(data => {
                allEmployees = data;
                renderEmployees(data);
            });
    });

    // Helper to render employee list
    function renderEmployees(list) {
        if (list.length === 0) {
            employeeSelect.innerHTML = '<option value="">No matches found</option>';
            return;
        }
        employeeSelect.innerHTML = '';
        list.forEach(e => {
            const specText = e.specialization ? `(${e.specialization})` : '(No Specialization)';
            const option = document.createElement('option');
            option.value = e.employee_id;
            option.textContent = `[ID: ${e.employee_id}] ${e.first_name} ${e.last_name} ${specText}`;
            option.className = "p-1 border-b border-gray-100";
            employeeSelect.appendChild(option);
        });
    }

    // Search Logic
    empSearch.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = allEmployees.filter(emp => 
            emp.first_name.toLowerCase().includes(term) || 
            emp.last_name.toLowerCase().includes(term) || 
            emp.employee_id.toString().includes(term) ||
            (emp.specialization && emp.specialization.toLowerCase().includes(term))
        );
        renderEmployees(filtered);
    });

    // Load Positions when Spec is selected
    specSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;
        const specialization = specSelect.value;
        if (!specialization) return;

        fetch(`/admin/hr2/departments/${deptCode}/positions?specialization=${encodeURIComponent(specialization)}`)
            .then(res => res.json())
            .then(data => {
                positionSelect.innerHTML = '<option value="">-- Select Target Position --</option>';
                data.forEach(p => positionSelect.innerHTML += `<option value="${p.id}">${p.position_title} (Rank: ${p.rank_level})</option>`);
            });
    });
});
</script>
@endsection