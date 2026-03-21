@extends('admin.hr2.layouts.app')
@section('title', 'Onboarding Assessments')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Onboarding Assessments</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
            <h6 class="m-0 font-weight-bold text-white">New Hire Assessments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Applicant ID</th>
                            <th>Name</th>
                            <th>Position / Spec</th>
                            <th>Assessment Status</th>
                            <th>Interview Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessments as $item)
                        <tr>
                            <td>{{ $item->applicant_id }}</td>
                            <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                            <td>{{ $item->specialization }}</td>
                            <td>
                                @if($item->assessment_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->assessment_status == 'scheduled')
                                    <span class="badge badge-info">Scheduled</span>
                                @elseif($item->assessment_status == 'passed')
                                    <span class="badge badge-success">Passed</span>
                                @else
                                    <span class="badge badge-danger">Failed</span>
                                @endif
                            </td>
                            <td>{{ $item->interview_date ? $item->interview_date->format('M d, Y') : 'Not Set' }}</td>
                            <td>
                                <a href="{{ route('admin.hr2.onboarding_assessments.show', $item->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Evaluate
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No onboarding assessments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $assessments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
