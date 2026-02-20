@extends('admin.layouts.app')

@section('content')
<div class="container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Competency Framework</h3>
        @if(auth()->user()->role_slug === 'hr_admin')
            <span class="badge bg-success">HR Administrator Access</span>
        @else
            <span class="badge bg-info text-dark">Read-Only Access</span>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
        </div>
    @endif

    {{-- 1. PROTECTED FORM: Only visible to hr_admin --}}
    @if(auth()->user()->role_slug === 'hr_admin')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Add New Competency</h6>
        </div>
        <div class="card-body bg-light rounded-bottom">
            <form action="{{ route('competencies.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. MED-01" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Competency Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Emergency Response" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Category Group</label>
                        <select name="competency_group" class="form-select" required>
                            <option value="">-- Select Group --</option>
                            <option value="Medical">Medical</option>
                            <option value="Technical">Technical</option>
                            <option value="Leadership">Leadership</option>
                            <option value="Soft Skills">Soft Skills</option>
                            <option value="Administrative">Administrative</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Describe the skills or certifications required...">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-plus-lg me-1"></i> Add to Registry
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- 2. LIST VIEW: Visible to all authorized users --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 15%">Code</th>
                        <th style="width: 35%">Title</th>
                        <th style="width: 20%">Group</th>
                        <th style="width: 20%">Created At</th>
                        @if(auth()->user()->role_slug === 'hr_admin')
                            <th style="width: 10%" class="text-center">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($competencies as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $item->title }}</div>
                            <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                        </td>
                        <td>{{ $item->competency_group }}</td>
                        <td>{{ $item->created_at ? $item->created_at->format('Y-m-d') : 'N/A' }}</td>
                        
                        {{-- 3. PROTECTED ACTIONS: Only visible to hr_admin --}}
                        @if(auth()->user()->role_slug === 'hr_admin')
                        <td class="text-center">
                            <form action="{{ route('competencies.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this competency?')">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-link text-danger p-0" title="Delete">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role_slug === 'hr_admin' ? 5 : 4 }}" class="text-center py-4 text-muted">
                            No competencies found in the registry.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection