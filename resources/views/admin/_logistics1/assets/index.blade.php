@extends('admin._logistics1.layouts.app')

@section('content')

<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-header h4 { font-size: 1.3rem; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }
    .page-header p  { font-size: 0.82rem; color: #94a3b8; margin: 0; }

    .btn-add { background: #1e293b; color: #fff; border: none; padding: 0.45rem 1.1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.18s; cursor: pointer; }
    .btn-add:hover { background: #334155; color: #fff; }

    /* Tab nav */
    .asset-tabs { display: flex; gap: 4px; background: #f1f5f9; padding: 5px; border-radius: 10px; margin-bottom: 1.5rem; width: fit-content; }
    .asset-tab  { padding: 0.45rem 1.1rem; border-radius: 7px; font-size: 0.82rem; font-weight: 600; color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; white-space: nowrap; }
    .asset-tab:hover  { color: #1e293b; background: #e2e8f0; }
    .asset-tab.active { background: #1e293b; color: #fff; box-shadow: 0 2px 8px rgba(30,41,59,0.18); }

    .tab-desc { font-size: 0.82rem; color: #64748b; margin-bottom: 1.1rem; }

    .filter-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.9rem 1rem; margin-bottom: 1.25rem; }
    .filter-bar .form-control,
    .filter-bar .form-select { border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.82rem; background: #fff; color: #1e293b; padding: 0.4rem 0.75rem; box-shadow: none; }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .btn-filter { background: #1e293b; color: #fff; border: none; border-radius: 7px; font-size: 0.82rem; font-weight: 600; padding: 0.4rem 1rem; cursor: pointer; }
    .btn-filter:hover { background: #334155; }

    .data-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
    .data-table { margin: 0; font-size: 0.82rem; }
    .data-table thead tr { background: #1e293b; }
    .data-table thead th { color: #94a3b8; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; padding: 0.85rem 1rem; border: none; white-space: nowrap; }
    .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody td { padding: 0.75rem 1rem; color: #334155; border: none; vertical-align: middle; }
    .row-num   { color: #94a3b8; font-size: 0.75rem; }
    .code-pill { background: #f1f5f9; color: #475569; border-radius: 5px; padding: 2px 8px; font-size: 0.73rem; font-family: monospace; font-weight: 600; }
    .row-title { font-weight: 600; color: #1e293b; }
    .row-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .badge-pill { border-radius: 20px; padding: 3px 10px; font-size: 0.72rem; font-weight: 600; display: inline-block; }
    .bp-active       { background: #dcfce7; color: #16a34a; }
    .bp-inactive     { background: #f1f5f9; color: #475569; }
    .bp-under_repair { background: #fef9c3; color: #a16207; }
    .bp-disposed     { background: #fee2e2; color: #b91c1c; }
    .bp-excellent    { background: #dcfce7; color: #16a34a; }
    .bp-good         { background: #dbeafe; color: #1d4ed8; }
    .bp-fair         { background: #ffedd5; color: #c2410c; }
    .bp-poor         { background: #fee2e2; color: #b91c1c; }
    .bp-available    { background: #dcfce7; color: #16a34a; }
    .bp-in_use       { background: #dbeafe; color: #1d4ed8; }
    .bp-maintenance  { background: #fef9c3; color: #a16207; }
    .bp-retired      { background: #fee2e2; color: #b91c1c; }

    .btn-act { width: 30px; height: 30px; border-radius: 7px; border: none; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; transition: all 0.15s; cursor: pointer; }
    .btn-act-edit   { background: #fef9c3; color: #a16207; }
    .btn-act-edit:hover   { background: #fde68a; }
    .btn-act-delete { background: #fee2e2; color: #b91c1c; }
    .btn-act-delete:hover { background: #fecaca; }

    .asset-img { width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; cursor: zoom-in; transition: transform 0.15s; }
    .asset-img:hover { transform: scale(1.08); }
    .asset-img-placeholder { width: 38px; height: 38px; border-radius: 8px; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1rem; border: 1px solid #e2e8f0; }

    .img-upload-wrap { border: 2px dashed #e2e8f0; border-radius: 10px; padding: 1.25rem; text-align: center; cursor: pointer; transition: border-color 0.15s; }
    .img-upload-wrap:hover { border-color: #94a3b8; }
    .img-upload-wrap i { font-size: 1.6rem; color: #94a3b8; display: block; margin-bottom: 4px; }
    .img-upload-wrap span { font-size: 0.78rem; color: #94a3b8; }
    .img-preview-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 0.6rem; }
    .img-preview-grid img { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; cursor: zoom-in; }

    /* Lightbox */
    .lightbox-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; }
    .lightbox-overlay.active { display: flex; }
    .lightbox-overlay img { max-width: 90vw; max-height: 90vh; border-radius: 10px; box-shadow: 0 8px 40px rgba(0,0,0,0.5); }
    .lightbox-close { position: absolute; top: 1.2rem; right: 1.5rem; color: #fff; font-size: 2rem; cursor: pointer; line-height: 1; }

    .empty-state { padding: 3.5rem 1rem; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 2.2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4; }
    .empty-state p { font-size: 0.85rem; margin: 0; }

    .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header  { background: #1e293b; border-radius: 14px 14px 0 0; padding: 1rem 1.25rem; border: none; }
    .modal-header .modal-title { color: #f8fafc; font-size: 0.95rem; font-weight: 700; }
    .modal-header .btn-close   { filter: invert(1) brightness(2); }
    .modal-body  { padding: 1.25rem; }
    .modal-footer { padding: 0.9rem 1.25rem; border-top: 1px solid #f1f5f9; }
    .modal-body .form-label { font-size: 0.78rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
    .modal-body .form-control,
    .modal-body .form-select { font-size: 0.83rem; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; padding: 0.45rem 0.75rem; box-shadow: none; }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus { border-color: #94a3b8; box-shadow: none; }
    .modal-body .form-control:disabled { background: #f8fafc; color: #94a3b8; }
    .btn-mc { background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 1rem; cursor: pointer; }
    .btn-mc:hover { background: #e2e8f0; }
    .btn-ms { background: #1e293b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-ms:hover { background: #334155; color: #fff; }
    .btn-mu { background: #f59e0b; color: #fff; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; padding: 0.45rem 1.1rem; cursor: pointer; }
    .btn-mu:hover { background: #d97706; }
</style>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightboxImg" src="" alt="">
</div>

{{-- Page Header --}}
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-boxes me-2"></i>Asset Management</h4>
        <p>Manage organizational assets and fleet vehicles</p>
    </div>
    @if($activeTab === 'assets' || $activeTab === 'asset_tracking')
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addAssetModal">
        <i class="bi bi-plus-lg"></i> Add Asset
    </button>
    @endif
</div>

{{-- Tab Navigation --}}
<div class="asset-tabs">
    <a class="asset-tab {{ $activeTab === 'assets' ? 'active' : '' }}"
       href="{{ route('admin.logistics1.asset_management.index', ['tab' => 'assets']) }}">
        <i class="bi bi-boxes"></i> Assets
    </a>
    <a class="asset-tab {{ $activeTab === 'asset_tracking' ? 'active' : '' }}"
       href="{{ route('admin.logistics1.asset_management.index', ['tab' => 'asset_tracking']) }}">
        <i class="bi bi-qr-code"></i> Asset Tracking &amp; Tagging
    </a>
    <a class="asset-tab {{ $activeTab === 'disposal_replacement' ? 'active' : '' }}"
       href="{{ route('admin.logistics1.asset_management.index', ['tab' => 'disposal_replacement']) }}">
        <i class="bi bi-arrow-repeat"></i> Disposal &amp; Replacement
    </a>
    <a class="asset-tab {{ $activeTab === 'fleet' ? 'active' : '' }}"
       href="{{ route('admin.logistics1.asset_management.index', ['tab' => 'fleet']) }}">
        <i class="bi bi-truck"></i> Fleet Vehicles
    </a>
</div>


{{-- ===== TAB 1: ASSETS ===== --}}
@if($activeTab === 'assets')

<p class="tab-desc">View and manage all organizational assets.</p>

<form method="GET" action="{{ route('admin.logistics1.asset_management.index') }}">
    <input type="hidden" name="tab" value="assets">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name, code, serial no., supplier..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active"       {{ request('status')==='active'       ?'selected':'' }}>Active</option>
                <option value="inactive"     {{ request('status')==='inactive'     ?'selected':'' }}>Inactive</option>
                <option value="under_repair" {{ request('status')==='under_repair' ?'selected':'' }}>Under Repair</option>
                <option value="disposed"     {{ request('status')==='disposed'     ?'selected':'' }}>Disposed</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="Equipment"   {{ request('category')==='Equipment'   ?'selected':'' }}>Equipment</option>
                <option value="Furniture"   {{ request('category')==='Furniture'   ?'selected':'' }}>Furniture</option>
                <option value="IT Hardware" {{ request('category')==='IT Hardware' ?'selected':'' }}>IT Hardware</option>
                <option value="Tools"       {{ request('category')==='Tools'       ?'selected':'' }}>Tools</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="condition_status" class="form-select">
                <option value="">All Conditions</option>
                <option value="excellent" {{ request('condition_status')==='excellent' ?'selected':'' }}>Excellent</option>
                <option value="good"      {{ request('condition_status')==='good'      ?'selected':'' }}>Good</option>
                <option value="fair"      {{ request('condition_status')==='fair'      ?'selected':'' }}>Fair</option>
                <option value="poor"      {{ request('condition_status')==='poor'      ?'selected':'' }}>Poor</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Purchase Cost</th>
                <th>Supplier</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($assets->currentPage()-1) * $assets->perPage() }}</span></td>
                <td>
                    @if(!empty($asset->asset_image))
                        <img src="{{ asset('storage/' . $asset->asset_image) }}" class="asset-img" alt="{{ $asset->asset_name }}" onclick="openLightbox(this.src)">
                    @else
                        <span class="asset-img-placeholder"><i class="bi bi-image"></i></span>
                    @endif
                </td>
                <td><span class="code-pill">{{ $asset->asset_code }}</span></td>
                <td>
                    <div class="row-title">{{ $asset->asset_name }}</div>
                    @if($asset->serial_number)<div class="row-sub">S/N: {{ $asset->serial_number }}</div>@endif
                </td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->location ?? '—' }}</td>
                <td><span class="badge-pill bp-{{ $asset->status }}">{{ ucfirst(str_replace('_',' ',$asset->status)) }}</span></td>
                <td><span class="badge-pill bp-{{ $asset->condition_status }}">{{ ucfirst($asset->condition_status) }}</span></td>
                <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                <td>{{ $asset->supplier ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn-act btn-act-edit" title="Edit"
                            data-bs-toggle="modal" data-bs-target="#editAssetModal"
                            data-id="{{ $asset->id }}"
                            data-asset_code="{{ $asset->asset_code }}"
                            data-asset_name="{{ $asset->asset_name }}"
                            data-serial_number="{{ $asset->serial_number }}"
                            data-category="{{ $asset->category }}"
                            data-location="{{ $asset->location }}"
                            data-status="{{ $asset->status }}"
                            data-condition_status="{{ $asset->condition_status }}"
                            data-purchase_cost="{{ $asset->purchase_cost }}"
                            data-supplier="{{ $asset->supplier }}"
                            data-asset_image="{{ $asset->asset_image }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.logistics1.asset_management.destroy', $asset->id) }}" style="display:inline;" onsubmit="return confirm('Delete this asset?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-act btn-act-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="11"><div class="empty-state"><i class="bi bi-boxes"></i><p>No assets found. Add your first asset to get started.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $assets->withQueryString()->links() }}</div>
@endif


{{-- ===== TAB 2: ASSET TRACKING & TAGGING ===== --}}
@if($activeTab === 'asset_tracking')

<p class="tab-desc">Track and tag all organizational assets by category, location, and condition.</p>

<form method="GET" action="{{ route('admin.logistics1.asset_management.index') }}">
    <input type="hidden" name="tab" value="asset_tracking">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name, code, serial no., supplier..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active"       {{ request('status')==='active'       ?'selected':'' }}>Active</option>
                <option value="inactive"     {{ request('status')==='inactive'     ?'selected':'' }}>Inactive</option>
                <option value="under_repair" {{ request('status')==='under_repair' ?'selected':'' }}>Under Repair</option>
                <option value="disposed"     {{ request('status')==='disposed'     ?'selected':'' }}>Disposed</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="Equipment"   {{ request('category')==='Equipment'   ?'selected':'' }}>Equipment</option>
                <option value="Furniture"   {{ request('category')==='Furniture'   ?'selected':'' }}>Furniture</option>
                <option value="IT Hardware" {{ request('category')==='IT Hardware' ?'selected':'' }}>IT Hardware</option>
                <option value="Tools"       {{ request('category')==='Tools'       ?'selected':'' }}>Tools</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="condition_status" class="form-select">
                <option value="">All Conditions</option>
                <option value="excellent" {{ request('condition_status')==='excellent' ?'selected':'' }}>Excellent</option>
                <option value="good"      {{ request('condition_status')==='good'      ?'selected':'' }}>Good</option>
                <option value="fair"      {{ request('condition_status')==='fair'      ?'selected':'' }}>Fair</option>
                <option value="poor"      {{ request('condition_status')==='poor'      ?'selected':'' }}>Poor</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Purchase Cost</th>
                <th>Supplier</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($assets->currentPage()-1) * $assets->perPage() }}</span></td>
                <td>
                    @if(!empty($asset->asset_image))
                        <img src="{{ asset('storage/' . $asset->asset_image) }}" class="asset-img" alt="{{ $asset->asset_name }}" onclick="openLightbox(this.src)">
                    @else
                        <span class="asset-img-placeholder"><i class="bi bi-image"></i></span>
                    @endif
                </td>
                <td><span class="code-pill">{{ $asset->asset_code }}</span></td>
                <td>
                    <div class="row-title">{{ $asset->asset_name }}</div>
                    @if($asset->serial_number)<div class="row-sub">S/N: {{ $asset->serial_number }}</div>@endif
                </td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->location ?? '—' }}</td>
                <td><span class="badge-pill bp-{{ $asset->status }}">{{ ucfirst(str_replace('_',' ',$asset->status)) }}</span></td>
                <td><span class="badge-pill bp-{{ $asset->condition_status }}">{{ ucfirst($asset->condition_status) }}</span></td>
                <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                <td>{{ $asset->supplier ?? '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn-act btn-act-edit" title="Edit"
                            data-bs-toggle="modal" data-bs-target="#editAssetModal"
                            data-id="{{ $asset->id }}"
                            data-asset_code="{{ $asset->asset_code }}"
                            data-asset_name="{{ $asset->asset_name }}"
                            data-serial_number="{{ $asset->serial_number }}"
                            data-category="{{ $asset->category }}"
                            data-location="{{ $asset->location }}"
                            data-status="{{ $asset->status }}"
                            data-condition_status="{{ $asset->condition_status }}"
                            data-purchase_cost="{{ $asset->purchase_cost }}"
                            data-supplier="{{ $asset->supplier }}"
                            data-asset_image="{{ $asset->asset_image }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.logistics1.asset_management.destroy', $asset->id) }}" style="display:inline;" onsubmit="return confirm('Delete this asset?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-act btn-act-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="11"><div class="empty-state"><i class="bi bi-boxes"></i><p>No assets found. Add your first asset to get started.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $assets->withQueryString()->links() }}</div>
@endif


{{-- ===== TAB 2: DISPOSAL & REPLACEMENT ===== --}}
@if($activeTab === 'disposal_replacement')

<p class="tab-desc">Assets marked as disposed or requiring replacement.</p>

<form method="GET" action="{{ route('admin.logistics1.asset_management.index') }}">
    <input type="hidden" name="tab" value="disposal_replacement">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by name, code, or serial no..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="condition_status" class="form-select">
                <option value="">All Conditions</option>
                <option value="fair" {{ request('condition_status')==='fair' ?'selected':'' }}>Fair</option>
                <option value="poor" {{ request('condition_status')==='poor' ?'selected':'' }}>Poor</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Code</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Purchase Date</th>
                <th>Purchase Cost</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
                $disposalAssets = $assets->getCollection()->filter(fn($a) => in_array($a->status, ['disposed']) || in_array($a->condition_status, ['fair','poor']));
            @endphp
            @forelse($disposalAssets as $asset)
            <tr>
                <td><span class="row-num">{{ $loop->iteration }}</span></td>
                <td>
                    @if(!empty($asset->asset_image))
                        <img src="{{ asset('storage/' . $asset->asset_image) }}" class="asset-img" alt="{{ $asset->asset_name }}" onclick="openLightbox(this.src)">
                    @else
                        <span class="asset-img-placeholder"><i class="bi bi-image"></i></span>
                    @endif
                </td>
                <td><span class="code-pill">{{ $asset->asset_code }}</span></td>
                <td>
                    <div class="row-title">{{ $asset->asset_name }}</div>
                    @if($asset->serial_number)<div class="row-sub">S/N: {{ $asset->serial_number }}</div>@endif
                </td>
                <td>{{ $asset->category }}</td>
                <td><span class="badge-pill bp-{{ $asset->status }}">{{ ucfirst(str_replace('_',' ',$asset->status)) }}</span></td>
                <td><span class="badge-pill bp-{{ $asset->condition_status }}">{{ ucfirst($asset->condition_status) }}</span></td>
                <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') : '—' }}</td>
                <td>₱{{ number_format($asset->purchase_cost, 2) }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn-act btn-act-edit" title="Edit"
                            data-bs-toggle="modal" data-bs-target="#editAssetModal"
                            data-id="{{ $asset->id }}"
                            data-asset_code="{{ $asset->asset_code }}"
                            data-asset_name="{{ $asset->asset_name }}"
                            data-serial_number="{{ $asset->serial_number }}"
                            data-category="{{ $asset->category }}"
                            data-location="{{ $asset->location }}"
                            data-status="{{ $asset->status }}"
                            data-condition_status="{{ $asset->condition_status }}"
                            data-purchase_cost="{{ $asset->purchase_cost }}"
                            data-supplier="{{ $asset->supplier }}"
                            data-asset_image="{{ $asset->asset_image }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.logistics1.asset_management.destroy', $asset->id) }}" style="display:inline;" onsubmit="return confirm('Delete this asset?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-act btn-act-delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="10"><div class="empty-state"><i class="bi bi-arrow-repeat"></i><p>No assets flagged for disposal or replacement.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif


{{-- ===== TAB 3: FLEET VEHICLES ===== --}}
@if($activeTab === 'fleet')

<p class="tab-desc">Overview of all fleet vehicles and their current status.</p>

<form method="GET" action="{{ route('admin.logistics1.asset_management.index') }}">
    <input type="hidden" name="tab" value="fleet">
    <div class="filter-bar row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by plate number, type, or model..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="fleet_status" class="form-select">
                <option value="">All Statuses</option>
                <option value="available"   {{ request('fleet_status')==='available'   ?'selected':'' }}>Available</option>
                <option value="in_use"      {{ request('fleet_status')==='in_use'      ?'selected':'' }}>In Use</option>
                <option value="maintenance" {{ request('fleet_status')==='maintenance' ?'selected':'' }}>Maintenance</option>
                <option value="retired"     {{ request('fleet_status')==='retired'     ?'selected':'' }}>Retired</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-filter w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-card">
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Plate Number</th>
                <th>Vehicle Info</th>
                <th>Status</th>
                <th>Last Maintained</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fleet as $vehicle)
            <tr>
                <td><span class="row-num">{{ $loop->iteration + ($fleet->currentPage()-1) * $fleet->perPage() }}</span></td>
                <td><span class="code-pill">{{ $vehicle->plate_number }}</span></td>
                <td>
                    <div class="row-title">
                        {{ $vehicle->vehicle_type ?? 'Vehicle' }}
                        @if(!empty($vehicle->model)) — {{ $vehicle->model }} @endif
                    </div>
                    @if(!empty($vehicle->brand))<div class="row-sub">{{ $vehicle->brand }}</div>@endif
                </td>
                <td>
                    @php
                        $fs = $vehicle->status ?? 'available';
                        $fmap = ['available'=>'bp-available','in_use'=>'bp-in_use','maintenance'=>'bp-maintenance','retired'=>'bp-retired'];
                    @endphp
                    <span class="badge-pill {{ $fmap[$fs] ?? 'bp-inactive' }}">{{ ucfirst(str_replace('_',' ',$fs)) }}</span>
                </td>
                <td>
                    @if(!empty($vehicle->last_maintained_at))
                        {{ \Carbon\Carbon::parse($vehicle->last_maintained_at)->format('M d, Y') }}
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state"><i class="bi bi-truck"></i><p>No fleet vehicles found.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-3">{{ $fleet->withQueryString()->links() }}</div>
@endif


{{-- ADD ASSET MODAL --}}
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.logistics1.asset_management.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-boxes me-2"></i>Add New Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Image Upload --}}
                        <div class="col-12">
                            <label class="form-label">Asset Image</label>
                            <div class="img-upload-wrap" onclick="document.getElementById('asset_image_input').click()">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>Click to upload image (JPG, PNG, WEBP — max 2MB)</span>
                                <img id="imagePreview" src="" alt="Preview" style="width:100%;max-height:140px;object-fit:contain;border-radius:8px;display:none;margin-top:0.5rem;">
                            </div>
                            <input type="file" id="asset_image_input" name="asset_image" accept="image/*" style="display:none;" onchange="previewImage(this, 'imagePreview')">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" name="asset_name" class="form-control" required maxlength="255" placeholder="Enter asset name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" maxlength="255" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category...</option>
                                <option value="Equipment">Equipment</option>
                                <option value="Furniture">Furniture</option>
                                <option value="IT Hardware">IT Hardware</option>
                                <option value="Tools">Tools</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" maxlength="255" placeholder="e.g. Warehouse A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Purchase Cost (₱)</label>
                            <input type="number" name="purchase_cost" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier" class="form-select">
                                <option value="">— Select vendor —</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_name }}">{{ $vendor->vendor_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-ms"><i class="bi bi-check-lg me-1"></i>Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- EDIT ASSET MODAL --}}
<div class="modal fade" id="editAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editAssetForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Image Upload --}}
                        <div class="col-12">
                            <label class="form-label">Asset Image</label>
                            <div class="img-upload-wrap" onclick="document.getElementById('edit_asset_image_input').click()">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>Click to replace image (JPG, PNG, WEBP — max 2MB)</span>
                                <img id="editImagePreview" src="" alt="Preview" style="width:100%;max-height:140px;object-fit:contain;border-radius:8px;display:none;margin-top:0.5rem;">
                            </div>
                            <input type="file" id="edit_asset_image_input" name="asset_image" accept="image/*" style="display:none;" onchange="previewImage(this, 'editImagePreview')">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Asset Code</label>
                            <input type="text" class="form-control" id="edit_asset_code" disabled>
                            <small style="font-size:0.72rem;color:#94a3b8;">Auto-generated, cannot be changed.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" name="asset_name" id="edit_asset_name" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" id="edit_serial_number" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="edit_category" class="form-select" required>
                                <option value="Equipment">Equipment</option>
                                <option value="Furniture">Furniture</option>
                                <option value="IT Hardware">IT Hardware</option>
                                <option value="Tools">Tools</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control" maxlength="255">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit_asset_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Condition <span class="text-danger">*</span></label>
                            <select name="condition_status" id="edit_condition_status" class="form-select" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Purchase Cost (₱)</label>
                            <input type="number" name="purchase_cost" id="edit_purchase_cost" class="form-control" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier" id="edit_supplier" class="form-select">
                                <option value="">— Select vendor —</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->vendor_name }}">{{ $vendor->vendor_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-mc" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-mu"><i class="bi bi-check-lg me-1"></i>Update Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('editAssetModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const form = document.getElementById('editAssetForm');
    form.action = `/admin/logistics1/asset-management/update/${btn.dataset.id}`;
    document.getElementById('edit_asset_code').value       = btn.dataset.asset_code;
    document.getElementById('edit_asset_name').value       = btn.dataset.asset_name;
    document.getElementById('edit_serial_number').value    = btn.dataset.serial_number   ?? '';
    document.getElementById('edit_category').value         = btn.dataset.category;
    document.getElementById('edit_location').value         = btn.dataset.location         ?? '';
    document.getElementById('edit_asset_status').value     = btn.dataset.status;
    document.getElementById('edit_condition_status').value = btn.dataset.condition_status;
    document.getElementById('edit_purchase_cost').value    = btn.dataset.purchase_cost;
    document.getElementById('edit_supplier').value         = btn.dataset.supplier ?? '';

    // Show existing image if any
    const editPreview = document.getElementById('editImagePreview');
    const imgVal = btn.dataset.asset_image ?? '';
    if (imgVal && imgVal !== 'null' && imgVal !== '') {
        editPreview.src = `/storage/${imgVal}`;
        editPreview.style.display = 'block';
    } else {
        editPreview.src = '';
        editPreview.style.display = 'none';
    }
});
</script>

@endsection