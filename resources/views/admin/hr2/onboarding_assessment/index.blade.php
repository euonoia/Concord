@extends('admin.hr2.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-3">
        {{-- Left Sidebar: Quick Action Search --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 text-uppercase small text-muted">Assessment Portal</h6>
                    
                    <form method="POST" action="{{ route('onboarding.assessment.check') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Reference ID</label>
                            <input type="text" name="reference_id" class="form-control form-control-sm"
                                   placeholder="e.g. APP-OUG9..." required>
                        </div>
                        <button class="btn btn-primary btn-sm w-100 shadow-sm">Process Assessment</button>
                    </form>

                    <hr class="my-3">

                    {{-- Session Alerts Integrated into Sidebar --}}
                    @if(session('error'))
                        <div class="alert alert-danger py-2 small mb-0">{{ session('error') }}</div>
                    @endif
                    @if(session('info') || session('success'))
                        <div class="alert alert-success py-2 small mb-0">{{ session('info') ?? session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Side: Data Tabs --}}
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-3">
                    <ul class="nav nav-pills card-header-pills small" id="assessmentTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                                <i class="bi bi-clock-history me-1"></i> Waiting for Assessment
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="validated-tab" data-bs-toggle="tab" data-bs-target="#validated" type="button">
                                <i class="bi bi-check-circle me-1"></i> Validated Results
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content" id="assessmentTabsContent">
                    {{-- Tab 1: Applicants Waiting --}}
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        @if($assessments->isEmpty())
                            <div class="text-center py-4"><p class="text-muted mb-0">No pending applicants.</p></div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.875rem;">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>Application ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th class="text-end">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assessments as $applicant)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $applicant->application_id }}</td>
                                                <td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td>
                                                <td>{{ $applicant->department_id }}</td>
                                                <td>
                                                    <span class="badge rounded-pill {{ $applicant->assessment_status == 'assessed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                        {{ ucfirst($applicant->assessment_status ?? 'Pending') }}
                                                    </span>
                                                </td>
                                                <td class="text-end text-muted">{{ \Carbon\Carbon::parse($applicant->applied_at)->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $assessments->links() }}</div>
                        @endif
                    </div>

                    {{-- Tab 2: Validated Assessments --}}
                    <div class="tab-pane fade" id="validated" role="tabpanel">
                        @if($validatedAssessments->isEmpty())
                            <div class="text-center py-4"><p class="text-muted mb-0">No records found.</p></div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.875rem;">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Score</th>
                                            <th>Level</th>
                                            <th class="text-end">Validated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($validatedAssessments as $assessment)
                                            <tr>
                                                <td class="fw-bold text-primary">{{ $assessment->application_id }}</td>
                                                <td>{{ $assessment->first_name }} {{ $assessment->last_name }}</td>
                                                <td class="fw-bold">{{ $assessment->rating }}</td>
                                                <td><span class="badge border text-dark">{{ $assessment->competency }}</span></td>
                                                <td class="text-end text-muted">{{ \Carbon\Carbon::parse($assessment->updated_at)->format('M d, Y') }}</td>
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
</div>
@endsection