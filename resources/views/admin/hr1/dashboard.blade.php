@extends('admin.hr1.layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="hr1-premium-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="text-white">HR1 Administration</h3>
            <p class="text-white">Welcome back, <strong>{{ Auth::user()->username }}</strong>. Here is your recruitment and hiring summary.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
            <i class="bi bi-clock-history me-2 text-white"></i>
            <span class="text-white small fw-bold">{{ now()->format('l, M d, Y') }}</span>
        </div>
    </div>

    {{-- Metrics Row --}}
    <div class="hr1-metrics-container">
        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-accent);">
            <div class="hr1-metric-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="hr1-metric-label">Total<br>Applicants</div>
            <div class="hr1-metric-value">{{ $totalApplicants }}</div>
        </div>

        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-success);">
            <div class="hr1-metric-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="hr1-metric-label">Accepted<br>Hires</div>
            <div class="hr1-metric-value">{{ $acceptedCount }}</div>
        </div>

        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-danger);">
            <div class="hr1-metric-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-person-x-fill"></i>
            </div>
            <div class="hr1-metric-label">Rejected<br>Apps</div>
            <div class="hr1-metric-value">{{ $rejectedCount }}</div>
        </div>

        <div class="hr1-metric-card" style="border-top: 5px solid var(--hr1-warning);">
            <div class="hr1-metric-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div class="hr1-metric-label">Active Job<br>Postings</div>
            <div class="hr1-metric-value">{{ $activeJobs }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4" style="margin-bottom: 30px !important;">
        <div class="col-lg-7">
            <div class="hr1-premium-table-card p-4" style="height: 380px;">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-briefcase me-2 text-primary"></i>Applications per Department</h6>
                <div style="height: 280px;">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="hr1-premium-table-card p-4" style="height: 380px;">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Distribution</h6>
                <div style="height: 280px; display: flex; justify-content: center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Table --}}
    <div class="hr1-premium-table-card">
        <div class="hr1-table-header">
            <h6>Recent Applications</h6>
            <a href="{{ route('hr1.applicants.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill" style="font-size: 0.75rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table hr1-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Department</th>
                        <th>Track</th>
                        <th>Status</th>
                        <th>Applied Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplicants as $applicant)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $applicant->first_name }} {{ $applicant->last_name }}</div>
                            <div class="small text-muted">{{ $applicant->email }}</div>
                        </td>
                        <td>
                             <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.7rem;">{{ $applicant->department_name }}</span>
                        </td>
                        <td>
                             <span class="text-capitalize small fw-semibold">{{ str_replace('_', ' ', $applicant->post_grad_status) }}</span>
                        </td>
                        <td>
                            @php
                                $statusBadge = match($applicant->application_status) {
                                    'accepted', 'onboarded' => 'hr1-badge-success',
                                    'rejected' => 'hr1-badge-danger',
                                    'interview' => 'hr1-badge-info',
                                    'under_review' => 'hr1-badge-warning',
                                    default => 'hr1-badge-primary',
                                };
                            @endphp
                            <span class="hr1-badge {{ $statusBadge }}">{{ ucfirst(str_replace('_', ' ', $applicant->application_status)) }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ \Carbon\Carbon::parse($applicant->created_at)->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No recent applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Department Chart
    const deptCtx = document.getElementById('deptChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($deptCounts->pluck('dept_name')) !!},
            datasets: [{
                label: 'Applicants',
                data: {!! json_encode($deptCounts->pluck('total')) !!},
                backgroundColor: '#0d6efd',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusCounts->pluck('application_status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))) !!},
            datasets: [{
                data: {!! json_encode($statusCounts->pluck('total')) !!},
                backgroundColor: ['#0dcaf0', '#0d6efd', '#ffc107', '#198754', '#dc3545', '#6c757d'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
            },
            cutout: '70%'
        }
    });
</script>
@endsection