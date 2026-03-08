@extends('layouts.residency_fellowship.app')

@section('content')

<div class="container py-5">

    <h2 class="mb-4">Residency Application</h2>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('careers.apply.store') }}">
        @csrf

        <div class="row g-3">

            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <!-- Display Selected Department as readonly -->
            <div class="col-md-6">
                <label>Department</label>
                <input type="text" class="form-control" value="{{ $departments->firstWhere('department_id', $dept)->name ?? 'N/A' }}" readonly>
                <input type="hidden" name="department_id" value="{{ $dept }}">
            </div>

            <div class="col-md-6">
                <label>Position</label>
                <select name="position_id" class="form-control">
                    @foreach($positions as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->position_title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label>Specialization</label>
                <select name="specialization" class="form-control">
                    <option value="">-- Select Specialization --</option>
                    @foreach($specializations as $s)
                        <option value="{{ $s->specialization_name }}">
                            {{ $s->specialization_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hidden post_grad_status -->
            <input type="hidden" name="post_grad_status" value="residency">

            <div class="col-md-12">
                <button class="btn btn-primary">
                    Submit Application
                </button>
            </div>

        </div>

    </form>

</div>

@endsection