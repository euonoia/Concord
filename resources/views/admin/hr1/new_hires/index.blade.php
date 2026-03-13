@extends('admin.hr1.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">New Hire Management</h3>
            <p class="text-white">Track onboarding progress and finalize employment details for successful candidates.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
             <span class="text-white small fw-bold"><i class="bi bi-person-fill-add me-2"></i>Onboarding System</span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show hr1-mb-5" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="hr1-filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">-- All Departments --</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->department_id }}" {{ ($filters['department'] ?? '') == $d->department_id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Specialization</label>
                <select name="specialization" class="form-select form-select-sm">
                    <option value="">-- All Specializations --</option>
                    @foreach($specializations as $s)
                        <option value="{{ $s->specialization_name }}" {{ ($filters['specialization'] ?? '') == $s->specialization_name ? 'selected' : '' }}>
                            {{ $s->specialization_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- All --</option>
                    <option value="onboarding" {{ ($filters['status'] ?? '') == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                    <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                 <a href="{{ route('hr1.newhires.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill shadow-sm">Reset</a>
            </div>
        </form>
    </div>

    {{-- New Hires Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6 class="mb-0">Employee Records</h6>
            <div class="small text-muted">Showing {{ $newHires->count() }} of {{ $newHires->total() }} entries</div>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Hire Details</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="text-center">Onboarding Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newHires as $n)
                        <tr>
                            <td class="text-muted font-monospace" style="font-size: 0.75rem;">#{{ $n->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $n->first_name }} {{ $n->last_name }}</div>
                                <div class="small text-muted">{{ $n->email }}</div>
                            </td>
                            <td>
                                 <div class="fw-semibold text-dark small" style="font-size: 0.75rem;">{{ $n->department_name }}</div>
                                 <div class="small text-muted">{{ $n->specialization ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($n->status) {
                                        'active' => 'hr1-badge-success',
                                        'onboarding' => 'hr1-badge-info',
                                        'inactive' => 'hr1-badge-danger',
                                        default => 'hr1-badge-primary',
                                    };
                                @endphp
                                <span class="hr1-badge {{ $statusBadge }}">{{ ucfirst($n->status) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ route('hr1.newhires.show', $n->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                                    
                                    <form action="{{ route('hr1.newhires.updateStatus', $n->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm rounded-pill" style="width: 125px; font-size: 0.75rem;">
                                            <option value="onboarding" {{ $n->status == 'onboarding' ? 'selected' : '' }}>Onboarding</option>
                                            <option value="active" {{ $n->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $n->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </form>

                                    @if($n->resume_path)
                                        <a href="{{ route('hr1.newhires.download', $n->id) }}" class="btn btn-sm btn-outline-success rounded-circle" title="Download CV" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($newHires->hasPages())
            <div class="px-3 py-2 border-top bg-light">
                {{ $newHires->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection