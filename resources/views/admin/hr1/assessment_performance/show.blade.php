@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <a href="{{ route('hr1.assessment.performance.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('hr1.assessment.performance.index') }}">Assessment Performance</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Applicant Profile</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="inline-block p-1 rounded-circle border mb-3 mx-auto" style="width: 100px; height: 100px;">
                             <i class="bi bi-person-bounding-box text-gray-300" style="font-size: 80px; line-height: 1;"></i>
                        </div>
                        <h4 class="mb-0">{{ $applicant->first_name }} {{ $applicant->last_name }}</h4>
                        <span class="badge badge-light border text-muted">{{ $applicant->application_id }}</span>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Specialization:</span>
                            <span class="font-weight-bold">{{ $applicant->specialization ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Department:</span>
                            <span class="font-weight-bold">{{ $applicant->department_id ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Contact:</span>
                            <span class="font-weight-bold">{{ $applicant->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            <span class="badge badge-pill badge-info px-2">{{ ucfirst($applicant->application_status) }}</span>
                        </li>
                    </ul>

                    <hr>
                    
                    <div class="text-center p-3 rounded bg-light">
                        <small class="text-uppercase text-muted d-block mb-1">Weighted Average</small>
                        <h2 class="mb-0 {{ $average >= 75 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($average, 2) }}%
                        </h2>
                        <small class="{{ $average >= 75 ? 'text-success' : 'text-danger' }}">
                            {{ $average >= 75 ? 'Passed' : 'Failed' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Competency Breakdown</h6>
                    
                    @if($applicant->is_validated == 1)
                        <span class="btn btn-success btn-sm disabled shadow-sm">
                            <i class="bi bi-check-circle-fill"></i> Validated
                        </span>
                    @else
                        <form action="{{ route('hr1.assessment.performance.validate', $applicant->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm" onclick="return confirm('Validate this assessment score?')">
                                <i class="bi bi-shield-check"></i> Validate Assessment
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Competency Name</th>
                                    <th class="text-center">Rating</th>
                                    <th>Remarks</th>
                                    <th>Assessed By</th>
                                    <th>Validated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scores as $score)
                                <tr>
                                    <td class="text-uppercase font-weight-bold text-dark small">
                                        {{ str_replace('_', ' ', $score->competency) }}
                                    </td>
                                    <td class="text-center">
                                        <div class="progress mb-1" style="height: 10px; min-width: 100px;">
                                            <div class="progress-bar {{ $score->rating >= 75 ? 'bg-success' : 'bg-warning' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $score->rating }}%"></div>
                                        </div>
                                        <span class="font-weight-bold {{ $score->rating >= 75 ? 'text-success' : 'text-warning' }}">
                                            {{ $score->rating }}%
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted italic">{{ $score->remarks ?? 'No remarks provided.' }}</small>
                                    </td>
                                    <td>
                                        <small class="font-weight-bold text-dark">{{ $score->assessor_name ?? $score->assessed_by ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small class="font-weight-bold text-dark">{{ $score->validator_name ?? $score->validated_by ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No specific competency ratings available.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection