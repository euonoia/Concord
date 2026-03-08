@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Applicant Management</h2>

    <!-- FILTER SECTION -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Department</label>
                    <select name="department" class="form-control">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}" {{ ($department ?? '') == $d->department_id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Position</label>
                    <select name="position" class="form-control">
                        <option value="">-- All Positions --</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}" {{ ($position ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->position_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- APPLICANTS TABLE -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Specialization</th>
                        <th>Applied At</th>
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
                            <td>{{ $a->position_title }}</td>
                            <td>{{ $a->specialization }}</td>
                            <td>{{ $a->applied_at }}</td>
                            <td>
                                <a href="{{ route('hr1.applicants.show', $a->id) }}" class="btn btn-sm btn-info">View</a>
                                @if($a->resume_path)
                                    <a href="{{ route('hr1.applicants.download', $a->id) }}" class="btn btn-sm btn-success" target="_blank">
                                        View CV
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $applicants->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection