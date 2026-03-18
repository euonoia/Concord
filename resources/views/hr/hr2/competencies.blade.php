@extends('layouts.dashboard.app')

@section('content')
<div class="hr2_competency_grades container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            
            {{-- Header Section --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Competency Grades</h4>
                    <p class="text-muted small mb-0">Overview of evaluated skills and performance metrics.</p>
                </div>
                <div class="text-end">
                    <span class="badge-status">Official Record</span>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert custom-alert-success fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Main Table Card --}}
            <div class="modern-card">
                @if($competencies->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open text-light mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No evaluated competencies available at this time.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Competency Name</th>
                                    <th>Description</th>
                                    <th class="text-center">Grade</th>
                                    <th>Validator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($competencies as $comp)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $comp->name }}</span>
                                        </td>
                                        <td class="text-muted small w-50">{{ $comp->description }}</td>
                                        <td class="text-center">
                                            <div class="grade-pill">{{ $comp->total_score }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="small fw-medium">{{ $comp->evaluator_name ?? 'Pending' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</div>
@endsection