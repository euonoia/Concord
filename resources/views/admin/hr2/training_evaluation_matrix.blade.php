@extends('admin.hr2.layouts.app')

@section('title', 'Training Evaluation Matrix')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">Training Performance Evaluation</h5>
                    <a href="{{ route('hr2.training') }}" class="btn btn-sm btn-secondary">Back to List</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4 bg-light p-3 rounded">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Employee Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                            <p class="mb-0"><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p class="mb-1"><strong>Competency:</strong> {{ $competency->name }}</p>
                            <p class="mb-0"><strong>Code:</strong> {{ $competency->competency_code }}</p>
                        </div>
                    </div>

                    <form id="evaluationForm">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                        <input type="hidden" name="competency_code" value="{{ $competency->competency_code }}">

                        <table class="table table-bordered">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th width="50%">Evaluation Criteria</th>
                                    <th width="30%">Weight/Rating (1-100)</th>
                                    <th width="20%">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Technical Skills & Application</strong></td>
                                    <td><input type="number" name="scores[technical]" class="form-control score-input" min="0" max="100" required></td>
                                    <td><input type="text" name="remarks[technical]" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><strong>Knowledge Retention</strong></td>
                                    <td><input type="number" name="scores[knowledge]" class="form-control score-input" min="0" max="100" required></td>
                                    <td><input type="text" name="remarks[knowledge]" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><strong>Behavior & Participation</strong></td>
                                    <td><input type="number" name="scores[participation]" class="form-control score-input" min="0" max="100" required></td>
                                    <td><input type="text" name="remarks[participation]" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td><strong>Attendance & Punctuality</strong></td>
                                    <td><input type="number" name="scores[attendance]" class="form-control score-input" min="0" max="100" required></td>
                                    <td><input type="text" name="remarks[attendance]" class="form-control"></td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th class="text-right">TOTAL SCORE:</th>
                                    <th class="text-center font-weight-bold text-primary h4" id="totalScoreDisplay">0</th>
                                    <th>/ 400</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5" id="btnSubmit">
                                <i class="fas fa-save mr-2"></i> Submit Evaluation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreInputs = document.querySelectorAll('.score-input');
    const totalDisplay = document.getElementById('totalScoreDisplay');
    const form = document.getElementById('evaluationForm');

    function calculateTotal() {
        let total = 0;
        scoreInputs.forEach(input => { total += parseInt(input.value) || 0; });
        totalDisplay.innerText = total;
    }

    scoreInputs.forEach(input => { input.addEventListener('input', calculateTotal); });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('btnSubmit');
        submitBtn.disabled = true;

        fetch("{{ route('hr2.training_evaluation.store') }}", {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Success! Score: ' + data.total_score);
                // FIXED: Redirects back to your working viewer route
                window.location.href = "{{ route('hr2.training') }}";
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
            }
        });
    });
});
</script>
@endsection