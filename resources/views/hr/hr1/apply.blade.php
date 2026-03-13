@extends('layouts.residency_fellowship.app')

@section('content')

<div class="container py-5">

    <h2 class="mb-4">Residency Application</h2>

    <!-- SUCCESS / ERROR MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- VALIDATION ERRORS -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('careers.apply.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">

            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
            </div>

            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
            </div>

            <div class="col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <div class="col-md-6">
                <label>Department</label>
                @php
                    $deptObj = $departments->firstWhere('department_id', $dept);
                    $deptName = $deptObj ? $deptObj->name : 'N/A';
                @endphp
                <input type="text" class="form-control" value="{{ $deptName }}" readonly>
                <input type="hidden" name="department_id" value="{{ $dept }}">
            </div>

            <div class="col-md-6">
                <label>Specialization</label>
                <select name="specialization" class="form-control">
                    <option value="">-- Select Specialization --</option>
                    @foreach($specializations as $s)
                        <option value="{{ $s->specialization_name }}" {{ old('specialization') == $s->specialization_name ? 'selected' : '' }}>
                            {{ $s->specialization_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label>Upload Resume (PDF)</label>
                <input type="file" name="resume" class="form-control" required>
            </div>

            <input type="hidden" name="post_grad_status" value="residency">

            <div class="col-md-12">
                <button class="btn btn-primary">Submit Application</button>
            </div>

        </div>
    </form>

</div>

@endsection