@extends('admin.hr1.layouts.app')

@section('content')
    <style>
        .metric-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: none;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 150px;
            height: 150px;
            margin-right: 20px;
            margin-bottom: 20px;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .metric-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #8a90a0;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.2;
        }
    </style>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-gray-800">Onboarding Assessment Performance</h1>
        </div>

        <div
            style="display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; margin-bottom: 25px !important; gap: 20px !important;">
            <div class="metric-card" style="border-top: 5px solid #0d6efd; margin: 0 !important;">
                <div class="metric-icon bg-primary bg-opacity-10 text-primary"
                    style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Records
                </div>
                <div class="metric-value" style="font-size: 1.5rem;">{{ $applicants->total() }}</div>
            </div>

            <div class="metric-card" style="border-top: 5px solid #198754; margin: 0 !important;">
                <div class="metric-icon bg-success bg-opacity-10 text-success"
                    style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Passed
                </div>
                <div class="metric-value" style="font-size: 1.5rem;">{{ $totalPassed }}</div>
            </div>

            <div class="metric-card" style="border-top: 5px solid #dc3545; margin: 0 !important;">
                <div class="metric-icon bg-danger bg-opacity-10 text-danger"
                    style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Failed
                </div>
                <div class="metric-value" style="font-size: 1.5rem;">{{ $totalFailed }}</div>
            </div>

            <div class="metric-card" style="border-top: 5px solid #ffc107; margin: 0 !important;">
                <div class="metric-icon bg-warning bg-opacity-10 text-warning"
                    style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">
                    Total<br>Assessed</div>
                <div class="metric-value" style="font-size: 1.5rem;">{{ $totalAssessed }}</div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">HR1 Assessment Tracking</h6>
                <div class="d-flex gap-2">
                    <span class="badge badge-light border text-muted px-3 py-2">
                        <i class="bi bi-info-circle mr-1"></i> Source: Onboarding Assessments
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="border-0">Application ID</th>
                                <th class="border-0">Applicant Name</th>
                                <th class="border-0">Specialization</th>
                                <th class="border-0">Validation Status</th>
                                <th class="border-0">Result</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applicants as $applicant)
                                <tr>
                                    <td class="font-weight-bold text-primary small">
                                        {{ $applicant->application_id }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-2 bg-primary text-white text-center rounded-circle shadow-sm"
                                                style="width: 35px; height: 35px; line-height: 35px; font-size: 12px;">
                                                {{ strtoupper(substr($applicant->first_name, 0, 1) . substr($applicant->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $applicant->first_name }}
                                                    {{ $applicant->last_name }}
                                                </div>
                                                <div class="small text-muted">{{ $applicant->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $applicant->specialization ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($applicant->is_validated)
                                            <span class="badge badge-success px-3">
                                                <i class="bi bi-check-circle"></i> Validated
                                            </span>
                                        @else
                                            <span class="badge badge-warning px-3 text-dark">
                                                <i class="bi bi-clock"></i> Pending Review
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $resultClass = match ($applicant->assessment_status) {
                                                'passed' => 'text-success font-weight-bold',
                                                'failed' => 'text-danger font-weight-bold',
                                                'assessed' => 'text-info font-weight-bold',
                                                default => 'text-muted italic'
                                            };
                                        @endphp
                                        <span class="{{ $resultClass }}">
                                            {{ strtoupper($applicant->assessment_status ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hr1.assessment.performance.show', $applicant->id) }}"
                                            class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 text-primary font-weight-bold">
                                            <i class="bi bi-file-earmark-person mr-1"></i> View Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-folder2-open fa-3x mb-3 d-block"></i>
                                            <p>No assessment records found in the database.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="small text-muted font-italic">
                        Showing entries {{ $applicants->firstItem() ?? 0 }} to {{ $applicants->lastItem() ?? 0 }} of
                        {{ $applicants->total() }}
                    </div>
                    <div>
                        {{ $applicants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection