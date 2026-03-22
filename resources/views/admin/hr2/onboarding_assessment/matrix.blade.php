@extends('admin.hr2.layouts.app')

@section('content')
<div class="container py-5">

    <div class="card shadow border-0">
        <div class="card-body">

            <h4 class="fw-bold mb-3">Assessment Matrix</h4>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="mb-3"><strong>Name:</strong> {{ $applicant->first_name }} {{ $applicant->last_name }}</div>
            <div class="mb-3"><strong>Department:</strong> {{ $applicant->department_id }}</div>
            <div class="mb-3"><strong>Specialization:</strong> {{ $applicant->specialization }}</div>

            <hr>

            <form method="POST" action="{{ route('onboarding.assessment.submit', $applicant->id) }}">
                @csrf

                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>Competency</th>
                            <th>Rating (20–100)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($competencies as $competency)
                        <tr>
                            <td>{{ $competency }}</td>
                            <td>
                                <div class="d-flex justify-content-between flex-wrap">
                                    @for($i=20; $i<=100; $i+=20)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="ratings[{{ $competency }}]"
                                                   id="rating_{{ $competency }}_{{ $i }}"
                                                   value="{{ $i }}" required>
                                            <label class="form-check-label" for="rating_{{ $competency }}_{{ $i }}">
                                                {{ $i }}
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <input type="text" name="remarks[{{ $competency }}]" class="form-control">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary w-100 mt-3">Submit Assessment</button>
            </form>

        </div>
    </div>

</div>
@endsection