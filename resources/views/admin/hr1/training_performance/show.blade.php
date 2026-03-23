@extends('admin.hr1.layouts.app')

@section('title','Employee Training Grade')

@section('content')
<div class="container p-5">

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success fade show mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
    @endif

    <h2 class="mb-4">Training Performance Validation</h2>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-muted mb-1">Standard Grade Calculation</h5>
                <h2 class="display-5 fw-bold mb-0">
                    <span class="{{ $weightedAverage >= 75 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($weightedAverage, 2) }}%
                    </span>
                </h2>
                <p class="text-muted mb-0">
                    Average of {{ $scores->count() }} Competencies 
                    | Status: <strong>{{ $weightedAverage >= 75 ? 'PASSED' : 'FOR REVIEW' }}</strong>
                </p>
            </div>
            <div>
                @if($isValidated)
                    <button class="btn btn-secondary btn-lg px-5 shadow-sm" disabled>
                        <i class="fas fa-check-double me-2"></i> Validated
                    </button>
                @else
                    <form id="validateForm" method="POST" action="{{ route('hr1.training.performance.validate', $employee_id) }}">
                        @csrf
                        <button type="button" onclick="confirmValidation()" class="btn btn-success btn-lg px-5 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> Validate & Store Grade
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle table-bordered shadow-sm">
            <thead class="table-dark text-center">
                <tr>
                    <th>Competency</th>
                    <th>Technical</th>
                    <th>Knowledge</th>
                    <th>Participation</th>
                    <th>Attendance</th>
                    <th class="bg-primary text-white">Grade (0-100)</th>
                    <th>Evaluator</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scores as $score)
                @php
                    $data = $score->decoded_scores ?? [];
                    $ratings = $data['ratings'] ?? $data;
                    $rowGrade = $score->total_score / 4;
                @endphp
                <tr class="text-center">
                    <td class="text-start">
                        <strong>{{ $score->competency_code }}</strong><br>
                        <small class="text-muted">{{ $score->competency_name }}</small>
                    </td>
                    <td>{{ $ratings['technical'] ?? '0' }}</td>
                    <td>{{ $ratings['knowledge'] ?? '0' }}</td>
                    <td>{{ $ratings['participation'] ?? '0' }}</td>
                    <td>{{ $ratings['attendance'] ?? '0' }}</td>
                    <td class="fw-bold fs-5">
                        {{ number_format($rowGrade, 2) }}%
                    </td>
                    <td>{{ $score->evaluator_first_name }} {{ $score->evaluator_last_name }}</td>
                    <td>{{ $score->evaluated_at ? \Carbon\Carbon::parse($score->evaluated_at)->format('M d, Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center p-5 text-muted">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('hr1.training.performance.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-chevron-left me-1"></i> Back to List
        </a>
    </div>
</div>

{{-- SweetAlert2 for Confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmValidation() {
        Swal.fire({
            title: 'Confirm Validation',
            text: "Are you sure you want to store the grade of {{ number_format($weightedAverage, 2) }}% for this employee? This will finalize the HR1 training performance record.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Validate it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('validateForm').submit();
            }
        })
    }
</script>

<style>
    .table th { vertical-align: middle; }
    .display-5 { letter-spacing: -1px; }
    .alert { border-left: 5px solid #198754; }
</style>
@endsection