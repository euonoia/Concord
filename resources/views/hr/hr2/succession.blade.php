@extends('layouts.dashboard.app')

@section('title', 'Succession Planning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/succession.css') }}">
@endpush

@section('content')
<div class="container p-4 succession-roadmap">
    <div class="header-box">
        <h2>Succession Roadmap</h2>
        <p>Positions where you have been identified as a potential successor.</p>
    </div>

    <div class="succession-list">
        @forelse($nominations as $nomination)
            <div class="nomination-card">
                <div class="accent-bar"></div>
                
                <div class="card-body">
                    <div class="card-main-header">
                        <div class="role-info">
                            <span class="label-text">Target Position</span>
                            <h3 class="position-title">{{ $nomination->target_position_title }}</h3>
                            
                            {{-- Updated "Currently serving as" with Specialization --}}
                            <p class="current-status">
                                Currently serving as: 
                                <strong>
                                    {{ $nomination->current_position_title }} 
                                    @if($nomination->current_specialization)
                                        ({{ $nomination->current_specialization }})
                                    @endif
                                </strong>
                            </p>
                        </div>
                        
                        <div class="readiness-box">
                            <div class="readiness-badge">
                                <span class="badge-label">Readiness Status</span>
                                <span class="badge-value">{{ $nomination->readiness }}</span>
                            </div>
                            <p class="transition-date">
                                Estimated Transition: {{ \Carbon\Carbon::parse($nomination->effective_at)->format('M Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Metrics Grid --}}
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <small class="metric-label">Training Performance</small>
                            {{-- Showing as Percentage based on your HR1 Grade logic --}}
                            <div class="metric-value">{{ number_format($nomination->performance_score, 1) }}%</div>
                            <div class="progress-container">
                                <div class="progress-bar performance" style="width: {{ $nomination->performance_score }}%;"></div>
                            </div>
                        </div>

                        <div class="metric-item">
                            <small class="metric-label">Retention Risk</small>
                            <div class="metric-value risk-{{ strtolower($nomination->retention_risk) }}">
                                {{ $nomination->retention_risk }}
                            </div>
                            <p class="stability-text">Stability Check</p>
                        </div>
                    </div>

                    <div class="development-plan">
                        <small class="plan-label">Succession Development Focus:</small>
                        <p class="plan-text">
                          
                            "{{ $nomination->development_plan ?? 'Your training path for this role is currently being drafted by HR.' }}"
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa fa-info-circle"></i>
                <h3>No Active Nominations</h3>
                <p>You are not currently listed as a successor for any critical positions. Continue to excel in your current role to be identified for future opportunities.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection