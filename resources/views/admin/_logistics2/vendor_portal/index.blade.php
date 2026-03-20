@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card p-4" style="background:white; border-radius:10px;">
        
        <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
            <h3><i class="bi bi-buildings"></i> Vendor Portal</h3>

            <a href="{{ route('admin.logistics2.vendor.portal.create') }}" 
               class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Vendor
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th>Vendor Code</th>
                        <th>Vendor Name</th>
                        <th>Category</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th width="160">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->vendor_code }}</td>
                            <td>{{ $vendor->vendor_name }}</td>
                            <td>{{ $vendor->category }}</td>
                            <td>{{ $vendor->contact_person }}</td>
                            <td>{{ $vendor->phone }}</td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>

                            <td>
                                <a href="{{ route('admin.logistics2.vendor.portal.edit', $vendor->id) }}" 
                                   class="btn btn-sm btn-warning">
                                   Edit
                                </a>

                                <form action="{{ route('admin.logistics2.vendor.portal.delete', $vendor->id) }}" 
                                      method="POST" 
                                      style="display:inline;">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No vendors yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection