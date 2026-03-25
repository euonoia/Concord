@extends('admin.hr4.layouts.app')

@section('title', 'HR KPI Dashboard - Analytics')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:           #eef3f7;
        --c-surface:      #ffffff;
        --c-border:       #d4e3ee;
        --c-teal:         #0a7c6e;
        --c-teal-light:   #e4f4f1;
        --c-teal-mid:     #b8e0da;
        --c-blue:         #1a5f8a;
        --c-blue-light:   #e8f2f9;
        --c-green:        #1a7a52;
        --c-green-light:  #e4f5ed;
        --c-red:          #be123c;
        --c-red-light:    #fce7ef;
        --c-yellow:       #92400e;
        --c-yellow-light: #fefce8;
        --c-text:         #1b2b3a;
        --c-muted:        #5c798e;
        --c-line:         #dde8f0;
    }

    * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    body { background: var(--c-bg); min-height: 100vh; padding: 2rem; }

    .analytics-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--c-line);
    }

    .analytics-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        font-weight: 600;
    }

    .analytics-header h1 em {
        color: var(--c-teal);
        font-style: italic;
    }

    /* KPI Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(10, 50, 80, 0.07);
        transition: all 0.3s ease;
    }

    .kpi-card:hover {
        box-shadow: 0 8px 20px rgba(10, 124, 110, 0.12);
        transform: translateY(-2px);
    }

    .kpi-card.accent { border-left: 4px solid var(--c-teal); }
    .kpi-card.accent2 { border-left: 4px solid var(--c-blue); }
    .kpi-card.accent3 { border-left: 4px solid var(--c-green); }
    .kpi-card.accent4 { border-left: 4px solid var(--c-yellow); }

    .kpi-label {
        font-size: 0.85rem;
        color: var(--c-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--c-text);
        margin-bottom: 0.5rem;
    }

    .kpi-change {
        font-size: 0.8rem;
        color: var(--c-green);
    }

    .kpi-change.negative {
        color: var(--c-red);
    }

    /* Dashboard Section */
    .dashboard-section {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 4px rgba(10, 50, 80, 0.07);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--c-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--c-teal);
        font-size: 1.2rem;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 1.5rem;
    }

    /* Tables */
    .analytics-table {
        width: 100%;
        border-collapse: collapse;
    }

    .analytics-table thead {
        background: #f5f5f5;
        border-bottom: 2px solid var(--c-line);
    }

    .analytics-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--c-text);
        font-size: 0.9rem;
    }

    .analytics-table td {
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--c-line);
        color: var(--c-text);
    }

    .analytics-table tbody tr:hover {
        background: #fafafa;
    }

    /* Progress Bar */
    .progress-bar {
        height: 6px;
        background: #e0e0e0;
        border-radius: 3px;
        overflow: hidden;
        margin: 0.5rem 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--c-teal), var(--c-teal-mid));
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    /* Department Health Indicator */
    .health-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 0.75rem;
    }

    .health-score {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .health-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        font-size: 0.85rem;
    }

    .health-circle.healthy { background: var(--c-green); }
    .health-circle.fair { background: var(--c-yellow); }
    .health-circle.poor { background: var(--c-red); }

    .health-info {
        display: flex;
        flex-direction: column;
    }

    .health-name {
        font-weight: 600;
        color: var(--c-text);
    }

    .health-count {
        font-size: 0.85rem;
        color: var(--c-muted);
    }

    /* Two Column Layout */
    .two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1000px) {
        .two-column {
            grid-template-columns: 1fr;
        }
    }

    /* New Hires List */
    .hire-item {
        padding: 1rem;
        border-bottom: 1px solid var(--c-line);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .hire-item:last-child {
        border-bottom: none;
    }

    .hire-info {
        flex: 1;
    }

    .hire-name {
        font-weight: 600;
        color: var(--c-text);
        margin-bottom: 0.25rem;
    }

    .hire-meta {
        font-size: 0.85rem;
        color: var(--c-muted);
    }

    .hire-days {
        background: var(--c-teal-light);
        color: var(--c-teal);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
    }
</style>

<div class="analytics-container">
    <div class="analytics-header">
        <h1>HR KPI <em>Dashboard</em></h1>
        <div style="font-size: 0.9rem; color: var(--c-muted);">
            Last updated: {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card accent">
            <div class="kpi-label">Total Headcount</div>
            <div class="kpi-value">{{ $dashboardData['totalHeadcount'] }}</div>
            <div class="kpi-change">({{ $dashboardData['activeCount'] }} active)</div>
        </div>

        <div class="kpi-card accent2">
            <div class="kpi-label">New Hires (MTD)</div>
            <div class="kpi-value">{{ $dashboardData['newHiresMTD'] }}</div>
            <div class="kpi-change">YTD: {{ $dashboardData['newHiresYTD'] }}</div>
        </div>

        <div class="kpi-card accent3">
            <div class="kpi-label">Vacant Positions</div>
            <div class="kpi-value">{{ $dashboardData['vacantPositions'] }}</div>
            <div class="kpi-change">{{ $dashboardData['vacancyRate'] }}% vacancy rate</div>
        </div>

        <div class="kpi-card accent4">
            <div class="kpi-label">Turnover Rate</div>
            <div class="kpi-value">{{ $dashboardData['turnoverRate'] }}%</div>
            <div class="kpi-change">{{ $dashboardData['employeesLeftMTD'] }} left this month</div>
        </div>

        <div class="kpi-card accent">
            <div class="kpi-label">Avg Tenure</div>
            <div class="kpi-value">{{ $dashboardData['avgTenure'] }} yrs</div>
            <div class="kpi-change">Employee experience</div>
        </div>

        <div class="kpi-card accent2">
            <div class="kpi-label">Promotions (MTD)</div>
            <div class="kpi-value">{{ $dashboardData['promotionsMTD'] }}</div>
            <div class="kpi-change">Professional growth</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="two-column">
        <!-- Headcount Trends -->
        <div class="dashboard-section">
            <div class="section-title">
                <i class="bi bi-graph-up"></i> Headcount Trends (12 Months)
            </div>
            <div class="chart-container">
                <canvas id="headcountChart"></canvas>
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="dashboard-section">
            <div class="section-title">
                <i class="bi bi-pie-chart"></i> Department Distribution
            </div>
            <div class="chart-container">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Health Scores -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-hospital"></i> Department Health Scores
        </div>
        <div id="healthScoresContainer">
            <p style="text-align: center; color: var(--c-muted);">Loading...</p>
        </div>
    </div>

    <!-- New Hires -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-person-check"></i> New Hires (Last 30 Days)
        </div>
        @if($newHires->count() > 0)
            <div>
                @foreach($newHires as $hire)
                    <div class="hire-item">
                        <div class="hire-info">
                            <div class="hire-name">{{ $hire['name'] }}</div>
                            <div class="hire-meta">
                                {{ $hire['position'] }} • {{ $hire['department'] }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.9rem; color: var(--c-muted); margin-bottom: 0.5rem;">{{ $hire['joinDate'] }}</div>
                            <div class="hire-days">{{ $hire['daysEmployed'] }} days</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: var(--c-muted); padding: 2rem;">No new hires in the last 30 days.</p>
        @endif
    </div>

    <!-- Attrition Analysis -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-exclamation-triangle"></i> Attrition Analysis
        </div>
        <div class="two-column">
            <div>
                <table class="analytics-table">
                    <thead>
                        <tr>
                            <th>Reason</th>
                            <th>Count</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attritionData as $data)
                            <tr>
                                <td>{{ $data['reason'] }}</td>
                                <td style="font-weight: 600;">{{ $data['count'] }}</td>
                                <td>{{ $data['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <div class="chart-container">
                    <canvas id="attritionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Headcount Trends Chart
    const headcountCtx = document.getElementById('headcountChart').getContext('2d');
    const headcountTrends = @json($trends);
    const headcountLabels = Object.keys(headcountTrends);
    const headcountData = Object.values(headcountTrends);

    new Chart(headcountCtx, {
        type: 'line',
        data: {
            labels: headcountLabels,
            datasets: [{
                label: 'Active Employees',
                data: headcountData,
                borderColor: '#0a7c6e',
                backgroundColor: 'rgba(10, 124, 110, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0a7c6e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Department Distribution Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    const departments = @json($departmentBreakdown);
    const deptLabels = departments.map(d => d.department);
    const deptData = departments.map(d => d.count);
    const deptColors = ['#0a7c6e', '#1a5f8a', '#1a7a52', '#92400e', '#be123c', '#0066cc'];

    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: deptLabels,
            datasets: [{
                data: deptData,
                backgroundColor: deptColors.slice(0, deptData.length),
                borderColor: '#fff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 12 } }
                }
            }
        }
    });

    // Attrition Chart
    const attritionCtx = document.getElementById('attritionChart').getContext('2d');
    const attritionData = @json($attritionData);
    const attritionLabels = attritionData.map(a => a.reason);
    const attritionCounts = attritionData.map(a => a.count);
    const attritionColors = ['#be123c', '#92400e', '#1a7a52', '#5c798e'];

    new Chart(attritionCtx, {
        type: 'bar',
        data: {
            labels: attritionLabels,
            datasets: [{
                label: 'Count',
                data: attritionCounts,
                backgroundColor: attritionColors,
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Load Department Health Scores
    fetch('{{ route("hr4.analytics.kpi.health") }}')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(dept => {
                const statusClass = dept.status;
                const statusLabel = dept.status.charAt(0).toUpperCase() + dept.status.slice(1);
                html += `
                    <div class="health-box">
                        <div class="health-score">
                            <div class="health-circle ${statusClass}">${dept.healthScore}</div>
                            <div class="health-info">
                                <div class="health-name">${dept.department}</div>
                                <div class="health-count">${dept.headcount} employees • ${dept.fillRate}% filled</div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="color: var(--c-muted); font-size: 0.85rem;">Turnover: ${dept.turnoverRate}%</div>
                            <div style="font-weight: 600; color: var(--c-text); margin-top: 0.25rem;">${statusLabel}</div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('healthScoresContainer').innerHTML = html;
        })
        .catch(err => console.error('Error loading health scores:', err));
</script>
@endsection
