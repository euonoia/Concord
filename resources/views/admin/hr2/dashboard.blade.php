@extends('admin.hr2.layouts.app')

@section('content')
<div class="hr2-dashboard-wrapper">
    {{-- Decorative Background Elements --}}
    <div class="bg-glow"></div>

    <div class="container-fluid p-0">


        {{-- Stats Grid --}}
        <div class="row g-4 mb-5">
            @php
                $stats = [
                    ['label' => 'Total Workforce', 'value' => $totalEmployees, 'icon' => 'fa-users-cog', 'color' => 'indigo', 'trend' => '8% Up'],
                    ['label' => 'Competencies', 'value' => $activeCompetencies, 'icon' => 'fa-award', 'color' => 'gold', 'trend' => 'Verified'],
                    ['label' => 'Global Units', 'value' => $totalDepartments, 'icon' => 'fa-network-wired', 'color' => 'emerald', 'trend' => 'Stable'],
                    ['label' => 'Requests', 'value' => $pendingEssRequests, 'icon' => 'fa-bolt', 'color' => 'rose', 'trend' => 'Priority'],
                    ['label' => 'Learning Modules', 'value' => $activeLearningModules, 'icon' => 'fa-book', 'color' => 'blue', 'trend' => 'Active'],
                    ['label' => 'Succession Slots', 'value' => $successionPositions, 'icon' => 'fa-chess-king', 'color' => 'purple', 'trend' => 'Open'],
                    ['label' => 'Upcoming Trainings', 'value' => $upcomingTrainings, 'icon' => 'fa-calendar-check', 'color' => 'cyan', 'trend' => '30 Days'],
                    ['label' => 'Recent Completions', 'value' => $recentCompletions, 'icon' => 'fa-check-double', 'color' => 'lime', 'trend' => 'This Month'],
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="stat-card-v2 {{ $stat['color'] }}">
                    <div class="stat-card-inner">
                        <div class="stat-info">
                            <span class="stat-label-v2">{{ $stat['label'] }}</span>
                            <h2 class="stat-number-v2">{{ number_format($stat['value']) }}</h2>
                        </div>
                        <div class="stat-visual">
                            <div class="icon-blob">
                                <i class="fas {{ $stat['icon'] }}"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer-v2">
                        <span class="trend-tag"><i class="fas fa-chart-line me-1"></i> {{ $stat['trend'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4">
            {{-- Main Table Area --}}
            <div class="col-lg-8">
                <div class="glass-card main-table-card">
                    <div class="card-header-v2">
                        <div>
                            <h4 class="m-0 font-weight-bold">Talent Performance Matrix</h4>
                            <p class="text-muted small m-0">Top performing internal staff based on recent evaluations</p>
                        </div>
                        <button class="btn-premium-sm">Full Analytics <i class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Talent Entity</th>
                                    <th>Total Score</th>
                                    <th>Weighted Average</th>
                                    <th>Performance Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $performer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-container me-3">
                                                <div class="avatar-ring"></div>
                                                <span class="avatar-text">{{ substr($performer->first_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="emp-name-v2">{{ $performer->first_name }} {{ $performer->last_name }}</div>
                                                <div class="emp-role">Senior Associate</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($performer->total_score, 2) }}</td>
                                    <td>{{ number_format($performer->weighted_average, 2) }}%</td>
                                    <td>
                                        @php
                                            $grade = $performer->weighted_average;
                                            if ($grade >= 90) $status = 'Excellent';
                                            elseif ($grade >= 75) $status = 'Good';
                                            elseif ($grade >= 60) $status = 'Satisfactory';
                                            else $status = 'Needs Improvement';
                                        @endphp
                                        <span class="badge-dot {{ $grade >= 90 ? 'bg-success' : ($grade >= 75 ? 'bg-primary' : ($grade >= 60 ? 'bg-warning' : 'bg-danger')) }}"></span>
                                        {{ $status }}
                                    </td>
                                  
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar Widgets --}}
            <div class="col-lg-4">
                {{-- Quick Nav --}}
                <div class="glass-card mb-4 p-4">
                    <h5 class="section-title-v2">Executive Navigation</h5>
                    <div class="nav-grid">
                        <a href="{{ route('competencies.index') }}" class="nav-item-v2">
                            <i class="fas fa-layer-group"></i>
                            <span>Skills</span>
                        </a>
                        <a href="{{ route('hr2.training') }}" class="nav-item-v2">
                            <i class="fas fa-terminal"></i>
                            <span>Training</span>
                        </a>
                        <a href="{{ route('learning.index') }}" class="nav-item-v2">
                            <i class="fas fa-university"></i>
                            <span>LMS</span>
                        </a>
                        <a href="{{ route('succession.index') }}" class="nav-item-v2">
                            <i class="fas fa-chess-king"></i>
                            <span>Succession</span>
                        </a>
                    </div>
                </div>

                {{-- Pulse Widget --}}
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">Operational Pulse</h5>
                    <div class="pulse-stack mt-4">
                        <div class="pulse-card-v2">
                            <div class="pulse-icon-v2"><i class="fas fa-file-signature"></i></div>
                            <div class="pulse-content-v2">
                                <h6>ESS Requests</h6>
                                <span class="pulse-meta">{{ $pendingEssRequests }} requires review</span>
                            </div>
                            <div class="pulse-badge-v2">Active</div>
                        </div>
                        <div class="pulse-card-v2">
                            <div class="pulse-icon-v2"><i class="fas fa-microphone-alt"></i></div>
                            <div class="pulse-content-v2">
                                <h6>Live Training</h6>
                                <span class="pulse-meta">{{ $upcomingTrainings }} sessions live</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Metrics Row --}}
        <div class="row g-4 mb-5">
            {{-- Training Completion Metrics --}}
            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">Training Analytics</h5>
                    <div class="metric-display mt-4">
                        <div class="metric-item mb-3">
                            <div class="metric-label">Completion Rate</div>
                            <div class="metric-value">{{ $trainingCompletionRate }}%</div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $trainingCompletionRate }}%"></div>
                            </div>
                        </div>
                        <div class="metric-item mb-3">
                            <div class="metric-label">Avg Performance Score</div>
                            <div class="metric-value">{{ number_format($avgPerformanceScore ?? 0, 2) }}</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-label">Total Evaluations</div>
                            <div class="metric-value">{{ $totalEvaluations ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Distribution --}}
            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">Performance Distribution</h5>
                    <div class="grade-distribution mt-4">
                        @forelse($performanceDistribution as $perf)
                        <div class="grade-item mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="grade-label">{{ $perf->grade }}</span>
                                <span class="grade-count">{{ $perf->employee_count }} staff</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" style="width: {{ ($perf->employee_count / ($performanceDistribution->sum('employee_count') ?? 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No performance data available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ESS Status Breakdown --}}
            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">ESS Request Status</h5>
                    <div class="status-breakdown mt-4">
                        @forelse($essRequestsByStatus as $status)
                        <div class="status-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-name text-capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                <span class="badge badge-primary">{{ $status->count }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No requests</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Department & Succession Row --}}
        <div class="row g-4 mb-5">
            {{-- Top Departments --}}
            <div class="col-lg-6">
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">Department Workforce Breakdown</h5>
                    <div class="dept-list mt-4">
                        @forelse($departmentMetrics as $dept)
                        <div class="dept-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="dept-name">{{ $dept->department_name ?? 'Unassigned' }}</span>
                                <span class="emp-badge">{{ $dept->emp_count }} employees</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ ($dept->emp_count / ($departmentMetrics->max('emp_count') ?? 1)) * 100 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No department data</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Succession Pipeline --}}
            <div class="col-lg-6">
                <div class="glass-card p-4">
                    <h5 class="section-title-v2">Succession Planning Pipeline</h5>
                    <div class="succession-overview mt-4">
                        <div class="pipeline-card mb-3">
                            <div class="pipeline-header">
                                <i class="fas fa-chess-king text-primary"></i>
                                <span>Open Positions</span>
                            </div>
                            <div class="pipeline-value">{{ $successionPositions }}</div>
                        </div>
                        <div class="pipeline-card">
                            <div class="pipeline-header">
                                <i class="fas fa-user-tie text-success"></i>
                                <span>Identified Candidates</span>
                            </div>
                            <div class="pipeline-value">{{ $successionCandidates }}</div>
                        </div>
                        <div class="pipeline-health">
                            <span class="health-label">Pipeline Health:</span>
                            <span class="health-indicator {{ $successionCandidates >= $successionPositions ? 'strong' : ($successionCandidates > 0 ? 'moderate' : 'weak') }}">
                                {{ $successionCandidates >= $successionPositions ? 'Strong' : ($successionCandidates > 0 ? 'Moderate' : 'Weak') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Metric Display Styles */
.metric-display {
    padding: 0.5rem 0;
}

.metric-item {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 8px;
}

.metric-label {
    font-size: 0.875rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

/* Grade Distribution Styles */
.grade-distribution .grade-item {
    padding: 0.75rem 0;
}

.grade-label {
    font-weight: 600;
    font-size: 0.95rem;
}

.grade-count {
    font-size: 0.85rem;
    color: #666;
}

/* Status Breakdown Styles */
.status-breakdown .status-item {
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 6px;
}

.status-name {
    font-weight: 500;
}

/* Department List Styles */
.dept-list .dept-item {
    padding: 0.75rem;
}

.dept-name {
    font-weight: 600;
    font-size: 0.95rem;
}

.emp-badge {
    background: rgba(99, 102, 241, 0.1);
    color: #4f46e5;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Succession Pipeline Styles */
.succession-overview {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.pipeline-card {
    padding: 1.25rem;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 8px;
    border-left: 4px solid #4f46e5;
}

.pipeline-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
    font-size: 0.95rem;
}

.pipeline-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
}

.pipeline-health {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.health-label {
    font-weight: 600;
    font-size: 0.9rem;
}

.health-indicator {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
}

.health-indicator.strong {
    background: #dcfce7;
    color: #166534;
}

.health-indicator.moderate {
    background: #fef3c7;
    color: #92400e;
}

.health-indicator.weak {
    background: #fee2e2;
    color: #991b1b;
}
</style>
@endsection