@extends('hr1.layouts.app')

@section('content')
<div x-data="dashboard()" style="display: flex; min-height: 100vh;">
    <!-- Mobile Topbar -->
    <div class="topbar">
        <button class="menu-toggle" @click="document.querySelector('.sidebar').classList.toggle('show')">
            ☰
        </button>
        <div class="title">MedCore HR1</div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" 
         id="sidebar"
         x-init="
            // Desktop hover collapse
            $el.addEventListener('mouseenter', () => {
                if (window.innerWidth > 768 && $el.classList.contains('collapsed')) {
                    $el.classList.remove('collapsed');
                }
            });
            $el.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) {
                    $el.classList.add('collapsed');
                }
            });
            // Default collapsed on desktop
            if (window.innerWidth > 768) {
                $el.classList.add('collapsed');
            }
            // Auto-close on mobile
            document.addEventListener('click', (e) => {
                const toggle = document.querySelector('.menu-toggle');
                if (!$el.contains(e.target) && toggle && !toggle.contains(e.target)) {
                    $el.classList.remove('show');
                }
            });
         ">
        <div class="logo">
            <img src="{{ asset('images/hr1/logo.png') }}" alt="HR1 Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display:none; width: 60px; height: 60px; background: var(--accent); border-radius: 10px; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px;">HR1</div>
            <div class="logo-text">MedCore HR1</div>
        </div>

        <nav>
            <template x-for="item in navItems" :key="item.id">
                <a href="#" 
                   @click.prevent="activeTab = item.id" 
                   :class="{ 'active': activeTab === item.id }"
                   class="nav-link">
                    <i :class="getIconClass(item.icon)"></i>
                    <span x-text="item.label"></span>
                    <span class="tooltip" x-text="item.label"></span>
                </a>
            </template>
        </nav>
    </div>

    <main class="main-content">
        <div class="p-8 md:p-12" style="width: 100%; max-width: 100%;">
            <div class="mb-16">
                <h1 class="text-6xl font-black text-primary tracking-tighter capitalize mb-6">Dashboard</h1>
                <div class="text-[15px] font-medium text-text-light/80 max-w-4xl leading-relaxed">
                    Hospital Command Center: <span class="text-accent font-black uppercase bg-accent/5 px-3 py-1 rounded-xl">Staff Context</span>.
                </div>
            </div>
            
            <!-- Staff Dashboard Content -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <div class="main-inner bg-primary text-white p-10 rounded-3xl flex justify-between items-center !w-full !max-w-none">
                    <div>
                        <h2 class="text-3xl font-black mb-2">MedCore Analytics: Staff</h2>
                        <p class="text-highlight">Overview of recruitment and performance metrics.</p>
                    </div>
                    <i data-lucide="bar-chart-2" class="w-16 h-16 text-highlight opacity-50"></i>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="card !w-full group cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                <i class="bi bi-people text-primary text-xl"></i>
                            </div>
                        </div>
                        <h4 class="text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Total Applicants</h4>
                        <div class="text-4xl font-black text-primary mb-1" x-text="applicants.length"></div>
                        <p class="text-xs text-text-light">Active candidates</p>
                    </div>
                    <div class="card !w-full group cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <i class="bi bi-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                        <h4 class="text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Offer Acceptance</h4>
                        <div class="text-4xl font-black text-primary mb-1">82%</div>
                        <p class="text-xs text-text-light">Acceptance rate</p>
                    </div>
                    <div class="card !w-full group cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="bi bi-clock text-blue-600 text-xl"></i>
                            </div>
                        </div>
                        <h4 class="text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Avg. Time to Hire</h4>
                        <div class="text-4xl font-black text-primary mb-1">18 Days</div>
                        <p class="text-xs text-text-light">Average duration</p>
                    </div>
                    <div class="card !w-full group cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <i class="bi bi-award text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <h4 class="text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Training Compliance</h4>
                        <div class="text-4xl font-black text-primary mb-1">94%</div>
                        <p class="text-xs text-text-light">Completion rate</p>
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

            <!-- Applicants Tab -->
            <div x-show="activeTab === 'applicant'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Applicants</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="applicants.length">
                    <template x-for="applicant in applicants" :key="applicant.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all duration-200 flex justify-between items-center group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-primary mb-1" x-text="applicant.name"></div>
                                    <div class="text-xs text-text-light" x-text="applicant.position"></div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold uppercase px-3 py-1.5 rounded-full bg-primary/10 text-primary border border-primary/20"
                                  x-text="applicant.status"></span>
                        </div>
                    </template>
                </div>
                <div x-show="!applicants.length" class="text-sm text-text-light">
                    No applicants yet.
                </div>
            </div>

            <!-- Recruitment Tab -->
            <div x-show="activeTab === 'recruitment'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Recruitment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="jobs.length">
                    <template x-for="job in jobs" :key="job.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all duration-200 flex flex-col gap-3 group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="text-base font-semibold text-primary mb-1" x-text="job.title"></div>
                                    <div class="text-xs text-text-light flex items-center gap-2">
                                        <i class="bi bi-building text-accent"></i>
                                        <span x-text="job.department"></span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                                    <i class="bi bi-briefcase text-accent"></i>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="text-xs text-text-light">Applications:</span>
                                <span class="text-sm font-bold text-primary bg-primary/10 px-3 py-1 rounded-full" x-text="job.applications_hr1 ? job.applications_hr1.length : 0"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="!jobs.length" class="text-sm text-text-light">
                    No recruitment data available.
                </div>
            </div>

            <!-- Onboarding Tab -->
            <div x-show="activeTab === 'onboarding'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Onboarding</h3>
                <div class="flex flex-col gap-3" x-show="tasks.length">
                    <template x-for="task in tasks" :key="task.id">
                        <div class="p-4 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-md transition-all duration-200 flex justify-between items-center group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors"
                                     :class="task.completed ? 'bg-green-100 group-hover:bg-green-200' : 'bg-yellow-100 group-hover:bg-yellow-200'">
                                    <i :class="task.completed ? 'bi bi-check-circle text-green-600' : 'bi bi-clock text-yellow-600'"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-primary mb-1" x-text="task.title"></div>
                                    <div class="text-xs text-text-light flex items-center gap-2">
                                        <i class="bi bi-building text-accent"></i>
                                        <span x-text="task.department"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold uppercase px-3 py-1.5 rounded-full border"
                                  :class="task.completed ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200'">
                                <span x-text="task.completed ? 'Completed' : 'Pending'"></span>
                            </span>
                        </div>
                    </template>
                </div>
                <div x-show="!tasks.length" class="text-sm text-text-light">
                    No onboarding tasks assigned.
                </div>
            </div>

            <!-- Performance Tab -->
            <div x-show="activeTab === 'performance'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-2">Performance</h3>
                <p class="text-sm text-text-light">Performance analytics and reports will appear here.</p>
            </div>

            <!-- Recognition Tab -->
            <div x-show="activeTab === 'recognition'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-2">Recognition</h3>
                <p class="text-sm text-text-light">Recognition and awards will appear here.</p>
            </div>

            <!-- Profile Tab -->
            <div x-show="activeTab === 'profile'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-2">Staff Profile</h3>
                <p class="text-sm text-text-light">Profile information will appear here.</p>
            </div>
        </div>
    </main>
</div>

@include('hr1.user_hr1.shared.modals')
@endsection

@push('scripts')
<script>
// Make getIconClass available globally
window.getIconClass = function(iconName) {
    const iconMap = {
        'layout-dashboard': 'bi bi-house-door',
        'users': 'bi bi-people',
        'briefcase': 'bi bi-briefcase',
        'user-plus': 'bi bi-person-plus',
        'trending-up': 'bi bi-graph-up',
        'award': 'bi bi-trophy',
        'user-circle': 'bi bi-person-circle',
        'clipboard-list': 'bi bi-clipboard-check',
        'check-square': 'bi bi-check-square',
        'target': 'bi bi-bullseye',
        'star': 'bi bi-star'
    };
    return iconMap[iconName] || 'bi bi-circle';
};

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
                    data: jobsData.map(job => job.applications_hr1 ? job.applications_hr1.length : 0),
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

