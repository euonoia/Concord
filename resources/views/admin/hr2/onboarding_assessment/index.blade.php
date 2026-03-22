@extends('admin.hr2.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow border-0">
                <div class="card-body text-center p-4">

                    <h4 class="fw-bold mb-3">Applicant Assessment</h4>
                    <p class="text-muted small">Enter Reference ID (e.g. APP-OUG9ABP6)</p>

                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('onboarding.assessment.check') }}">
                        @csrf
                        <input type="text" name="reference_id" class="form-control mb-3"
                               placeholder="Reference ID" required>
                        <button class="btn btn-primary w-100">Continue</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection