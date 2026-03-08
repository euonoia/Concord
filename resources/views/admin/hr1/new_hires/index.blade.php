@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">New Hire Management</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- FILTER SECTION -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Department</label>
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
                    <label>Specialization</label>
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
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">-- All Statuses --</option>
                        <option value="onboarding" {{ ($filters['status'] ?? '') == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                        <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- NEW HIRES TABLE -->
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
                        <th>Specialization</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newHires as $n)
                        <tr>
                            <td>{{ $n->id }}</td>
                            <td>{{ $n->first_name }} {{ $n->last_name }}</td>
                            <td>{{ $n->email }}</td>
                            <td>{{ $n->phone }}</td>
                            <td>{{ $n->department_name }}</td>
                            <td>{{ $n->specialization ?? 'N/A' }}</td>
                            <td>{{ ucfirst($n->status) }}</td>
                            <td>
                                <a href="{{ route('hr1.newhires.show', $n->id) }}" class="btn btn-sm btn-info">View</a>
                                @if($n->resume_path)
                                    <a href="{{ route('hr1.newhires.download', $n->id) }}" class="btn btn-sm btn-success" target="_blank">CV</a>
                                @endif
                                <form action="{{ route('hr1.newhires.updateStatus', $n->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="form-control form-control-sm">
                                        <option value="onboarding" {{ $n->status == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                                        <option value="active" {{ $n->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $n->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No new hires found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $newHires->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection