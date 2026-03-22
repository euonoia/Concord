@extends('admin.hr1.layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dash-gradient-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }
    .metric-card {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: none;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 150px;
        height: 150px;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    .metric-card:hover {
        transform: translateY(-5px);
    }
    .metric-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 12px;
    }
    .metric-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #8a90a0;
        letter-spacing: 0.5px;
    }
    .metric-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1.2;
    }
    .chart-container {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        height: 350px;
    }
    .table-card {
        background: #fff;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fcfcfc;
    }
    .recent-table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8a90a0;
        padding: 12px 20px;
        background: #f8f9fa;
        border: none;
    }
    .recent-table td {
        padding: 12px 20px;
        border: none;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.85rem;
    }
    .badge-status {
        font-size: 0.65rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
    }
</style>

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="dash-gradient-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 15px;">
        <div>
            <h3 class="fw-bold mb-1 text-white">HR1 Administration</h3>
            <p class="mb-0 text-white" style="opacity: 0.85; font-size: 0.9rem;">Welcome back, <strong>{{ Auth::user()->username }}</strong>. Here is your recruitment and hiring summary.</p>
        </div>
        <div style="background: rgba(255,255,255,0.15); padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(5px);">
            <i class="bi bi-clock-history me-2 text-white"></i>
            <span class="text-white small fw-bold">{{ now()->format('l, M d, Y') }}</span>
        </div>
    </div>

    {{-- Metrics Row --}}
    <div style="display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; margin-top: 25px !important; margin-bottom: 35px !important; gap: 20px !important;">
        <div class="metric-card" style="border-top: 5px solid #0d6efd; margin: 0 !important;">
            <div class="metric-icon bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Total<br>Applicants</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $totalApplicants }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #198754; margin: 0 !important;">
            <div class="metric-icon bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Accepted<br>Hires</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $acceptedCount }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #dc3545; margin: 0 !important;">
            <div class="metric-icon bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-person-x-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Rejected<br>Apps</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $rejectedCount }}</div>
        </div>

        <div class="metric-card" style="border-top: 5px solid #ffc107; margin: 0 !important;">
            <div class="metric-icon bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px; font-size: 1rem; margin-bottom: 10px;">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div class="metric-label" style="font-size: 0.65rem; line-height: 1.2; margin-bottom: 5px;">Active Job<br>Postings</div>
            <div class="metric-value" style="font-size: 1.5rem;">{{ $activeJobs }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4" style="margin-bottom: 24px !important;">
        <div class="col-lg-7">
            <div class="chart-container">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-briefcase me-2 text-primary"></i>Applications per Department</h6>
                <div style="height: 280px;">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="chart-container">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart me-2 text-primary"></i>Application Status Distribution</h6>
                <div style="height: 280px; display: flex; justify-content: center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Table --}}
    <div class="table-card" style="margin-bottom: 30px !important;">
        <div class="table-card-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Recent Applications</h6>
            <a href="{{ route('hr1.applicants.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill" style="font-size: 0.75rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table recent-table align-middle mb-0">
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
                                $statusClass = match($applicant->application_status) {
                                    'accepted', 'onboarded' => 'bg-success bg-opacity-10 text-success',
                                    'rejected' => 'bg-danger bg-opacity-10 text-danger',
                                    'interview' => 'bg-info bg-opacity-10 text-info',
                                    'under_review' => 'bg-warning bg-opacity-10 text-warning',
                                    default => 'bg-secondary bg-opacity-10 text-secondary',
                                };
                            @endphp
                            <span class="badge-status {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $applicant->application_status)) }}</span>
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
                backgroundColor: [
                    '#0dcaf0', // info
                    '#0d6efd', // primary
                    '#ffc107', // warning
                    '#198754', // success
                    '#dc3545', // danger
                    '#6c757d'  // secondary
                ],
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