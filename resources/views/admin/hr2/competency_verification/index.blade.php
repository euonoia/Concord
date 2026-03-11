@extends('layouts.dashboard.app')

@section('content')
<div class="container-fluid"> <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Competency Verification</h2>
        <a href="{{ route('competencies.index') }}" class="btn btn-secondary">
            ← Back to Framework
        </a>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.hr2.competency.verification.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search Name or ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.hr2.competency.verification.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Employee</th>
                    <th>ID</th>
                    <th>Dept/Spec</th>
                    <th>Competency</th>
                    <th>Status</th>
                    <th>Completed At</th>
                    <th>Verified By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completions as $c)
                <tr>
                    <td><strong>{{ $c->first_name }} {{ $c->last_name }}</strong></td>
                    <td>{{ $c->employee_id }}</td>
                    <td>
                        <small class="text-muted">{{ $c->department_id }}</small><br>
                        {{ $c->specialization }}
                    </td>
                    <td><code>{{ $c->competency_code }}</code></td>
                    <td>
                        @if($c->verified_by)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($c->completed_at)->format('M d, Y') }}</td>
                    <td>{{ $c->verified_by ?? '—' }}</td>
                    <td>
                        @if(!$c->verified_by)
                        <form method="POST" action="{{ route('admin.hr2.competency.verify', $c->id) }}">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="text" name="verification_notes" class="form-control" placeholder="Notes...">
                                <button class="btn btn-success" type="submit">Verify</button>
                            </div>
                        </form>
                        @else
                            <span class="text-muted small">Confirmed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">No competency records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $completions->links() }}
    </div>

</div>
@endsection