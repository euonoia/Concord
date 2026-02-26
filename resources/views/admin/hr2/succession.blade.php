@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container" style="padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="font-weight: 700; color: #333; margin-bottom: 25px;">
        <i class="fas fa-seedling" style="color: #28a745; margin-right: 10px;"></i>Succession Planning
    </h2>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Strategic Candidate Assessment --}}
    <div style="background: #f0f7ff; padding: 30px; border-radius: 15px; border: 1px solid #cde4ff; margin-bottom: 40px;">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf
            <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: #0056b3;">
                <i class="fas fa-user-tie"></i> Strategic Candidate Assessment
            </h3>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                {{-- Department --}}
                <div>
                    <label style="font-weight: 600;">Department:</label>
                    <select id="dept_select" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Specialization --}}
                <div>
                    <label style="font-weight: 600;">Specialization:</label>
                    <select id="spec_select" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;">
                        <option value="">-- Choose Department First --</option>
                    </select>
                </div>

                {{-- Target Position --}}
                <div>
                    <label style="font-weight: 600;">Target Position:</label>
                    <select name="position_id" id="position_select" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="">-- Select Specialization First --</option>
                    </select>
                </div>
            </div>

            {{-- Employee & Readiness --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: 600;">Select Employee:</label>
                    <select name="employee_id" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight: 600;">Readiness Level:</label>
                    <select name="readiness" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency Only</option>
                    </select>
                </div>
            </div>

            {{-- Performance / Retention --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: 600;">Performance Score (1-10):</label>
                    <input type="number" name="perf_score" min="1" max="10" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Potential Score (1-10):</label>
                    <input type="number" name="pot_score" min="1" max="10" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Retention Risk:</label>
                    <select name="retention_risk" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="Low">Low Risk</option>
                        <option value="Medium" selected>Medium Risk</option>
                        <option value="High">High Risk</option>
                    </select>
                </div>
            </div>

            {{-- Date & Development Plan --}}
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div>
                    <label style="font-weight: 600;">Target Transition Date:</label>
                    <input type="date" name="effective_at" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Succession Development Focus:</label>
                    <input type="text" name="development_plan" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" placeholder="Identify specific training or mentorship required...">
                </div>
            </div>

            <button type="submit" style="margin-top: 25px; background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.3s;">
                <i class="fas fa-user-check"></i> Finalize Candidate Selection
            </button>
        </form>
    </div>

    {{-- Include Candidates Table --}}
    @include('admin.hr2.candidates')
</div>

{{-- JS for Dynamic Dropdowns --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptSelect = document.getElementById('dept_select');
    const specSelect = document.getElementById('spec_select');
    const positionSelect = document.getElementById('position_select');

    // Department -> Specialization
    deptSelect.addEventListener('change', function() {
        const deptCode = this.value;
        specSelect.innerHTML = '<option>Loading...</option>';
        positionSelect.innerHTML = '<option>-- Select Specialization First --</option>';

        if (!deptCode) {
            specSelect.innerHTML = '<option value="">-- Choose Department First --</option>';
            return;
        }

        fetch(`/admin/hr2/departments/${deptCode}/specializations`)
            .then(res => res.json())
            .then(data => {
                specSelect.innerHTML = '<option value="">-- Select Specialization --</option>';
                if (data.length === 0) {
                    specSelect.innerHTML = '<option value="">No specializations available</option>';
                } else {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.specialization_name;
                        opt.textContent = item.specialization_name;
                        specSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => console.error(err));
    });

    // Specialization -> Target Position
    specSelect.addEventListener('change', function() {
        const deptCode = deptSelect.value;
        const specialization = this.value;
        positionSelect.innerHTML = '<option>Loading...</option>';

        if (!specialization) {
            positionSelect.innerHTML = '<option value="">-- Select Specialization First --</option>';
            return;
        }

        fetch(`/admin/hr2/departments/${deptCode}/positions?specialization=${encodeURIComponent(specialization)}`)
            .then(res => res.json())
            .then(data => {
                positionSelect.innerHTML = '<option value="">-- Select Target Position --</option>';
                if (data.length === 0) {
                    positionSelect.innerHTML = '<option value="">No positions available</option>';
                } else {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.position_title;
                        positionSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => console.error(err));
    });
});
</script>
@endsection