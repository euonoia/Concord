@extends('core.core1.layouts.app')

@section('title', 'Intelligence | Core1 Intelligence Hub')

@section('content')
<!-- High-End Dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'core-navy': '#1B3C53',
                    'core-steel': '#456882',
                    'core-beige': '#D2C1B6',
                    'core-bg': '#f7f8fa',
                    'premium-emerald': '#10b981',
                    'premium-amber': '#f59e0b',
                    'premium-rose': '#ef4444',
                },
                borderRadius: {
                    '5xl': '3.5rem',
                }
            }
        }
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@100;300;400;700;900&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(27, 60, 83, 0.1);
        --core-primary: #1B3C53;
        --core-accent: #456882;
    }

    .premium-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        background-image: 
            radial-gradient(at 0% 0%, rgba(27, 60, 83, 0.03) 0px, transparent 50%),
            radial-gradient(at 100% 0%, rgba(210, 193, 182, 0.1) 0px, transparent 50%);
        min-height: 100vh;
        color: #1e293b;
    }

    .ultra-header-font {
        font-family: 'Outfit', sans-serif;
    }

    .glass-widget {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .glass-widget:hover {
        border-color: rgba(27, 60, 83, 0.2);
        background: rgba(255, 255, 255, 1);
        transform: translateY(-5px);
        box-shadow: 0 20px 45px rgba(27, 60, 83, 0.08);
    }

    .brand-border-primary { border-bottom: 3px solid var(--core-primary); }
    .brand-border-accent { border-bottom: 3px solid var(--core-accent); }
    .brand-border-beige { border-bottom: 3px solid #D2C1B6; }

    .stat-value {
        font-family: 'Outfit', sans-serif;
        letter-spacing: -2px;
        color: var(--core-primary);
    }

    @keyframes reveal {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-reveal {
        animation: reveal 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }

    .chart-box-ultra {
        height: 350px !important;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f8fafc; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="premium-page core1-container !p-0">
    <div class="max-w-[1700px] mx-auto p-6 md:p-12">
        
        <!-- Intelligence Header -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-10 mb-16 animate-reveal">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 border border-blue-100 rounded-full">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    <span class="text-[10px] font-bold text-blue-800 uppercase tracking-widest ultra-header-font">Core Insights Engine</span>
                </div>
                <h1 class="text-6xl md:text-7xl font-black text-slate-900 tracking-tighter ultra-header-font">
                    Hospital <span class="text-core-navy">Intelligence</span>
                </h1>
                <p class="text-slate-500 text-xl font-medium max-w-2xl leading-relaxed">
                    Real-time clinical telemetry and fiscal performance oversight, perfectly aligned with the Concord design system.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-6">
                <div class="glass-widget px-8 py-5 rounded-3xl flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status</span>
                    <span class="text-lg font-bold text-core-navy">Network Active</span>
                </div>
                <button class="bg-core-navy text-white px-10 py-5 rounded-[2.5rem] font-black text-lg hover:bg-core-steel transition-all shadow-xl shadow-blue-900/10">
                    <i class="fas fa-file-export mr-2"></i>Export Intelligence
                </button>
            </div>
        </div>

        <!-- Metric Grid: Widget Style -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            
            <!-- Widget 1: Patients -->
            <div class="glass-widget p-8 rounded-[3rem] brand-border-primary animate-reveal" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-8">
                    <div class="text-white bg-core-navy w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-900/20">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                </div>
                <h3 class="text-5xl font-bold stat-value mb-2">{{ number_format($stats['total_patients']) }}</h3>
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-widest">Global Database</p>
                <div class="mt-6 flex items-center gap-2">
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">+12% vs last month</span>
                </div>
            </div>

            <!-- Widget 2: Appointments -->
            <div class="glass-widget p-8 rounded-[3rem] brand-border-accent animate-reveal" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-8">
                    <div class="text-white bg-core-steel w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-gray-400/20">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <h3 class="text-5xl font-bold stat-value mb-2">{{ number_format($stats['monthly_appointments']) }}</h3>
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-widest">Monthly Load</p>
                <div class="mt-6 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="bg-core-navy h-full w-[75%]"></div>
                </div>
            </div>

            <!-- Widget 3: Pending -->
            <div class="glass-widget p-8 rounded-[3rem] brand-border-beige animate-reveal" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-8">
                    <div class="text-white bg-[#D2C1B6] w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                <h3 class="text-5xl font-bold stat-value mb-2">{{ number_format($stats['pending_appointments']) }}</h3>
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-widest">Awaiting Triage</p>
                <div class="mt-6 flex items-center gap-1">
                    @for($i=0; $i<8; $i++)
                        <div class="w-1.5 h-1.5 rounded-full {{ $i < 4 ? 'bg-[#D2C1B6]' : 'bg-slate-200' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Widget 4: Revenue -->
            <div class="glass-widget p-8 rounded-[3rem] border-none bg-core-navy !text-white animate-reveal" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-8">
                    <div class="text-core-navy bg-white w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                </div>
                <h3 class="text-5xl font-bold stat-value !text-white mb-2">₱{{ number_format($stats['monthly_revenue'], 0) }}</h3>
                <p class="text-blue-200/60 font-bold uppercase text-[10px] tracking-widest">Fiscal Performance</p>
                <p class="text-[10px] text-blue-200/40 mt-6 tracking-widest italic">Live Revenue Cycle</p>
            </div>
        </div>

        <!-- Main Charting Logic -->
        <div class="space-y-8 mb-16 text-slate-900">
            
            <!-- Row 1: Pulse (Full Width) -->
            <div class="glass-widget p-10 rounded-[4rem] animate-reveal" style="animation-delay: 0.5s">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <h3 class="text-3xl font-black ultra-header-font text-core-navy">Hospital Pulse</h3>
                        <p class="text-slate-400 font-medium">Measurement of daily entity velocity (Last 7 Days)</p>
                    </div>
                </div>
                <div class="chart-box-ultra">
                    <canvas id="pulseChart"></canvas>
                </div>
            </div>

            <!-- Row 2: Grid for Matrix & Mix -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Status Matrix -->
                <div class="glass-widget p-10 rounded-[4rem] animate-reveal" style="animation-delay: 0.6s">
                    <h3 class="text-3xl font-black ultra-header-font text-core-navy mb-2">Outcome Quality</h3>
                    <p class="text-slate-400 font-medium mb-12">Statistical probability of consultation results</p>
                    <div class="chart-box-ultra !h-[300px]">
                        <canvas id="matrixChart"></canvas>
                    </div>
                </div>

                <!-- Patient Mix -->
                <div class="glass-widget p-10 rounded-[4rem] animate-reveal" style="animation-delay: 0.7s">
                    <h3 class="text-3xl font-black ultra-header-font text-core-navy mb-2">Patient Mix</h3>
                    <p class="text-slate-400 font-medium mb-12">Demographic distribution analytics</p>
                    <div class="chart-box-ultra !h-[300px]">
                        <canvas id="mixChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Row 3: Capital Flow (Full Width) -->
            <div class="glass-widget p-10 rounded-[4rem] animate-reveal" style="animation-delay: 0.8s">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <h3 class="text-3xl font-black ultra-header-font text-core-navy">Capital Flow</h3>
                        <p class="text-slate-400 font-medium tracking-tight">Consolidated fiscal growth mapping (6 months)</p>
                    </div>
                </div>
                <div class="chart-box-ultra">
                    <canvas id="capitalChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.elements.bar.borderRadius = 10;

        const brandColors = ['#1B3C53', '#456882', '#D2C1B6', '#10b981', '#f59e0b', '#dc2626'];

        // 1. Pulse Chart (Navy Theme)
        const ctxPulse = document.getElementById('pulseChart').getContext('2d');
        const navyGradient = ctxPulse.createLinearGradient(0, 0, 0, 400);
        navyGradient.addColorStop(0, 'rgba(27, 60, 83, 0.15)');
        navyGradient.addColorStop(1, 'rgba(27, 60, 83, 0)');

        new Chart(ctxPulse, {
            type: 'line',
            data: {
                labels: {!! json_encode($trends->pluck('date')) !!},
                datasets: [{
                    label: 'Load',
                    data: {!! json_encode($trends->pluck('count')) !!},
                    borderColor: '#1B3C53',
                    borderWidth: 5,
                    backgroundColor: navyGradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1B3C53',
                    pointBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { stepSize: 1, padding: 20 }
                    },
                    x: { grid: { display: false }, ticks: { padding: 20 } }
                }
            }
        });

        // 2. Matrix Chart
        new Chart(document.getElementById('matrixChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusDistribution->pluck('status')->map(fn($s) => ucfirst($s))) !!},
                datasets: [{
                    data: {!! json_encode($statusDistribution->pluck('count')) !!},
                    backgroundColor: brandColors,
                    borderWidth: 8,
                    borderColor: '#fff'
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // 3. Mix Chart (Brand Pie)
        new Chart(document.getElementById('mixChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($demographics->pluck('gender')->map(fn($g) => ucfirst($g))) !!},
                datasets: [{
                    data: {!! json_encode($demographics->pluck('count')) !!},
                    backgroundColor: ['#1B3C53', '#456882', '#D2C1B6'],
                    borderWidth: 5,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                }
            }
        });

        // 4. Capital Flow (Navy Bar)
        new Chart(document.getElementById('capitalChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueTrends->pluck('month')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueTrends->pluck('total')) !!},
                    backgroundColor: '#1B3C53',
                    hoverBackgroundColor: '#456882',
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { callback: (v) => '₱' + (v/1000) + 'k', padding: 20 } 
                    },
                    x: { grid: { display: false }, ticks: { padding: 20 } }
                }
            }
        });
    });
</script>
@endpush
@endsection
