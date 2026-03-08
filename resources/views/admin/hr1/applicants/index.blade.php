@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Applicant Management</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="department" class="form-control">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}" {{ ($filters['department'] ?? '') == $d->department_id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="specialization" class="form-control">
                        <option value="">-- All Specializations --</option>
                        @foreach($specializations as $s)
                            <option value="{{ $s->specialization_name }}" {{ ($filters['specialization'] ?? '') == $s->specialization_name ? 'selected' : '' }}>
                                {{ $s->specialization_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">-- All Statuses --</option>
                        @foreach(['pending','under_review','interview','accepted','rejected','onboarded'] as $status)
                            <option value="{{ $status }}" {{ ($filters['status'] ?? '') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ',$status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Specialization</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->first_name }} {{ $a->last_name }}</td>
                            <td>{{ $a->email }}</td>
                            <td>{{ $a->phone }}</td>
                            <td>{{ $a->department_name }}</td>
                            <td>{{ $a->specialization }}</td>
                            <td>{{ ucfirst(str_replace('_',' ',$a->application_status)) }}</td>
                            <td>
                                <a href="{{ route('hr1.applicants.show', $a->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $applicants->withQueryString()->links() }}</div>
        </div>
    </div>
</div>
@endsection