@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Applicant Details</h2>
        <a href="{{ route('hr1.applicants.index') }}" class="btn btn-outline-secondary">
            Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        {{-- Left Column: Information --}}
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover mb-0">
                        <tr><th width="30%">Full Name</th><td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td></tr>
                        <tr><th>Email</th><td>{{ $applicant->email }}</td></tr>
                        <tr><th>Phone</th><td>{{ $applicant->phone }}</td></tr>
                        <tr><th>Department</th><td>{{ $applicant->department_name }}</td></tr>
                        <tr><th>Specialization</th><td>{{ $applicant->specialization ?? 'N/A' }}</td></tr>
                        <tr><th>Status</th><td>
                            <span class="badge {{ $applicant->application_status == 'rejected' ? 'bg-danger' : 'bg-success' }}">
                                {{ ucfirst(str_replace('_',' ',$applicant->application_status)) }}
                            </span>
                        </td></tr>
                    </table>
                </div>
            </div>

            {{-- Interview Schedule Details --}}
            @if($applicant->schedule_date)
            <div class="card mb-4 shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Interview Schedule Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="30%">Date & Time</th>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($applicant->schedule_date)->format('F d, Y') }}</strong> 
                                at {{ \Carbon\Carbon::parse($applicant->schedule_time)->format('h:i A') }}
                            </td>
                        </tr>
                        <tr>
                            <th>Location</th>
                            <td>{{ $applicant->interview_location ?? 'No location set' }}</td>
                        </tr>
                        <tr>
                            <th>Validated By</th>
                            <td>{{ $applicant->validator_first }} {{ $applicant->validator_last }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Actions --}}
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Manage Application</h5>
                </div>
                <div class="card-body">
                    @php
                        // Combine date and time into one Carbon object
                        $interviewDateTime = $applicant->schedule_date 
                            ? \Carbon\Carbon::parse($applicant->schedule_date . ' ' . $applicant->schedule_time) 
                            : null;
                        
                        // Check if the interview time has passed
                        $isInterviewFinished = $interviewDateTime && $interviewDateTime->isPast();
                    @endphp

                    <form action="{{ route('hr1.applicants.updateStatus', $applicant->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small">Application Status</label>
                            <select name="application_status" class="form-select" required>
                                @foreach(['pending','under_review','interview','rejected'] as $status)
                                    <option value="{{ $status }}" {{ $applicant->application_status == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_',' ',$status)) }}
                                    </option>
                                @endforeach

                                {{-- Only show 'Accepted' if the interview has happened --}}
                                @if($isInterviewFinished)
                                    <option value="accepted" {{ $applicant->application_status == 'accepted' ? 'selected' : '' }}>
                                        Accepted (Post-Interview)
                                    </option>
                                @else
                                    <option value="" disabled>--- Accept Locked ---</option>
                                @endif
                            </select>

                            @if(!$isInterviewFinished && $applicant->schedule_date)
                                <div class="form-text text-danger mt-2">
                                    <small><i class="fas fa-lock"></i> "Accepted" status is locked until the interview date/time has passed.</small>
                                </div>
                            @elseif(!$applicant->schedule_date)
                                <div class="form-text text-warning mt-2">
                                    <small>No interview scheduled yet.</small>
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>

            {{-- CV Section --}}
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white text-center">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <div class="card-body text-center">
                    @if($applicant->resume_path)
                        <a href="{{ route('hr1.applicants.download', $applicant->id) }}" class="btn btn-outline-primary w-100" target="_blank">
                            View CV
                        </a>
                    @else
                        <span class="text-muted">No resume uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection