@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onboarding Assessment Performance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.hr1.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Performance Management</li>
                <li class="breadcrumb-item active" aria-current="page">Assessment Performance</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center px-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Assessment Master Records
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $applicants->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clipboard-data fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
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
                                            <div class="font-weight-bold text-dark">{{ $applicant->first_name }} {{ $applicant->last_name }}</div>
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
                                        $resultClass = match($applicant->assessment_status) {
                                            'passed' => 'text-success font-weight-bold',
                                            'failed' => 'text-danger font-weight-bold',
                                            'assessed' => 'text-info font-weight-bold',
                                            default  => 'text-muted italic'
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
                    Showing entries {{ $applicants->firstItem() ?? 0 }} to {{ $applicants->lastItem() ?? 0 }} of {{ $applicants->total() }}
                </div>
                <div>
                    {{ $applicants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection