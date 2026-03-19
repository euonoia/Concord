@extends('admin._logistics1.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-kanban me-2"></i>Project Management</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProjectModal">
        <i class="bi bi-plus-lg me-1"></i> New Project
    </button>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.logistics1.project_management.index') }}" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search by name or code..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="planned"   {{ request('status') === 'planned'   ? 'selected' : '' }}>Planned</option>
            <option value="ongoing"   {{ request('status') === 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="on_hold"   {{ request('status') === 'on_hold'   ? 'selected' : '' }}>On Hold</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="priority" class="form-select form-select-sm">
            <option value="">All Priorities</option>
            <option value="low"      {{ request('priority') === 'low'      ? 'selected' : '' }}>Low</option>
            <option value="normal"   {{ request('priority') === 'normal'   ? 'selected' : '' }}>Normal</option>
            <option value="high"     {{ request('priority') === 'high'     ? 'selected' : '' }}>High</option>
            <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-secondary btn-sm w-100">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>

{{-- Projects Table --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Project Name</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Budget</th>
                <th>Actual Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr>
                <td>{{ $loop->iteration + ($projects->currentPage() - 1) * $projects->perPage() }}</td>
                <td><code>{{ $project->project_code }}</code></td>
                <td>
                    {{ $project->project_name }}
                    @if($project->description)
                        <br><small class="text-muted">{{ Str::limit($project->description, 60) }}</small>
                    @endif
                </td>
                <td>
                    @php
                        $statusMap = [
                            'planned'   => 'secondary',
                            'ongoing'   => 'primary',
                            'completed' => 'success',
                            'on_hold'   => 'warning',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusMap[$project->status] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </td>
                <td>
                    @php
                        $priorityMap = [
                            'low'      => 'success',
                            'normal'   => 'info',
                            'high'     => 'warning',
                            'critical' => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $priorityMap[$project->priority] ?? 'secondary' }}">
                        {{ ucfirst($project->priority) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</td>
                <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : '—' }}</td>
                <td>₱{{ number_format($project->budget, 2) }}</td>
                <td>₱{{ number_format($project->actual_cost, 2) }}</td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editProjectModal"
                        data-id="{{ $project->id }}"
                        data-project_code="{{ $project->project_code }}"
                        data-project_name="{{ $project->project_name }}"
                        data-description="{{ $project->description }}"
                        data-start_date="{{ $project->start_date }}"
                        data-end_date="{{ $project->end_date }}"
                        data-status="{{ $project->status }}"
                        data-priority="{{ $project->priority }}"
                        data-budget="{{ $project->budget }}"
                        data-actual_cost="{{ $project->actual_cost }}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <form method="POST"
                          action="{{ route('admin.logistics1.project_management.destroy', $project->id) }}"
                          style="display:inline;"
                          onsubmit="return confirm('Are you sure you want to delete this project?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                    No projects found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-end mt-2">
    {{ $projects->withQueryString()->links() }}
</div>


{{-- ==================== ADD PROJECT MODAL ==================== --}}
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.project_management.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-kanban me-2"></i>New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project Code <span class="text-danger">*</span></label>
                            <input type="text" name="project_code" class="form-control" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="planned">Planned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Budget (₱)</label>
                            <input type="number" name="budget" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Actual Cost (₱)</label>
                            <input type="number" name="actual_cost" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== EDIT PROJECT MODAL ==================== --}}
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editProjectForm">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project Code</label>
                            <input type="text" class="form-control" id="edit_project_code" disabled>
                            <small class="text-muted">Code cannot be changed after creation.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="edit_project_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="planned">Planned</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" id="edit_priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Budget (₱)</label>
                            <input type="number" name="budget" id="edit_budget" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Actual Cost (₱)</label>
                            <input type="number" name="actual_cost" id="edit_actual_cost" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i> Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Populate edit modal via JS --}}
<script>
document.getElementById('editProjectModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editProjectForm');

    form.action = `/admin/logistics1/project-management/update/${btn.dataset.id}`;

    document.getElementById('edit_project_code').value  = btn.dataset.project_code;
    document.getElementById('edit_project_name').value  = btn.dataset.project_name;
    document.getElementById('edit_description').value   = btn.dataset.description;
    document.getElementById('edit_start_date').value    = btn.dataset.start_date;
    document.getElementById('edit_end_date').value      = btn.dataset.end_date ?? '';
    document.getElementById('edit_status').value        = btn.dataset.status;
    document.getElementById('edit_priority').value      = btn.dataset.priority;
    document.getElementById('edit_budget').value        = btn.dataset.budget;
    document.getElementById('edit_actual_cost').value   = btn.dataset.actual_cost;
});
</script>
@extends('admin.layouts.app')