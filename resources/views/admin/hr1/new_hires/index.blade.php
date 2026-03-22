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

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h4 class="mb-1 text-primary"><i class="fas fa-exchange-alt me-2"></i>Core Human Capital Handover</h4>
                        <p class="text-muted mb-0">Monitor and synchronize your hired employees with the HR4 module for payroll and core records.</p>
                    </div>
                    <form action="{{ route('hr1.newhires.syncHr4') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-sync me-2"></i>Handover to Core Capital
                        </button>
                    </form>
                </div>
                @if($recentSyncs->count() > 0)
                <div class="card-footer bg-white border-0 px-4 pb-4">
                    <label class="small text-uppercase fw-bold text-muted mb-2">Recently Synchronized</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($recentSyncs as $sync)
                            <div class="badge bg-white text-dark border p-2 shadow-sm d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>{{ $sync->full_name }} ({{ $sync->employee_id }})</span>
                                <span class="ms-2 text-muted small border-start ps-2">{{ \Carbon\Carbon::parse($sync->hired_at)->format('M d, H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card" style="margin-top: 40px; padding: 10px;">
        <div class="card-body" style="padding: 10px;">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; align-items: end; margin-bottom: 0; padding: 10px 15px;">
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
                <div class="d-flex align-items-end justify-content-end">
                    <button class="btn btn-primary px-5 py-2" style="width: auto;">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm" style="margin-top: 40px;">
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
                            <th class="text-nowrap text-center">HR2 Assessment</th>
                            <th class="text-nowrap text-center">HR1 Validation</th>
                            <th class="text-nowrap text-center" style="min-width: 200px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($newHires as $n)
                            <tr>
                                <td>{{ $n->id }}</td>
                                <td class="text-nowrap font-weight-bold">
                                    {{ $n->first_name }} {{ $n->last_name }}
                                    @if($n->status == 'onboarding')
                                        <i class="bi bi-hourglass-split text-info ms-1" title="Onboarding In Progress"></i>
                                    @endif
                                </td>
                                <td>{{ $n->email }}</td>
                                <td class="text-nowrap">{{ $n->phone }}</td>
                                <td>{{ $n->department_name }}</td>
                                <td>{{ $n->specialization ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $n->status == 'active' ? 'bg-success' : ($n->status == 'onboarding' ? 'bg-info' : 'bg-secondary') }}">
                                        {{ ucfirst($n->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = [
                                            'pending' => 'bg-warning text-dark',
                                            'scheduled' => 'bg-primary',
                                            'passed' => 'bg-success',
                                            'failed' => 'bg-danger'
                                        ][$n->assessment_status ?? 'pending'];
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ strtoupper($n->assessment_status ?? 'pending') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($n->is_validated)
                                        <span class="badge bg-success" title="Validated by {{ $n->validated_by }}">
                                            <i class="bi bi-check-all me-1"></i> VALIDATED
                                        </span>
                                    @elseif($n->assessment_status == 'passed')
                                        <form action="{{ route('hr1.newhires.validateAssessment', $n->applicant_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0" title="Click to Validate Grade">
                                                <i class="bi bi-patch-check me-1"></i> Validate Grade
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-muted border">WAITING</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('hr1.newhires.show', $n->id) }}" class="btn btn-sm btn-info text-white" title="View Full Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($n->resume_path)
                                            <a href="{{ route('hr1.newhires.download', $n->id) }}" class="btn btn-sm btn-success" target="_blank" title="Download CV">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        @endif

                                        <form action="{{ route('hr1.newhires.updateStatus', $n->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 110px;" {{ ($n->status == 'onboarding' && !$n->is_validated) ? 'title="Validate grade first to activate"' : '' }}>
                                                <option value="onboarding" {{ $n->status == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                                                <option value="active" {{ $n->status == 'active' ? 'selected' : '' }} {{ ($n->status == 'onboarding' && !$n->is_validated) ? 'disabled' : '' }}>Active</option>
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