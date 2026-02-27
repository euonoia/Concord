@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container p-5 font-sans">
    <h2 class="font-bold text-gray-800 mb-6">
        <i class="fas fa-seedling text-green-600 mr-2"></i>Succession Planning
    </h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-5 rounded border-l-4 border-green-600 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-blue-50 p-8 rounded-lg border border-blue-200 mb-10">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf

            <h3 class="text-blue-700 font-semibold mb-5">
                <i class="fas fa-user-tie"></i> Strategic Candidate Assessment
            </h3>

            {{-- Department, Specialization, Target Position --}}
            <div class="grid grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="font-medium">Department:</label>
                    <select id="dept_select" class="w-full p-2 border rounded mt-1 border-blue-200">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-medium">Specialization:</label>
                    <select id="spec_select" class="w-full p-2 border rounded mt-1 border-blue-200">
                        <option value="">-- Select Department First --</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Target Position:</label>
                    <select name="position_id" id="position_select" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="">-- Select Specialization First --</option>
                    </select>
                </div>
            </div>

            {{-- Employee & Readiness --}}
            <div class="grid grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="font-medium">Select Employee:</label>
                    <select name="employee_id" id="employee_select" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="">-- Select Department First --</option>
                    </select>
                </div>

                <div>
                    <label class="font-medium">Readiness Level:</label>
                    <select name="readiness" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency Only</option>
                    </select>
                </div>
            </div>

            {{-- Performance / Retention --}}
            <div class="grid grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="font-medium">Performance Score (1-10):</label>
                    <input type="number" name="perf_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div>
                    <label class="font-medium">Potential Score (1-10):</label>
                    <input type="number" name="pot_score" min="1" max="10" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div>
                    <label class="font-medium">Retention Risk:</label>
                    <select name="retention_risk" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Low">Low Risk</option>
                        <option value="Medium" selected>Medium Risk</option>
                        <option value="High">High Risk</option>
                    </select>
                </div>
            </div>

            {{-- Date & Development Plan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="font-medium">Target Transition Date:</label>
                    <input type="date" name="effective_at" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
                <div class="md:col-span-2">
                    <label class="font-medium">Succession Development Focus:</label>
                    <input type="text" name="development_plan" class="w-full p-2 border rounded mt-1 border-blue-200" placeholder="Identify training or mentorship required...">
                </div>
            </div>

            <button type="submit" class="mt-5 bg-green-600 text-white px-6 py-2 rounded font-bold hover:bg-green-700 transition">
                <i class="fas fa-user-check"></i> Finalize Candidate Selection
            </button>
        </form>
    </div>
</div>

{{-- JS for dynamic dropdowns --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');
    const positionSelect = document.getElementById('position_select');
    const employeeSelect = document.getElementById('employee_select');

    deptSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;

        specSelect.innerHTML = '<option value="">-- Select Department First --</option>';
        positionSelect.innerHTML = '<option value="">-- Select Specialization First --</option>';
        employeeSelect.innerHTML = '<option value="">-- Select Specialization First --</option>';

        if (!deptCode) return;

        fetch(`/admin/hr2/departments/${deptCode}/specializations`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">-- Select Specialization --</option>';
                data.forEach(s => {
                    specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`;
                });
            });
    });

    specSelect.addEventListener('change', () => {
        const deptCode = deptSelect.value;
        const specialization = specSelect.value;

        positionSelect.innerHTML = '<option>Loading...</option>';
        employeeSelect.innerHTML = '<option value="">-- Select Specialization First --</option>';

        if (!specialization) {
            positionSelect.innerHTML = '<option value="">-- Select Specialization First --</option>';
            return;
        }

        fetch(`/admin/hr2/departments/${deptCode}/positions?specialization=${encodeURIComponent(specialization)}`)
            .then(res => res.json())
            .then(data => {
                positionSelect.innerHTML = '<option value="">-- Select Target Position --</option>';
                data.forEach(p => {
                    positionSelect.innerHTML += `<option value="${p.id}">${p.position_title} (Rank: ${p.rank_level})</option>`;
                });
            });

        fetch(`/admin/hr2/departments/${deptCode}/employees?specialization=${encodeURIComponent(specialization)}`)
            .then(res => res.json())
            .then(data => {
                employeeSelect.innerHTML = '<option value="">-- Select Employee --</option>';
                data.forEach(e => {
                    employeeSelect.innerHTML += `<option value="${e.employee_id}">${e.first_name} ${e.last_name}</option>`;
                });
            });
    });
});
</script>
@endsection