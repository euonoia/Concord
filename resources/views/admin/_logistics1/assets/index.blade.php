@extends('admin._logistics1.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-boxes me-2"></i>Asset Management</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAssetModal">
        <i class="bi bi-plus-lg me-1"></i> Add Asset
    </button>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.logistics1.asset_management.index') }}" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search by name, code, serial no., or supplier..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="active"       {{ request('status') === 'active'       ? 'selected' : '' }}>Active</option>
            <option value="inactive"     {{ request('status') === 'inactive'     ? 'selected' : '' }}>Inactive</option>
            <option value="under_repair" {{ request('status') === 'under_repair' ? 'selected' : '' }}>Under Repair</option>
            <option value="disposed"     {{ request('status') === 'disposed'     ? 'selected' : '' }}>Disposed</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="category" class="form-select form-select-sm">
            <option value="">All Categories</option>
            <option value="Equipment"   {{ request('category') === 'Equipment'   ? 'selected' : '' }}>Equipment</option>
            <option value="Vehicle"     {{ request('category') === 'Vehicle'     ? 'selected' : '' }}>Vehicle</option>
            <option value="Furniture"   {{ request('category') === 'Furniture'   ? 'selected' : '' }}>Furniture</option>
            <option value="IT Hardware" {{ request('category') === 'IT Hardware' ? 'selected' : '' }}>IT Hardware</option>
            <option value="Tools"       {{ request('category') === 'Tools'       ? 'selected' : '' }}>Tools</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="condition_status" class="form-select form-select-sm">
            <option value="">All Conditions</option>
            <option value="excellent" {{ request('condition_status') === 'excellent' ? 'selected' : '' }}>Excellent</option>
            <option value="good"      {{ request('condition_status') === 'good'      ? 'selected' : '' }}>Good</option>
            <option value="fair"      {{ request('condition_status') === 'fair'      ? 'selected' : '' }}>Fair</option>
            <option value="poor"      {{ request('condition_status') === 'poor'      ? 'selected' : '' }}>Poor</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary btn-sm w-100">
            <i class="bi bi-search me-1"></i> Filter
        </button>
    </div>
</form>

{{-- Assets Table --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Purchase Cost</th>
                <th>Warranty Expiry</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td>{{ $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() }}</td>
                <td><code>{{ $asset->asset_code }}</code></td>
                <td>
                    {{ $asset->asset_name }}
                    @if($asset->serial_number)
                        <br><small class="text-muted">S/N: {{ $asset->serial_number }}</small>
                    @endif
                    @if($asset->supplier)
                        <br><small class="text-muted"><i class="bi bi-shop me-1"></i>{{ $asset->supplier }}</small>
                    @endif
                </td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->location ?? '—' }}</td>
                <td>
                    @php
                        $statusMap = [
                            'active'       => 'success',
                            'inactive'     => 'secondary',
                            'under_repair' => 'warning',
                            'disposed'     => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusMap[$asset->status] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                    </span>
                </td>
                <td>
                    @php
                        $conditionMap = [
                            'excellent' => 'success',
                            'good'      => 'info',
                            'fair'      => 'warning',
                            'poor'      => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $conditionMap[$asset->condition_status] ?? 'secondary' }}">
                        {{ ucfirst($asset->condition_status) }}
                    </span>
                </td>
                <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                <td>
                    @if($asset->warranty_expiry)
                        @php $expiry = \Carbon\Carbon::parse($asset->warranty_expiry); @endphp
                        <span class="{{ $expiry->isPast() ? 'text-danger' : 'text-success' }}">
                            {{ $expiry->format('M d, Y') }}
                            @if($expiry->isPast()) <i class="bi bi-exclamation-triangle-fill"></i> @endif
                        </span>
                    @else
                        —
                    @endif
                </td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editAssetModal"
                        data-id="{{ $asset->id }}"
                        data-asset_code="{{ $asset->asset_code }}"
                        data-asset_name="{{ $asset->asset_name }}"
                        data-serial_number="{{ $asset->serial_number }}"
                        data-category="{{ $asset->category }}"
                        data-location="{{ $asset->location }}"
                        data-status="{{ $asset->status }}"
                        data-condition_status="{{ $asset->condition_status }}"
                        data-purchase_date="{{ $asset->purchase_date }}"
                        data-purchase_cost="{{ $asset->purchase_cost }}"
                        data-supplier="{{ $asset->supplier }}"
                        data-warranty_expiry="{{ $asset->warranty_expiry }}"
                        data-last_maintained_at="{{ $asset->last_maintained_at }}"
                        data-notes="{{ $asset->notes }}">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <form method="POST"
                          action="{{ route('admin.logistics1.asset_management.destroy', $asset->id) }}"
                          style="display:inline;"
                          onsubmit="return confirm('Are you sure you want to delete this asset?')">
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
                    No assets found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-end mt-2">
    {{ $assets->withQueryString()->links() }}
</div>


{{-- ==================== ADD ASSET MODAL ==================== --}}
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.asset_management.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-boxes me-2"></i>Add New Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Code <span class="text-danger">*</span></label>
                            <input type="text" name="asset_code" class="form-control" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" name="asset_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category...</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Vehicle">Vehicle</option>
                                <option value="Furniture">Furniture</option>
                                <option value="IT Hardware">IT Hardware</option>
                                <option value="Tools">Tools</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                            <select name="condition_status" class="form-select" required>
                                <option value="excellent">Excellent</option>
                                <option value="good" selected>Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Purchase Cost (₱)</label>
                            <input type="number" name="purchase_cost" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier</label>
                            <input type="text" name="supplier" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warranty Expiry</label>
                            <input type="date" name="warranty_expiry" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Maintained</label>
                            <input type="date" name="last_maintained_at" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== EDIT ASSET MODAL ==================== --}}
<div class="modal fade" id="editAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editAssetForm">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Code</label>
                            <input type="text" class="form-control" id="edit_asset_code" disabled>
                            <small class="text-muted">Code cannot be changed after creation.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" name="asset_name" id="edit_asset_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serial Number</label>
                            <input type="text" name="serial_number" id="edit_serial_number" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" id="edit_category" class="form-select" required>
                                <option value="Equipment">Equipment</option>
                                <option value="Vehicle">Vehicle</option>
                                <option value="Furniture">Furniture</option>
                                <option value="IT Hardware">IT Hardware</option>
                                <option value="Tools">Tools</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_asset_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                            <select name="condition_status" id="edit_condition_status" class="form-select" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Purchase Date</label>
                            <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Purchase Cost (₱)</label>
                            <input type="number" name="purchase_cost" id="edit_purchase_cost" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Supplier</label>
                            <input type="text" name="supplier" id="edit_supplier" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warranty Expiry</label>
                            <input type="date" name="warranty_expiry" id="edit_warranty_expiry" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Maintained</label>
                            <input type="date" name="last_maintained_at" id="edit_last_maintained_at" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i> Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Populate edit modal via JS --}}
<script>
document.getElementById('editAssetModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editAssetForm');

    form.action = `/admin/logistics1/asset-management/update/${btn.dataset.id}`;

    document.getElementById('edit_asset_code').value         = btn.dataset.asset_code;
    document.getElementById('edit_asset_name').value         = btn.dataset.asset_name;
    document.getElementById('edit_serial_number').value      = btn.dataset.serial_number   ?? '';
    document.getElementById('edit_category').value           = btn.dataset.category;
    document.getElementById('edit_location').value           = btn.dataset.location         ?? '';
    document.getElementById('edit_asset_status').value       = btn.dataset.status;
    document.getElementById('edit_condition_status').value   = btn.dataset.condition_status;
    document.getElementById('edit_purchase_date').value      = btn.dataset.purchase_date    ?? '';
    document.getElementById('edit_purchase_cost').value      = btn.dataset.purchase_cost;
    document.getElementById('edit_supplier').value           = btn.dataset.supplier          ?? '';
    document.getElementById('edit_warranty_expiry').value    = btn.dataset.warranty_expiry   ?? '';
    document.getElementById('edit_last_maintained_at').value = btn.dataset.last_maintained_at ?? '';
    document.getElementById('edit_notes').value              = btn.dataset.notes             ?? '';
});
</script>

@endsection