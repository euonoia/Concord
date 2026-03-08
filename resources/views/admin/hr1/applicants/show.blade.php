@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2>Applicant Details</h2>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Full Name</th>
                    <td>{{ $applicant->first_name }} {{ $applicant->last_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $applicant->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $applicant->phone }}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $applicant->department_name }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $applicant->position_title }}</td>
                </tr>
                <tr>
                    <th>Specialization</th>
                    <td>{{ $applicant->specialization }}</td>
                </tr>
                <tr>
                    <th>Post Grad Status</th>
                    <td>{{ $applicant->post_grad_status }}</td>
                </tr>
                <tr>
                    <th>Applied At</th>
                    <td>{{ $applicant->applied_at }}</td>
                </tr>
            </table>

            <a href="{{ route('hr1.applicants.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection