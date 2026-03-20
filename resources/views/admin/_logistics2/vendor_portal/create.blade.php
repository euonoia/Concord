@extends('admin._logistics2.layouts.app')

@section('content')

<div class="L2_portal_vendor">
    <div class="vendor-card">
        <div class="header-strip">
            <h5 class="mb-0">Vendor </h5>
        </div>

        <form action="{{ route('admin.logistics2.vendor.portal.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="section-title">Primary Identification</div>
                
                <div class="col-3">
                    <label class="form-label text-info">Vendor Code</label>
                    <input type="text" class="form-control readonly-box" value="{{ $vendorCode }}" readonly>
                    <input type="hidden" name="vendor_code" value="{{ $vendorCode }}">
                </div>
                
                <div class="col-6">
                    <label class="form-label">Full Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="vendor_name" class="form-control" required placeholder="Legal Entity Name">
                </div>

                <div class="col-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="" disabled selected>-- Select --</option>
                        <option value="Pharmaceutical Supplier">Pharmaceutical</option>
                        <option value="Medical Equipment Supplier">Medical Equipment</option>
                        <option value="Laboratory Supplier">Laboratory</option>
                        <option value="Consumables Supplier">Consumables</option>
                        <option value="General Supplier">General</option>
                    </select>
                </div>

                <div class="col-4">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="section-title">Logistics & Compliance</div>
                
                <div class="col-8">
                    <label class="form-label">Registered Office Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="col-4">
                    <label class="form-label">Tax ID (TIN)</label>
                    <input type="text" name="tax_id" class="form-control">
                </div>

                <div class="col-3">
                    <label class="form-label">Business Permit</label>
                    <input type="text" name="business_permit" class="form-control">
                </div>
                <div class="col-3">
                    <label class="form-label text-success">Accreditation Date</label>
                    <input type="date" name="accreditation_date" class="form-control" style="border-left: 3px solid var(--success);">
                </div>
                <div class="col-3">
                    <label class="form-label text-danger">Expiry Date</label>
                    <input type="date" name="accreditation_expiry" class="form-control" style="border-left: 3px solid var(--danger);">
                </div>
                <div class="col-3">
                    <label class="form-label">Internal Remarks</label>
                    <input type="text" name="notes" class="form-control">
                </div>

                <div class="col-12 text-end" style="padding-top: var(--space-md);">
                    <hr style="border: 0; border-top: 1px solid var(--neutral-200); margin-bottom: var(--space-md);">
                    <button type="button" class="btn-link" style="margin-right: var(--space-md); color: var(--text-light); border:none; background:none;" onclick="history.back()">Discard</button>
                    <button type="submit" class="btn-save shadow-md">
                        Commit Record
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection