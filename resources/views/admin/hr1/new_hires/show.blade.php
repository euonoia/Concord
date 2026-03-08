@extends('admin.hr1.layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">New Hire Details</h2>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Full Name:</strong> {{ $newHire->first_name }} {{ $newHire->last_name }}</div>
                <div class="col-md-6"><strong>Email:</strong> {{ $newHire->email }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Phone:</strong> {{ $newHire->phone }}</div>
                <div class="col-md-6"><strong>Department:</strong> {{ $newHire->department_name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Position:</strong> {{ $newHire->position_title }}</div>
                <div class="col-md-6"><strong>Post Grad Status:</strong> {{ ucfirst($newHire->post_grad_status) }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($newHire->status) }}</div>
                <div class="col-md-6">
                    @if($newHire->resume_path)
                        <a href="{{ route('hr1.newhires.download', $newHire->id) }}" class="btn btn-success" target="_blank">Download Resume</a>
                    @else
                        <span class="text-muted">No Resume</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('hr1.newhires.index') }}" class="btn btn-secondary">Back to New Hires</a>
</div>
@endsection