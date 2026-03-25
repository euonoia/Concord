@extends('admin.hr2.layouts.app')
@section('title','HR2 Training')

@section('content')
<div class="container-fluid">

    <!-- ===============================
         TRAINING MATRIX (MAIN TABLE)
    ================================ -->
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

            <!-- MAIN TABLE -->
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



    <!-- ===============================
         NEW TABLE: HR1 VALIDATED RESULTS
    ================================ -->
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="m-0 font-weight-bold text-uppercase">Validated Training Performance (HR1)</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-success text-white text-center">
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Weighted Average</th>
                            <th>Status</th>
                            <th>Validated By</th>
                            <th>Date Evaluated</th>
                        </tr>
                    </thead>

                    <tbody id="validatedHR1Table">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Loading validated employees...
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
const validatedTable = document.getElementById('validatedHR1Table');

const trainingEvaluationRoute = "{{ route('hr2.training.evaluate') }}";


/* ===========================
   LOAD SPECIALIZATIONS
=========================== */
dept.addEventListener('change', function() {

    spec.innerHTML = '<option value="">Loading...</option>';
    comp.innerHTML = '<option value="">Select Competency</option>';

    if(!this.value) return;

    fetch(`/admin/hr2/get-specializations/${this.value}`)
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

    fetch(`/admin/hr2/get-competencies/${dept.value}/${this.value}`)
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

            let actionHTML = '';

            if(emp.training_score !== null){
                actionHTML = `<span class="text-success font-weight-bold">Evaluated (${emp.training_score})</span>`;
            }
            else if(!emp.training_date){
                actionHTML = `<span class="text-warning">Training not scheduled (HR3)</span>`;
            }
            else{
                actionHTML = `
                    <a href="${trainingEvaluationRoute}?employee_id=${emp.employee_id}&competency_code=${competency}"
                       class="btn btn-sm btn-primary">Evaluate</a>`;
            }

            table.innerHTML += `
                <tr>
                    <td class="text-center">${emp.employee_id}</td>
                    <td class="font-weight-bold">${emp.first_name} ${emp.last_name}</td>
                    <td>${emp.training_date ?? 'Not Scheduled'}</td>
                    <td class="text-center">${actionHTML}</td>
                </tr>`;
        });

    });

});


/* ===========================
   LOAD HR1 VALIDATED TABLE
=========================== */
function loadValidatedHR1(){

    fetch('/admin/hr2/validated-employees')
    .then(res => res.json())
    .then(data => {

        validatedTable.innerHTML = '';

        if(data.length === 0){
            validatedTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">No validated employees found.</td>
                </tr>`;
            return;
        }

        data.forEach(emp => {

            validatedTable.innerHTML += `
                <tr>
                    <td class="text-center">${emp.employee_id}</td>
                    <td class="font-weight-bold">${emp.first_name} ${emp.last_name}</td>
                    <td class="text-center text-success font-weight-bold">${emp.weighted_average}</td>
                    <td class="text-center">${emp.status}</td>
                    <td class="text-center">${emp.eval_fname ?? ''} ${emp.eval_lname ?? ''}</td>
                    <td class="text-center">${emp.evaluated_at}</td>
                </tr>`;
        });

    });

}

loadValidatedHR1();
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