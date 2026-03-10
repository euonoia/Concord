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
                    Hospital Command Center: <span class="text-accent font-black uppercase bg-accent/5 px-3 py-1 rounded-xl">Candidate Context</span>.
                </div>
            </div>
            
            <!-- Candidate Dashboard Content -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Open Positions</h4>
                        <div class="text-3xl font-black text-primary" x-text="jobs.length"></div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Pending Tasks</h4>
                        <div class="text-3xl font-black text-primary" x-text="tasks.filter(t => !t.completed).length"></div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Completed Tasks</h4>
                        <div class="text-3xl font-black text-primary" x-text="tasks.filter(t => t.completed).length"></div>
                    </div>
                    <div class="card !w-full">
                        <h4 class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Assessment %</h4>
                        <div class="text-3xl font-black text-primary">65%</div>
                    </div>
                </div>

                <!-- Live Application Journey -->
                <div class="main-inner !w-full !max-w-none">
                    <h3 class="text-xl font-black text-primary mb-6">Live Application Journey</h3>
                    <div class="flex gap-4">
                        <template x-for="app in myApplications" :key="app.id">
                            <div class="p-4 bg-bg rounded-2xl flex-1 border border-gray-100">
                                <div class="text-xs font-black uppercase text-accent mb-2" x-text="app.jobTitle"></div>
                                <div class="flex items-center gap-2">
                                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary" style="width: 60%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-primary" x-text="app.status"></span>
                                </div>
                            </div>
                        </template>
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
        role: 'candidate',
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
        
        get myApplications() {
            // Get applications for current candidate (first applicant as example)
            return this.applicants[0]?.applications || [];
        },
        
        get navItems() {
            return [
                { id: 'dashboard', label: 'Dashboard', icon: 'layout-dashboard' },
                { id: 'my-application', label: 'My Apps', icon: 'clipboard-list' },
                { id: 'recruitment', label: 'Jobs', icon: 'briefcase' },
                { id: 'onboarding', label: 'Tasks', icon: 'check-square' },
                { id: 'performance', label: 'Self Assessment', icon: 'target' },
                { id: 'recognition', label: 'Culture', icon: 'star' },
                { id: 'profile', label: 'Profile', icon: 'user-circle' }
            ];
        },
        
        openApplyModal(job) {
            this.selectedJob = job;
            this.modalType = 'apply';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>
@endpush

