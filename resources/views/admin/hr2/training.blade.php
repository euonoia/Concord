@extends('admin.hr2.layouts.app')

@section('title','HR2 Training')

@section('content')

<div class="container">

<h2>Training Eligibility Viewer</h2>

<br>

<div style="display:flex;gap:20px;">

<select id="department">
    <option value="">Select Department</option>
    @foreach($departments as $dept)
        <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
    @endforeach
</select>

<select id="specialization">
    <option value="">Select Specialization</option>
</select>

<select id="competency">
    <option value="">Select Competency</option>
</select>

</div>

<br>

<table border="1" width="100%" style="border-collapse:collapse">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Completed At</th>
            <th>Training Evaluation</th>
        </tr>
    </thead>
    <tbody id="employeeTable">
        <tr>
            <td colspan="4" align="center">Select department, specialization, and competency</td>
        </tr>
    </tbody>
</table>

</div>

<script>
const dept = document.getElementById('department');
const spec = document.getElementById('specialization');
const comp = document.getElementById('competency');
const table = document.getElementById('employeeTable');

// Named route for evaluation page
const trainingEvaluationRoute = "{{ route('hr2.training_evaluation.show') }}";

dept.addEventListener('change', function() {
    let value = this.value;
    spec.innerHTML = '<option>Loading...</option>';

    fetch(`/admin/hr2/get-specializations/${value}`)
    .then(res => res.json())
    .then(data => {
        spec.innerHTML = '<option value="">Select Specialization</option>';
        data.forEach(s => {
            spec.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`;
        });
    });
});

spec.addEventListener('change', function() {
    let deptVal = dept.value;
    let specVal = this.value;
    comp.innerHTML = '<option>Loading...</option>';

    fetch(`/admin/hr2/get-competencies/${deptVal}/${specVal}`)
    .then(res => res.json())
    .then(data => {
        comp.innerHTML = '<option value="">Select Competency</option>';
        data.forEach(c => {
            comp.innerHTML += `<option value="${c.competency_code}">${c.name}</option>`;
        });
    });
});

comp.addEventListener('change', function() {
    let department = dept.value;
    let specialization = spec.value;
    let competency = this.value;

    fetch(`/admin/hr2/eligible-employees?department_id=${department}&specialization=${specialization}&competency_code=${competency}`)
    .then(res => res.json())
    .then(data => {
        table.innerHTML = '';

        if(data.length === 0){
            table.innerHTML = `<tr><td colspan="4" align="center">No employees found</td></tr>`;
            return;
        }

        data.forEach(emp => {
            let evaluationHTML = '-';
            
            if(emp.training_score){
                evaluationHTML = `<span class="badge badge-success">Already Evaluated</span>`;
            } else {
                const finalUrl = `${trainingEvaluationRoute}?employee_id=${emp.employee_id}&competency_code=${competency}`;
                
                evaluationHTML = `<a href="${finalUrl}" class="btn btn-sm btn-primary">Evaluate</a>`;
            }

            table.innerHTML += `
                <tr>
                    <td>${emp.employee_id}</td>
                    <td>${emp.first_name} ${emp.last_name}</td>
                    <td>${emp.completed_at ?? '-'}</td>
                    <td>${evaluationHTML}</td>
                </tr>
            `;
        });
    });
});
</script>

@endsection