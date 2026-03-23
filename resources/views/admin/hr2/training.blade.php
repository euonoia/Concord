@extends('admin.hr2.layouts.app')
@section('title','HR2 Training')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary text-uppercase">Training Matrix</h4>
        </div>

        <div class="card-body">

            <!-- FILTERS -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="font-weight-bold text-dark">Department</label>
                    <select id="department" class="form-control border-left-primary shadow-sm">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="font-weight-bold text-dark">Specialization</label>
                    <select id="specialization" class="form-control border-left-primary shadow-sm">
                        <option value="">Select Specialization</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="font-weight-bold text-dark">Competency</label>
                    <select id="competency" class="form-control border-left-primary shadow-sm">
                        <option value="">Select Competency</option>
                    </select>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th>Employee ID</th>
                            <th>Full Name</th>
                            <th>Training Schedule (HR3)</th>
                            <th>Action / Status</th>
                        </tr>
                    </thead>

                    <tbody id="employeeTable">
                        <tr>
                            <td colspan="4" align="center" class="py-5 text-muted small italic">
                                <i class="fas fa-filter mr-2"></i>
                                Please select filters above to load eligible employees...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
const dept = document.getElementById('department');
const spec = document.getElementById('specialization');
const comp = document.getElementById('competency');
const table = document.getElementById('employeeTable');

// route to open evaluation matrix
const trainingEvaluationRoute = "{{ route('hr2.training.evaluate') }}";


/* ===========================
   LOAD SPECIALIZATIONS
=========================== */
dept.addEventListener('change', function() {
    let value = this.value;

    spec.innerHTML = '<option value="">Loading...</option>';
    comp.innerHTML = '<option value="">Select Competency</option>';

    if(!value) return;

    fetch(`/admin/hr2/get-specializations/${value}`)
    .then(res => res.json())
    .then(data => {
        spec.innerHTML = '<option value="">Select Specialization</option>';

        data.forEach(s => {
            spec.innerHTML += `<option value="${s.specialization_name}">${s.specialization_name}</option>`;
        });
    });
});


/* ===========================
   LOAD COMPETENCIES
=========================== */
spec.addEventListener('change', function() {
    let deptVal = dept.value;
    let specVal = this.value;

    comp.innerHTML = '<option value="">Loading...</option>';

    if(!specVal) return;

    fetch(`/admin/hr2/get-competencies/${deptVal}/${specVal}`)
    .then(res => res.json())
    .then(data => {

        comp.innerHTML = '<option value="">Select Competency</option>';

        data.forEach(c => {
            comp.innerHTML += `<option value="${c.competency_code}">${c.name}</option>`;
        });

    });
});


/* ===========================
   LOAD ELIGIBLE EMPLOYEES
=========================== */
comp.addEventListener('change', function() {

    let department = dept.value;
    let specialization = spec.value;
    let competency = this.value;

    if(!competency) return;

    table.innerHTML = `
        <tr>
            <td colspan="4" align="center" class="py-4">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Searching records...
            </td>
        </tr>`;

    fetch(`/admin/hr2/eligible-employees?department_id=${department}&specialization=${specialization}&competency_code=${competency}`)
    .then(res => res.json())
    .then(data => {

        table.innerHTML = '';

        if(data.length === 0){
            table.innerHTML = `
                <tr>
                    <td colspan="4" align="center" class="py-4">
                        No eligible employees found.
                    </td>
                </tr>`;
            return;
        }

        data.forEach(emp => {

            /* ===========================
               TRAINING SCHEDULE COLUMN
            =========================== */
            let trainingAtHTML = `<span class="text-muted small italic">Not Scheduled</span>`;

            if(emp.training_date){
                trainingAtHTML = `
                    <div class="small">
                        <i class="fas fa-calendar-alt text-primary"></i> ${emp.training_date}<br>
                        <i class="fas fa-clock text-muted"></i> ${emp.training_time}<br>
                        <i class="fas fa-map-marker-alt text-danger"></i> ${emp.venue || 'TBA'}
                    </div>`;
            }


            /* ===========================
               ACTION / STATUS LOGIC
            =========================== */
            let actionHTML = '';

            // 1. Already evaluated
            if(emp.training_score !== null && emp.training_score !== undefined){
                actionHTML = `
                    <div class="text-success small p-2 bg-light border rounded shadow-sm text-center">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Evaluated (${emp.training_score})</strong>
                    </div>`;
            }

            // 2. No training schedule yet
            else if(!emp.training_date){
                actionHTML = `
                    <div class="text-warning small p-2 bg-light border rounded shadow-sm text-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Training not scheduled (HR3)
                    </div>`;
            }

            // 3. Training scheduled → allow evaluation
            else{
                const finalUrl = `${trainingEvaluationRoute}?employee_id=${emp.employee_id}&competency_code=${competency}`;

                actionHTML = `
                    <div class="text-center">
                        <a href="${finalUrl}" class="btn btn-sm btn-primary px-4 shadow-sm font-weight-bold">
                            <i class="fas fa-clipboard-check mr-1"></i> EVALUATE
                        </a>
                    </div>`;
            }


            /* ===========================
               APPEND ROW
            =========================== */
            table.innerHTML += `
                <tr>
                    <td class="align-middle text-center">${emp.employee_id}</td>
                    <td class="align-middle font-weight-bold text-dark">${emp.first_name} ${emp.last_name}</td>
                    <td class="align-middle">${trainingAtHTML}</td>
                    <td class="align-middle" style="min-width: 200px;">${actionHTML}</td>
                </tr>`;
        });

    });

});
</script>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(78, 115, 223, 0.05);
}
.italic {
    font-style: italic;
}
</style>

@endsection