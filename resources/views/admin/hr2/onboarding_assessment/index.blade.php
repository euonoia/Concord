@extends('admin.hr2.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow border-0 mb-4">
                <div class="card-body text-center p-4">

                    <h4 class="fw-bold mb-3">Applicant Assessment</h4>
                    <p class="text-muted small">Enter Reference ID (e.g. APP-OUG9ABP6)</p>

                    {{-- Display error message --}}
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Display info message --}}
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <form method="POST" action="{{ route('onboarding.assessment.check') }}">
                        @csrf
                        <input type="text" name="reference_id" class="form-control mb-3"
                               placeholder="Reference ID" required>
                        <button class="btn btn-primary w-100">Continue</button>
                    </form>

                </div>
            </div>

            {{-- Validated Ratings Table --}}
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Validated Assessments</h5>

                    @if($validatedAssessments->isEmpty())
                        <p class="text-muted">No validated assessments yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Application ID</th>
                                        <th>Name</th>
                                        <th>Final Score</th>
                                        <th>Competency Level</th>
                                        <th>Validated By</th>
                                        <th>Validated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($validatedAssessments as $index => $assessment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $assessment->application_id }}</td>
                                            <td>{{ $assessment->first_name }} {{ $assessment->last_name }}</td>
                                            <td>{{ $assessment->rating }}</td>
                                            <td>{{ $assessment->competency }}</td>
                                            <td>{{ $assessment->validator_name ?? '-' }}</td>
                                            <td>{{ $assessment->updated_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection