@extends('admin.hr4.layouts.app')

@section('title', 'Payroll & Labor Cost Analytics')

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
        --c-blue:         #1a5f8a;
        --c-green:        #1a7a52;
        --c-red:          #be123c;
        --c-yellow:       #92400e;
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
    }

    .analytics-header h1 em {
        color: var(--c-teal);
        font-style: italic;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-export {
        background: var(--c-teal);
        color: #fff;
    }

    .btn-export:hover {
        background: #0b9483;
        transform: translateY(-2px);
    }

    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .summary-card.alert { border-left: 4px solid var(--c-red); }
    .summary-card.warning { border-left: 4px solid var(--c-yellow); }
    .summary-card.healthy { border-left: 4px solid var(--c-green); }

    .summary-label {
        font-size: 0.85rem;
        color: var(--c-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--c-text);
        margin-bottom: 0.5rem;
    }

    .summary-meta {
        font-size: 0.85rem;
        color: var(--c-muted);
    }

    .budget-bar {
        height: 6px;
        background: #e0e0e0;
        border-radius: 3px;
        margin-top: 0.75rem;
        overflow: hidden;
    }

    .budget-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--c-teal), var(--c-teal));
        border-radius: 3px;
    }

    .budget-fill.warning { background: linear-gradient(90deg, var(--c-yellow), var(--c-yellow)); }
    .budget-fill.over { background: linear-gradient(90deg, var(--c-red), var(--c-red)); }

    /* Dashboard Section */
    .dashboard-section {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
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

    /* Table */
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

    .amount {
        font-weight: 600;
        color: var(--c-teal);
    }

    .percentage {
        background: var(--c-teal-light);
        color: var(--c-teal);
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Two Column */
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

    /* List Items */
    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid var(--c-line);
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-label {
        font-weight: 600;
        color: var(--c-text);
    }

    .list-value {
        font-weight: 700;
        color: var(--c-teal);
        font-size: 1.1rem;
    }

    .contribution-bar {
        margin-top: 0.5rem;
        height: 4px;
        background: #e0e0e0;
        border-radius: 2px;
        overflow: hidden;
    }

    .contribution-fill {
        height: 100%;
        background: var(--c-teal);
    }
</style>

<div class="analytics-container">
    <div class="analytics-header">
        <h1>Payroll & Labor Cost <em>Analytics</em></h1>
        <div class="header-actions">
            <form action="{{ route('hr4.analytics.payroll.export') }}" method="GET" style="display: inline;">
                <button type="submit" class="btn btn-export">
                    <i class="bi bi-download"></i> Export Report
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Monthly Payroll</div>
            <div class="summary-value">₱{{ number_format($summaryData['monthlyTotal'], 2) }}</div>
            <div class="summary-meta">YTD: ₱{{ number_format($summaryData['ytdTotal'], 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Monthly Deductions</div>
            <div class="summary-value">₱{{ number_format($summaryData['monthlyDeductions'], 2) }}</div>
            <div class="summary-meta">{{ round(($summaryData['monthlyDeductions'] / $summaryData['monthlyTotal']) * 100, 1) }}% of payroll</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Net Pay (Monthly)</div>
            <div class="summary-value">₱{{ number_format($summaryData['monthlyNetPay'], 2) }}</div>
            <div class="summary-meta">Actual cash paid out</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Cost Per Employee</div>
            <div class="summary-value">₱{{ number_format($summaryData['costPerEmployee'], 0) }}</div>
            <div class="summary-meta">{{ $summaryData['activeEmployees'] }} active employees</div>
        </div>

        <div class="summary-card {{ $summaryData['budgetStatus'] === 'over' ? 'alert' : ($summaryData['budgetStatus'] === 'warning' ? 'warning' : 'healthy') }}">
            <div class="summary-label">Budget Status</div>
            <div class="summary-value">{{ $summaryData['budgetPercentage'] }}%</div>
            <div class="budget-bar">
                <div class="budget-fill {{ $summaryData['budgetStatus'] === 'over' ? 'over' : ($summaryData['budgetStatus'] === 'warning' ? 'warning' : '') }}"
                     style="width: {{ min(100, $summaryData['budgetPercentage']) }}%"></div>
            </div>
            <div class="summary-meta" style="margin-top: 0.5rem;">Budget: ₱{{ number_format($summaryData['budget'], 0) }}</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Compensation Total</div>
            <div class="summary-value">₱{{ number_format($summaryData['compensationTotal'], 2) }}</div>
            <div class="summary-meta">Current month total</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="two-column">
        <!-- Payroll Trends -->
        <div class="dashboard-section">
            <div class="section-title">
                <i class="bi bi-graph-up"></i> Payroll Trends (12 Months)
            </div>
            <div class="chart-container">
                <canvas id="payrollTrendChart"></canvas>
            </div>
        </div>

        <!-- Cost Analysis Pie -->
        <div class="dashboard-section">
            <div class="section-title">
                <i class="bi bi-pie-chart"></i> Cost Breakdown
            </div>
            <div class="chart-container">
                <canvas id="costBreakdownChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Cost by Department -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-building"></i> Cost by Department
        </div>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th style="text-align: right;">Headcount</th>
                    <th style="text-align: right;">Total Cost</th>
                    <th style="text-align: right;">Avg Salary</th>
                </tr>
            </thead>
            <tbody>
                @foreach($costByDepartment as $dept)
                    <tr>
                        <td>
                            <strong>{{ $dept->department ?? 'N/A' }}</strong>
                        </td>
                        <td style="text-align: right;">{{ $dept->headcount }}</td>
                        <td style="text-align: right;">
                            <span class="amount">₱{{ number_format($dept->total_base_salary ?? 0, 2) }}</span>
                        </td>
                        <td style="text-align: right;">
                            <span>₱{{ number_format($dept->avg_salary ?? 0, 2) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Cost by Position -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-briefcase"></i> Cost by Position
        </div>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th style="text-align: center;">Count</th>
                    <th style="text-align: right;">Base Salary</th>
                    <th style="text-align: right;">Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($costByPosition as $pos)
                    <tr>
                        <td><strong>{{ $pos['position'] }}</strong></td>
                        <td style="text-align: center;">{{ $pos['count'] }}</td>
                        <td style="text-align: right;">₱{{ number_format($pos['baseSalary'], 2) }}</td>
                        <td style="text-align: right;">
                            <span class="amount">₱{{ number_format($pos['totalCost'], 2) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Deduction Analysis -->
    <div class="dashboard-section">
        <div class="section-title">
            <i class="bi bi-dash-circle"></i> Deduction Breakdown
        </div>
        <div class="two-column">
            <div id="deductionList">
                @foreach($deductionBreakdown as $ded)
                    <div class="list-item">
                        <div class="list-label">{{ $ded['type'] }}</div>
                        <div class="list-value">₱{{ number_format($ded['amount'], 2) }}</div>
                    </div>
                    <div style="padding: 0 1rem 0.5rem 1rem;">
                        <div class="contribution-bar">
                            <div class="contribution-fill" style="width: {{ round(($ded['amount'] / array_sum(array_map(fn($d) => $d['amount'], $deductionBreakdown->toArray()))) * 100, 0) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <div class="chart-container">
                    <canvas id="deductionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Distribution -->
    @php
        $salaryDist = $salaryDistribution;
    @endphp
    @if($salaryDist)
        <div class="dashboard-section">
            <div class="section-title">
                <i class="bi bi-graph-up-arrow"></i> Salary Distribution
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div style="padding: 1rem; background: var(--c-teal-light); border-radius: 8px;">
                    <div style="color: var(--c-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">Minimum Salary</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--c-teal);">
                        ₱{{ number_format($salaryDist->min_salary, 2) }}
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--c-blue-light); border-radius: 8px;">
                    <div style="color: var(--c-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">Average Salary</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--c-blue);">
                        ₱{{ number_format($salaryDist->avg_salary, 2) }}
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--c-green-light); border-radius: 8px;">
                    <div style="color: var(--c-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">Maximum Salary</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--c-green);">
                        ₱{{ number_format($salaryDist->max_salary, 2) }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Payroll Trend Chart
    const trendCtx = document.getElementById('payrollTrendChart').getContext('2d');
    const payrollTrends = @json($payrollTrends);
    const trendLabels = Object.keys(payrollTrends);
    const trendData = Object.values(payrollTrends);

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Total Payroll',
                data: trendData,
                borderColor: '#0a7c6e',
                backgroundColor: 'rgba(10, 124, 110, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0a7c6e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Cost Breakdown Chart
    const costCtx = document.getElementById('costBreakdownChart').getContext('2d');
    const costAnalysis = @json($costAnalysis);
    const costLabels = costAnalysis.map(c => c.category);
    const costData = costAnalysis.map(c => c.amount);
    const costColors = ['#0a7c6e', '#1a5f8a', '#1a7a52', '#92400e', '#5c798e'];

    new Chart(costCtx, {
        type: 'doughnut',
        data: {
            labels: costLabels,
            datasets: [{
                data: costData,
                backgroundColor: costColors,
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

    // Deduction Chart
    const dedCtx = document.getElementById('deductionChart').getContext('2d');
    const deductions = @json($deductionBreakdown);
    const dedLabels = deductions.map(d => d.type);
    const dedData = deductions.map(d => d.amount);

    new Chart(dedCtx, {
        type: 'bar',
        data: {
            labels: dedLabels,
            datasets: [{
                label: 'Amount',
                data: dedData,
                backgroundColor: ['#be123c', '#92400e', '#0a7c6e', '#1a5f8a', '#1a7a52'],
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
</script>
@endsection
