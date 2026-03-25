@extends('admin.hr4.layouts.app')

@section('title', 'HR Analytics Module')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    body {
        background: linear-gradient(135deg, #eef3f7 0%, #f0f5f8 100%);
        min-height: 100vh;
        padding: 2rem;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .header {
        text-align: center;
        margin-bottom: 3rem;
        animation: fadeDown 0.6s ease both;
    }

    .header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2.5rem;
        color: #1b2b3a;
        margin: 0 0 0.5rem 0;
        font-weight: 600;
    }

    .header h1 em {
        color: #0a7c6e;
        font-style: italic;
    }

    .header p {
        color: #5c798e;
        font-size: 1.05rem;
        margin: 0;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 700px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .analytics-card {
        background: #ffffff;
        border: 1.5px solid #d4e3ee;
        border-radius: 14px;
        padding: 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(10, 50, 80, 0.06);
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .analytics-card:hover {
        box-shadow: 0 8px 24px rgba(10, 124, 110, 0.15);
        transform: translateY(-4px);
        border-color: #0a7c6e;
    }

    .card-icon {
        font-size: 2.5rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: linear-gradient(135deg, #e4f4f1 0%, #c0e4dd 100%);
        color: #0a7c6e;
    }

    .analytics-card.payroll .card-icon {
        background: linear-gradient(135deg, #e8f2f9 0%, #c5dfe8 100%);
        color: #1a5f8a;
    }

    .card-content h2 {
        font-size: 1.5rem;
        color: #1b2b3a;
        margin: 0;
        font-weight: 600;
    }

    .card-content p {
        color: #5c798e;
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .card-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #0a7c6e;
        font-weight: 600;
        margin-top: auto;
        transition: all 0.2s ease;
    }

    .analytics-card.payroll .card-cta {
        color: #1a5f8a;
    }

    .analytics-card:hover .card-cta {
        gap: 0.75rem;
    }

    .features {
        background: #ffffff;
        border: 1.5px solid #d4e3ee;
        border-radius: 14px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(10, 50, 80, 0.06);
    }

    .features h3 {
        font-size: 1.2rem;
        color: #1b2b3a;
        margin-top: 0;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .feature-columns {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 700px) {
        .feature-columns {
            grid-template-columns: 1fr;
        }
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-list li {
        padding: 0.6rem 0;
        color: #1b2b3a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }

    .feature-list li:before {
        content: "✓";
        color: #0a7c6e;
        font-weight: 700;
        font-size: 1.1rem;
    }

    @keyframes fadeDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .analytics-card {
        animation: fadeUp 0.6s ease both;
    }

    .analytics-card:nth-child(2) {
        animation-delay: 0.1s;
    }

    .features {
        animation: fadeUp 0.6s ease both 0.2s backwards;
    }
</style>

<div class="container">
    <div class="header">
        <h1>HR <em>Analytics</em></h1>
        <p>Key insights and metrics for workforce and financial management</p>
    </div>

    <div class="analytics-grid">
        <!-- KPI Dashboard -->
        <a href="{{ route('hr4.analytics.kpi') }}" class="analytics-card">
            <div class="card-icon">
                <i class="bi bi-graph-up" style="font-size: 24px;"></i>
            </div>
            <div class="card-content">
                <h2>HR KPI Dashboard</h2>
                <p>Monitor employee metrics, headcount trends, turnover rates, and department health scores in real-time.</p>
                <div class="card-cta">
                    View Dashboard <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </a>

        <!-- Payroll Analytics -->
        <a href="{{ route('hr4.analytics.payroll') }}" class="analytics-card payroll">
            <div class="card-icon">
                <i class="bi bi-calculator" style="font-size: 24px;"></i>
            </div>
            <div class="card-content">
                <h2>Payroll & Labor Cost</h2>
                <p>Analyze payroll costs, departmental expenses, salary distributions, and budget compliance tracking.</p>
                <div class="card-cta">
                    View Analytics <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="features">
        <h3><i class="bi bi-star-fill" style="color: #0a7c6e; margin-right: 0.5rem;"></i> Key Features</h3>
        <div class="feature-columns">
            <div>
                <h4 style="color: #0a7c6e; margin-top: 0; font-weight: 600; font-size: 1rem;">KPI Dashboard Includes:</h4>
                <ul class="feature-list">
                    <li>Total Headcount & Active Status</li>
                    <li>New Hires & Vacancy Tracking</li>
                    <li>Turnover & Attrition Rates</li>
                    <li>Department Health Scores</li>
                    <li>12-Month Headcount Trends</li>
                    <li>Average Tenure Metrics</li>
                </ul>
            </div>
            <div>
                <h4 style="color: #1a5f8a; margin-top: 0; font-weight: 600; font-size: 1rem;">Payroll Analytics Includes:</h4>
                <ul class="feature-list">
                    <li>Monthly & YTD Payroll Totals</li>
                    <li>Cost Per Employee Metrics</li>
                    <li>Budget vs. Actual Comparison</li>
                    <li>Department Cost Breakdown</li>
                    <li>Position-wise Cost Analysis</li>
                    <li>Salary Distribution Insights</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
