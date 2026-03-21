@extends('admin.hr2.layouts.app')
@section('title', 'Evaluate Onboarding')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Applicant Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Full Name:</strong> {{ $assessment->first_name }} {{ $assessment->last_name }}</p>
                    <p><strong>Email:</strong> {{ $assessment->email }}</p>
                    <p><strong>Phone:</strong> {{ $assessment->phone ?? 'N/A' }}</p>
                    <p><strong>Specialization:</strong> {{ $assessment->specialization }}</p>
                    <p><strong>Applied At:</strong> {{ $assessment->applied_at ? $assessment->applied_at->format('M d, Y') : 'N/A' }}</p>
                    <hr>
                    @if($assessment->resume_path)
                        <a href="{{ Storage::url($assessment->resume_path) }}" class="btn btn-info btn-sm" target="_blank">
                             <i class="fas fa-file-pdf"></i> View Resume
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Assessment Evaluation</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hr2.onboarding_assessments.update', $assessment->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Assessment Status</label>
                            <select name="assessment_status" class="form-control" required>
                                <option value="pending" {{ $assessment->assessment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="scheduled" {{ $assessment->assessment_status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="passed" {{ $assessment->assessment_status == 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ $assessment->assessment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Interview Date</label>
                            <input type="datetime-local" name="interview_date" class="form-control" 
                                value="{{ $assessment->interview_date ? $assessment->interview_date->format('Y-m-d\TH:i') : '' }}">
                        </div>

                        <div class="form-group">
                            <label>Interviewer</label>
                            <input type="text" name="interviewer" class="form-control" value="{{ $assessment->interviewer }}">
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="4">{{ $assessment->remarks }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block mt-4">
                            <i class="fas fa-save mr-2"></i> Save Evaluation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
