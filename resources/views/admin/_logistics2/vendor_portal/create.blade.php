@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">

<div class="card p-4" style="background:white; border-radius:10px;">

<h3 style="margin-bottom:20px;">
    <i class="bi bi-person-plus"></i> Create Vendor
</h3>

<form action="{{ route('admin.logistics2.vendor.portal.store') }}" method="POST">
@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label>Vendor Code</label>
<input type="hidden" class="form-control" value="{{ $vendorCode }}" readonly>
</div>

<div class="col-md-6 mb-3">
<label>Vendor Name</label>
<input type="text" name="vendor_name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Contact Person</label>
<input type="text" name="contact_person" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Category</label>
<select name="category" class="form-control" required>
    <option value="" disabled selected>-- Select Category --</option>

    <option value="Pharmaceutical Supplier">Pharmaceutical Supplier</option>
    <option value="Medical Equipment Supplier">Medical Equipment Supplier</option>
    <option value="Laboratory Supplier">Laboratory Supplier</option>
    <option value="Consumables Supplier">Consumables Supplier</option>
    <option value="Food / Diet Supplier">Food / Diet Supplier</option>
    <option value="Maintenance / Technical Supplier">Maintenance / Technical Supplier</option>
    <option value="Logistics / Transport Supplier">Logistics / Transport Supplier</option>
    <option value="IT / Systems Supplier">IT / Systems Supplier</option>
    <option value="General Supplier">General Supplier</option>
</select>
</div>
<div class="col-md-12 mb-3">
<label>Address</label>
<textarea name="address" class="form-control"></textarea>
</div>

<div class="col-md-6 mb-3">
<label>Tax ID</label>
<input type="text" name="tax_id" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Business Permit</label>
<input type="text" name="business_permit" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Accreditation Date</label>
<input type="date" name="accreditation_date" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Accreditation Expiry</label>
<input type="date" name="accreditation_expiry" class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Notes</label>
<textarea name="notes" class="form-control"></textarea>
</div>

</div>

<button class="btn btn-success">
    <i class="bi bi-check-circle"></i> Save Vendor
</button>

</form>

</div>
</div>
@endsection