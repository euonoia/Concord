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
                    <div class="row mb-4 bg-light p-3 rounded border-left-primary">
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
                                    <th width="40%">Evaluation Criteria</th>
                                    <th width="40%">Rating Score</th>
                                    <th width="20%">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $criteria = [
                                        'technical' => 'Technical Skills & Application',
                                        'knowledge' => 'Knowledge Retention',
                                        'participation' => 'Behavior & Participation',
                                        'attendance' => 'Attendance & Punctuality'
                                    ];
                                @endphp

                                @foreach($criteria as $key => $label)
                                <tr>
                                    <td><strong>{{ $label }}</strong></td>
                                    <td>
                                        <div class="d-flex justify-content-around align-items-center rating-group">
                                            <label class="mb-0 mr-2"><input type="radio" name="scores[{{ $key }}]" value="25" class="score-check" required> 25</label>
                                            <label class="mb-0 mr-2"><input type="radio" name="scores[{{ $key }}]" value="50" class="score-check"> 50</label>
                                            <label class="mb-0 mr-2"><input type="radio" name="scores[{{ $key }}]" value="75" class="score-check"> 75</label>
                                            <label class="mb-0"><input type="radio" name="scores[{{ $key }}]" value="100" class="score-check"> 100</label>
                                        </div>
                                    </td>
                                    <td><input type="text" name="remarks[{{ $key }}]" class="form-control form-control-sm" placeholder="Optional notes"></td>
                                </tr>
                                @endforeach
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
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm" id="btnSubmit">
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
    const scoreRadios = document.querySelectorAll('.score-check');
    const totalDisplay = document.getElementById('totalScoreDisplay');
    const form = document.getElementById('evaluationForm');

    // Calculate total based on selected radio buttons
    function calculateTotal() {
        let total = 0;
        // Get all unique names of radio groups
        const groups = new Set();
        scoreRadios.forEach(r => groups.add(r.name));
        
        // Sum values of checked radios
        groups.forEach(name => {
            const checked = document.querySelector(`input[name="${name}"]:checked`);
            if (checked) {
                total += parseInt(checked.value);
            }
        });
        
        totalDisplay.innerText = total;
    }

    // Listen for changes on any radio button
    scoreRadios.forEach(radio => {
        radio.addEventListener('change', calculateTotal);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('btnSubmit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

        fetch("{{ route('hr2.training_evaluation.store') }}", {
            method: 'POST',
            body: new FormData(this),
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json' 
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Success! Total Score: ' + data.total_score);
                window.location.href = "{{ route('hr2.training') }}";
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Submit Evaluation';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Something went wrong.');
            submitBtn.disabled = false;
        });
    });
});
</script>

<style>
    .rating-group label {
        cursor: pointer;
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: all 0.2s;
        background: #fff;
    }
    .rating-group label:hover {
        background: #f8f9fc;
        border-color: #4e73df;
    }
    /* Simple styling to make selected radio visible if needed */
    input[type="radio"] {
        transform: scale(1.2);
        margin-right: 5px;
    }
</style>
@endsection