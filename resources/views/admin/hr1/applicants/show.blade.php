@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2>Applicant Details</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Full Name</th><td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td></tr>
                <tr><th>Email</th><td>{{ $applicant->email }}</td></tr>
                <tr><th>Phone</th><td>{{ $applicant->phone }}</td></tr>
                <tr><th>Department</th><td>{{ $applicant->department_name }}</td></tr>
                <tr><th>Position</th><td>{{ $applicant->position_title }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst(str_replace('_',' ',$applicant->application_status)) }}</td></tr>
                <tr><th>Applied At</th><td>{{ $applicant->applied_at }}</td></tr>
            </table>

            @if($applicant->resume_path)
                <a href="{{ route('hr1.applicants.download', $applicant->id) }}" class="btn btn-primary mt-3" target="_blank">View/Download CV</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Update Status</h5>
            <form action="{{ route('hr1.applicants.updateStatus', $applicant->id) }}" method="POST">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <select name="application_status" class="form-control" required>
                            @foreach(['pending','under_review','interview','accepted','rejected','onboarded'] as $status)
                                <option value="{{ $status }}" {{ $applicant->application_status == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_',' ',$status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('hr1.applicants.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection