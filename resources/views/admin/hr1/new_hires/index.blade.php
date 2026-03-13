@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">New Hire Management</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Department</label>
                    <select name="department" class="form-select">
                        <option value="">-- All Departments --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->department_id }}" {{ ($filters['department'] ?? '') == $d->department_id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Specialization</label>
                    <select name="specialization" class="form-select">
                        <option value="">-- All Specializations --</option>
                        @foreach($specializations as $s)
                            <option value="{{ $s->specialization_name }}" {{ ($filters['specialization'] ?? '') == $s->specialization_name ? 'selected' : '' }}>
                                {{ $s->specialization_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- All Statuses --</option>
                        <option value="onboarding" {{ ($filters['status'] ?? '') == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                        <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0"> <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap" style="width: 50px;">#</th>
                            <th class="text-nowrap">Full Name</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap">Phone</th>
                            <th class="text-nowrap">Department</th>
                            <th class="text-nowrap">Specialization</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap text-center" style="min-width: 250px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newHires as $n)
                            <tr>
                                <td>{{ $n->id }}</td>
                                <td class="text-nowrap font-weight-bold">{{ $n->first_name }} {{ $n->last_name }}</td>
                                <td>{{ $n->email }}</td>
                                <td class="text-nowrap">{{ $n->phone }}</td>
                                <td>{{ $n->department_name }}</td>
                                <td>{{ $n->specialization ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $n->status == 'active' ? 'bg-success' : ($n->status == 'onboarding' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ ucfirst($n->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('hr1.newhires.show', $n->id) }}" class="btn btn-sm btn-info text-white">View</a>
                                        
                                        @if($n->resume_path)
                                            <a href="{{ route('hr1.newhires.download', $n->id) }}" class="btn btn-sm btn-success" target="_blank">CV</a>
                                        @endif

                                        <form action="{{ route('hr1.newhires.updateStatus', $n->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 120px;">
                                                <option value="onboarding" {{ $n->status == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                                                <option value="active" {{ $n->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $n->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($newHires->hasPages())
            <div class="card-footer bg-white">
                {{ $newHires->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    /* Prevent table text from wrapping to the next line in critical areas */
    .text-nowrap {
        white-space: nowrap;
    }
    
    /* Ensure the table doesn't feel cramped on small screens */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Style for the select dropdown inside table */
    .form-select-sm {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        font-size: 0.8rem;
    }
</style>
@endsection