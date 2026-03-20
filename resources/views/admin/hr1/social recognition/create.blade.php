@extends('admin.hr1.layouts.app')

@section('content')
<style>
    .dash-gradient-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .form-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: none;
    }
    .form-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a90a0;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
</style>

<div class="container-fluid py-4">
    <div class="dash-gradient-header mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.hr1.recognition.index') }}" class="btn btn-link text-white p-0 me-3 fs-4">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-1 text-white">Create Recognition Post</h3>
                <p class="mb-0 text-white" style="opacity: 0.85; font-size: 0.9rem;">Announce a new appreciation or recognition.</p>
            </div>
        </div>
    </div>

    <div class="card form-card p-4">
        <form action="{{ route('admin.hr1.recognition.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Enter recognition title..." required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recognize Employee</label>
                        <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}" {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                    [{{ $employee->employee_id }}] {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="8" placeholder="Write your appreciation message here..." required>{{ old('content') }}</textarea>
                        @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-4">
                        <label class="form-label">Recognition Image (Optional)</label>
                        <div class="border rounded-3 p-4 text-center bg-light border-dashed" style="border-style: dashed !important; border-width: 2px !important;">
                            <i class="bi bi-cloud-upload fs-1 text-muted d-block mb-2"></i>
                            <input type="file" name="image" class="form-control form-control-sm @error('image') is-invalid @enderror">
                            <p class="mt-3 mb-0 small text-muted">Max size: 2MB (JPG, PNG)</p>
                        </div>
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-switch p-3 border rounded-3 bg-white shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="award_bonus" id="award_bonus" value="1">
                            <label class="form-check-label fw-bold text-primary" for="award_bonus">
                                <i class="bi bi-cash-stack me-1"></i> Award Recognition Bonus
                            </label>
                            <p class="small text-muted mb-0 mt-1" style="font-size: 0.7rem;">Sends 500.00 bonus to HR4 Compensation Planning.</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <button type="submit" class="btn btn-primary w-100 mb-2 fw-bold py-2 shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-check-circle-fill me-2"></i>Publish Recognition
                        </button>
                        <a href="{{ route('admin.hr1.recognition.index') }}" class="btn btn-light border w-100 fw-bold py-2 shadow-sm" style="border-radius: 8px;">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
