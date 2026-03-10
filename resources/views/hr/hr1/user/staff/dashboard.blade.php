@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f8fafc]" x-data="dashboard()">
    @include('user.shared.sidebar')
    @include('user.shared.header')

    <main :class="`flex-1 transition-all duration-500 overflow-x-hidden ${sidebarOpen ? 'ml-[280px]' : 'ml-[100px]'}`">
        <div class="p-8 md:p-16 max-w-[1600px] mx-auto">
            <div class="mb-16">
                <h1 class="text-6xl font-black text-primary tracking-tighter capitalize mb-6">Dashboard</h1>
                <div class="text-[15px] font-medium text-text-light/80 max-w-4xl leading-relaxed">
                    Hospital Command Center: <span class="text-accent font-black uppercase bg-accent/5 px-3 py-1 rounded-xl">Staff Context</span>.
                </div>
            </div>
            
            <!-- Staff Dashboard Content -->
            <div class="space-y-6">
                <div class="main-inner bg-primary text-white p-10 rounded-3xl flex justify-between items-center !w-full !max-w-none">
                    <div>
                        <h2 class="text-3xl font-black mb-2">MedCore Analytics: Staff</h2>
                        <p class="text-highlight">Overview of recruitment and performance metrics.</p>
                    </div>
                    <i data-lucide="bar-chart-2" class="w-16 h-16 text-highlight opacity-50"></i>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Total Applicants</h4>
                        <div class="text-3xl font-black text-primary" x-text="applicants.length"></div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Offer Acceptance</h4>
                        <div class="text-3xl font-black text-primary">82%</div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Avg. Time to Hire</h4>
                        <div class="text-3xl font-black text-primary">18 Days</div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Training Compliance</h4>
                        <div class="text-3xl font-black text-primary">94%</div>
                    </div>
                </div>

                <!-- Recruitment Performance Chart -->
                <div class="main-inner !w-full !max-w-none">
                    <h3 class="text-xl font-black text-primary mb-6">Recruitment Performance</h3>
                    <div class="h-64">
                        <canvas id="recruitmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@include('user.shared.modals')
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        role: 'staff',
        activeTab: 'dashboard',
        sidebarOpen: true,
        modalType: null,
        selectedJob: null,
        selectedApplicant: null,
        applicants: @json($applicants ?? []),
        jobs: @json($jobs ?? []),
        recognitions: @json($recognitions ?? []),
        tasks: @json($tasks ?? []),
        awardCategories: @json($awardCategories ?? []),
        evalCriteria: @json($evalCriteria ?? []),
        availableModules: @json($availableModules ?? []),
        
        get navItems() {
            return [
                { id: 'dashboard', label: 'Dashboard', icon: 'layout-dashboard' },
                { id: 'applicant', label: 'Applicants', icon: 'users' },
                { id: 'recruitment', label: 'Recruitment', icon: 'briefcase' },
                { id: 'onboarding', label: 'Onboarding', icon: 'user-plus' },
                { id: 'performance', label: 'Performance', icon: 'trending-up' },
                { id: 'recognition', label: 'Recognition', icon: 'award' },
                { id: 'profile', label: 'Profile', icon: 'user-circle' }
            ];
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    
    // Initialize chart
    const ctx = document.getElementById('recruitmentChart');
    if (ctx && typeof Chart !== 'undefined') {
        const jobsData = @json($jobs ?? []);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: jobsData.map(job => job.title),
                datasets: [{
                    label: 'Applications',
                    data: jobsData.map(job => job.applications ? job.applications.length : 0),
                    backgroundColor: '#1B3C53',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush

