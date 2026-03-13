@extends('admin.hr1.layouts.app')

@section('title','Employee Training Grade')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">Performance Validation</h3>
            <p class="text-white">Reviewing scores for Employee <strong>#{{ $employee_id }}</strong></p>
        </div>
        <div>
            <a href="{{ route('hr1.training.performance.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-chevron-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show hr1-mb-5" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Score Summary Card --}}
    <div class="hr1-premium-table-card hr1-mb-5 border-start border-primary border-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 20px;">
            <div>
                <div class="text-muted small fw-bold text-uppercase mb-1">Standard Weighted Average</div>
                <h2 class="display-5 fw-bold mb-1">
                    <span class="{{ $weightedAverage >= 75 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($weightedAverage, 2) }}%
                    </span>
                </h2>
                <p class="text-muted small mb-0">
                    Aggregation of {{ $scores->count() }} Competencies 
                    | Final Status: <strong class="{{ $weightedAverage >= 75 ? 'text-success' : 'text-danger' }}">{{ $weightedAverage >= 75 ? 'PASSED' : 'FOR REVIEW' }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center">
                @if($isValidated)
                    <div class="text-center px-5 border py-2 bg-light rounded-pill">
                        <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                        <span class="fw-bold text-success">GRADES VALIDATED</span>
                    </div>
                @else
                    <form id="validateForm" method="POST" action="{{ route('hr1.training.performance.validate', $employee_id) }}">
                        @csrf
                        <button type="button" onclick="confirmValidation()" class="btn btn-success rounded-pill px-5 py-2 shadow-sm fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i> Validate & Store Grade
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Detailed Metrics Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6>Competency Performance Breakdown</h6>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0">
                <thead>
                    <tr class="text-center">
                        <th class="text-start">Competency</th>
                        <th>Technical</th>
                        <th>Knowledge</th>
                        <th>Participation</th>
                        <th>Attendance</th>
                        <th class="bg-primary text-white">Final Score</th>
                        <th>Evaluated By</th>
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
                            <div class="fw-bold text-dark">{{ $score->competency_code }}</div>
                            <div class="small text-muted">{{ $score->competency_name }}</div>
                        </td>
                        <td class="small">{{ $ratings['technical'] ?? '0' }}</td>
                        <td class="small">{{ $ratings['knowledge'] ?? '0' }}</td>
                        <td class="small">{{ $ratings['participation'] ?? '0' }}</td>
                        <td class="small">{{ $ratings['attendance'] ?? '0' }}</td>
                        <td class="fw-bold {{ $rowGrade >= 75 ? 'text-success' : 'text-danger' }}" style="font-size: 1rem;">
                            {{ number_format($rowGrade, 2) }}%
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $score->evaluator_first_name }} {{ $score->evaluator_last_name }}</div>
                        </td>
                        <td class="small text-muted">
                            {{ $score->evaluated_at ? \Carbon\Carbon::parse($score->evaluated_at)->format('M d, Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">No competency data available for this employee.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- SweetAlert2 for Confirmation --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmValidation() {
        Swal.fire({
            title: 'Confirm Validation',
            html: `You are about to finalize a grade of <strong>{{ number_format($weightedAverage, 2) }}%</strong>.<br><small class="text-muted">This action stores the record permanently.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Validate it!',
            cancelButtonText: 'Review Again',
            customClass: {
                confirmButton: 'rounded-pill px-4',
                cancelButton: 'rounded-pill px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('validateForm').submit();
            }
        })
    }
</script>
@endsection