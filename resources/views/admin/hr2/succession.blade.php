@extends('admin.hr2.layouts.app')

@section('title', 'Succession Planning')

@section('content')
<div class="container p-5 font-sans">
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-2xl text-gray-800">
            <i class="fas fa-seedling text-green-600 mr-2"></i>Succession Planning
        </h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Assessment Form --}}
    <div class="bg-blue-50 p-8 rounded-lg border border-blue-200 mb-10 shadow-sm">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf
            <h3 class="text-blue-700 font-semibold mb-5 flex items-center gap-2">
                <i class="fas fa-user-tie"></i> Candidate Nomination
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Department</label>
                    <select id="dept_select" class="w-full p-2 border rounded mt-1 border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Specialty Filter</label>
                    <select id="spec_select" class="w-full p-2 border rounded mt-1 border-blue-200 outline-none">
                        <option value="">-- Select Dept First --</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Target Position</label>
                    <select name="position_id" id="position_select" class="w-full p-2 border rounded mt-1 border-blue-200 outline-none" required>
                        <option value="">-- Select Specialty First --</option>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="text-xs font-bold text-gray-600 uppercase">Choose Candidate (Displays HR1 Grade & Specialization)</label>
                <input type="text" id="emp_search" placeholder="Quick search by name or ID..." class="w-full p-2 border rounded-t mt-1 text-sm border-blue-200 outline-none">
                <select name="employee_id" id="employee_select" size="5" class="w-full p-2 border rounded-b border-t-0 bg-white border-blue-200 outline-none" required>
                    <option value="">-- Select Department to Load Employees --</option>
                </select>
                
                {{-- Dynamic Grade Preview --}}
                <div id="grade_preview" class="hidden mt-2 p-3 bg-white border border-blue-100 rounded text-sm shadow-inner flex items-center justify-between">
                    <div>
                        <i class="fas fa-chart-line text-blue-500 mr-1"></i> Validated Grade: <strong id="prev_grade" class="text-blue-700">--</strong>
                    </div>
                    <div>
                        <i class="fas fa-user-check text-gray-500 mr-1"></i> Validator: <strong id="prev_evaluator" class="text-gray-700">--</strong>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Readiness</label>
                    <select name="readiness" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Retention Risk</label>
                    <select name="retention_risk" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                        <option value="Low">Low Risk</option>
                        <option value="Medium">Medium Risk</option>
                        <option value="High">High Risk</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">Effective Date</label>
                    <input type="date" name="effective_at" class="w-full p-2 border rounded mt-1 border-blue-200" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="text-xs font-bold text-gray-600 uppercase">Development Plan / Growth Strategy</label>
                <textarea name="development_plan" rows="3" class="w-full p-3 border rounded mt-1 text-sm border-blue-200 focus:ring-2 focus:ring-blue-400 outline-none" placeholder="Describe the steps needed for this candidate to be fully prepared..."></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-md">
                Nominate Candidate
            </button>
        </form>
    </div>

    {{-- Active Pipeline Table --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 p-4 border-b">
            <h3 class="font-bold text-gray-700 text-sm uppercase tracking-wider">Active Succession Pipeline</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="p-4">Candidate & Development Plan</th>
                    <th class="p-4">Target Role</th>
                    <th class="p-4">Readiness</th>
                    <th class="p-4 text-center">Grade</th>
                    <th class="p-4">Validator</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($candidates as $c)
                <tr class="border-b hover:bg-gray-50 align-top transition">
                    <td class="p-4">
                        <div class="font-bold text-gray-900">{{ $c->employee->first_name }} {{ $c->employee->last_name }}</div>
                        <div class="text-xs text-gray-400 mb-2">ID: {{ $c->employee_id }} | {{ $c->employee->specialization ?? 'General' }}</div>
                        @if($c->development_plan)
                            <div class="p-2 bg-yellow-50 border border-yellow-100 rounded text-xs italic text-gray-600">
                                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i> "{{ $c->development_plan }}"
                            </div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="text-blue-700 font-semibold">{{ $c->position->position_title }}</div>
                        <div class="text-xs text-gray-400">{{ $c->specialization }}</div>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $c->readiness == 'Ready Now' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $c->readiness }}
                        </span>
                    </td>
                    <td class="p-4 text-center font-bold text-lg {{ $c->training_grade >= 75 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($c->training_grade, 2) }}%
                    </td>
                    <td class="p-4">
                        <div class="font-medium text-gray-800">{{ $c->eval_fname }} {{ $c->eval_lname }}</div>
                        <div class="text-xs text-gray-400 italic">HR1 Operations</div>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <form action="{{ route('succession.candidate.destroy', $c->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition p-1" title="Remove Nomination">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400 italic">No candidates currently in the pipeline.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- PROMOTED SUCCESSOR CANDIDATES --}}
<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden mt-10">
    <div class="bg-green-50 p-4 border-b">
        <h3 class="font-bold text-green-700 text-sm uppercase tracking-wider">
            Promoted Successor Candidates
        </h3>
    </div>

    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($promotedCandidates as $p)
            <div class="border rounded-lg p-4 bg-green-50 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $p->first_name }} {{ $p->last_name }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $p->employee_id }}</div>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">{{ \Carbon\Carbon::parse($p->promoted_at)->format('M d, Y') }}</span>
                </div>

                <div class="mb-2 text-sm text-gray-700">
                    <div><strong>From:</strong> {{ $p->old_position ?? 'N/A' }}</div>
                    <div><strong>To:</strong> {{ $p->new_position ?? 'N/A' }}</div>
                </div>

                <div class="mb-2 text-xs text-gray-600">
                    <strong>Specialization:</strong> {{ $p->old_specialization ?? 'N/A' }} <span class="text-gray-400">→</span> {{ $p->new_specialization ?? 'N/A' }}
                </div>

                <div class="text-xs text-gray-500">
                    Promoted by: <strong>{{ $p->promoter_first_name ?? 'Unknown' }} {{ $p->promoter_last_name ?? '' }}</strong>
                </div>
            </div>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 p-10 text-center text-gray-400 italic">No promoted successor candidates yet.</div>
        @endforelse
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');
    const posSelect = document.getElementById('position_select');
    const empSelect = document.getElementById('employee_select');
    const empSearch = document.getElementById('emp_search');
    const gradePrev = document.getElementById('grade_preview');
    const pGrade = document.getElementById('prev_grade');
    const pEval = document.getElementById('prev_evaluator');

    let empData = [];

    // 1. Department Change: Fetch Specs and Employees
    deptSelect.addEventListener('change', () => {
        const deptId = deptSelect.value;
        if(!deptId) return;

        // Fetch Specializations for the dropdown
        fetch(`/admin/hr2/departments/${deptId}/specializations`)
            .then(r => r.json())
            .then(d => {
                specSelect.innerHTML = '<option value="">-- All Specializations --</option>';
                d.forEach(s => specSelect.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`);
            });

        // Fetch Employees (Controller returns weighted_average and evaluator name)
        fetch(`/admin/hr2/departments/${deptId}/employees`)
            .then(r => r.json())
            .then(d => {
                empData = d;
                renderEmps(d);
            });
    });

    // 2. Specialty Change: Filter Positions
    specSelect.addEventListener('change', () => {
        const deptId = deptSelect.value;
        const specName = specSelect.value;
        fetch(`/admin/hr2/departments/${deptId}/positions?specialization=${encodeURIComponent(specName)}`)
            .then(r => r.json())
            .then(d => {
                posSelect.innerHTML = '<option value="">-- Select Target Position --</option>';
                d.forEach(p => posSelect.innerHTML += `<option value="${p.id}">${p.position_title}</option>`);
            });
    });

    // 3. Employee Selection: Show detailed preview
    empSelect.addEventListener('change', () => {
        const emp = empData.find(e => e.employee_id == empSelect.value);
        if(emp) {
            gradePrev.classList.remove('hidden');
            pGrade.textContent = emp.weighted_average ? parseFloat(emp.weighted_average).toFixed(2) + '%' : 'No Grade';
            pEval.textContent = emp.evaluator_name || 'Not Evaluated';
            
            // Highlight color based on passing grade (75%)
            pGrade.className = (parseFloat(emp.weighted_average) >= 75) ? 'text-green-600' : 'text-red-600';
        }
    });

    /**
     * Renders the employee list into the select box
     * Formats: [Grade] Name - Specialization
     */
    function renderEmps(list) {
        empSelect.innerHTML = list.length ? '' : '<option value="">No employees found for this department.</option>';
        list.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.employee_id;
            
            const grade = e.weighted_average ? parseFloat(e.weighted_average).toFixed(1) + '%' : 'N/A';
            const spec = e.specialization ? e.specialization : 'General';
            
            // Format: [95.0%] John Doe - Cardiology
            opt.textContent = `[${grade}] ${e.first_name} ${e.last_name} — (${spec})`;
            empSelect.appendChild(opt);
        });
    }

    // 4. Search Filter
    empSearch.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = empData.filter(en => 
            en.first_name.toLowerCase().includes(term) || 
            en.last_name.toLowerCase().includes(term) ||
            en.employee_id.toString().includes(term) ||
            (en.specialization && en.specialization.toLowerCase().includes(term))
        );
        renderEmps(filtered);
    });
});
</script>
@endsection