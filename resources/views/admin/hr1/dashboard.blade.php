@extends('admin.hr1.layouts.app')

@section('content')
<div x-data="dashboard()" x-init="init()" style="display: flex; min-height: 100vh;">
    <!-- Mobile Topbar -->
    <div class="topbar">
        <button class="menu-toggle" @click="document.querySelector('.sidebar').classList.toggle('show')">
            ☰
        </button>
        <div class="title">MedCore HR1</div>
        <button type="button"
                class="ml-auto text-xs px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700"
                @click="alert('Logout functionality will be implemented when authentication is added.')">
            Sign out
        </button>
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
            <div class="logo-text">Concord : HR1</div>
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
                    Hospital Command Center: <span class="text-accent font-black uppercase bg-accent/5 px-3 py-1 rounded-xl">Admin Context</span>.
                </div>
            </div>
            
            <!-- Admin Dashboard Content -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <div class="main-inner bg-primary text-white p-10 rounded-3xl flex justify-between items-center !w-full !max-w-none">
                    <div>
                        <h2 class="text-3xl font-black mb-2">Concord HR1 : Admin</h2>
                        <p class="text-highlight">Complete overview of recruitment, performance, and system metrics.</p>
                    </div>
                    <i data-lucide="bar-chart-2" class="w-16 h-16 text-highlight opacity-50"></i>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="card !w-full group cursor-pointer text-left" title="Total number of active applicants and candidates">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors flex-shrink-0">
                                <i class="bi bi-people text-primary text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Total Applicants</h4>
                                    <span class="text-[10px] text-text-light">Active candidates</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.totalApplicants || applicants.length"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left" title="Training module completion rate">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                                <i class="bi bi-award text-purple-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Training Compliance</h4>
                                    <span class="text-[10px] text-text-light">Completion rate</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="(analytics.trainingCompliance || 0) + '%'"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left" title="Open job positions">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors flex-shrink-0">
                                <i class="bi bi-briefcase text-accent text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Active Job Postings</h4>
                                    <span class="text-[10px] text-text-light">Open positions</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.totalJobs || jobs.length"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidate Status Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors flex-shrink-0">
                                <i class="bi bi-person-check text-emerald-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Active Candidates</h4>
                                    <span class="text-[10px] text-text-light">Candidate / Probation / Regular</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="(analytics.statusCounts?.Candidate ?? 0) + (analytics.statusCounts?.Probation ?? 0) + (analytics.statusCounts?.Regular ?? 0)"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors flex-shrink-0">
                                <i class="bi bi-hand-thumbs-up text-green-700 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Regular Employees</h4>
                                    <span class="text-[10px] text-text-light">Regular status</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.statusCounts?.Regular ?? 0"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors flex-shrink-0">
                                <i class="bi bi-x-circle text-red-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Rejected Candidates</h4>
                                    <span class="text-[10px] text-text-light">Final rejections</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.statusCounts?.Rejected ?? 0"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Admin Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="card !w-full group cursor-pointer text-left" title="Tasks awaiting action">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-200 transition-colors flex-shrink-0">
                                <i class="bi bi-list-check text-yellow-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Pending Tasks</h4>
                                    <span class="text-[10px] text-text-light">Awaiting action</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.pendingTasks || tasks.filter(t => !t.completed).length"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center group-hover:bg-pink-200 transition-colors flex-shrink-0">
                                <i class="bi bi-trophy text-pink-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Nominated Candidates</h4>
                                    <span class="text-[10px] text-text-light">Most outstanding nominees</span>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="analytics.totalRecognitions || recognitions.length"></div>
                            </div>
                        </div>
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
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-primary">Applicant Management</h3>
                    <div class="flex flex-col gap-2 items-end">
                        <button @click="modalType = 'add-applicant'" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-hover transition-colors flex items-center gap-2" title="Add new applicant or candidate">
                            <i class="bi bi-person-plus"></i>
                            <span class="text-sm font-semibold">Add User</span>
                        </button>
                        <div class="relative">
                            <button
                                type="button"
                                @click="showDownloadMenu = !showDownloadMenu"
                                class="px-4 py-2 text-[11px] rounded-lg border border-gray-300 text-text-light hover:border-primary hover:text-primary bg-white flex items-center gap-2"
                                title="Download applicant list by status">
                                <i class="bi bi-download"></i>
                                <span class="font-semibold">Download List</span>
                                <i class="bi" :class="showDownloadMenu ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </button>
                            <div
                                x-show="showDownloadMenu"
                                x-cloak
                                @click.outside="showDownloadMenu = false"
                                class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 text-[11px] z-[120]">
                                <button type="button"
                                        class="w-full text-left px-3 py-1.5 hover:bg-bg text-text-light"
                                        @click="downloadApplicantsByStatus('applicant'); showDownloadMenu = false;">
                                    Applicants
                                </button>
                                <button type="button"
                                        class="w-full text-left px-3 py-1.5 hover:bg-bg text-text-light"
                                        @click="downloadApplicantsByStatus('candidate'); showDownloadMenu = false;">
                                    Candidates
                                </button>
                                <button type="button"
                                        class="w-full text-left px-3 py-1.5 hover:bg-bg text-text-light"
                                        @click="downloadApplicantsByStatus('probation'); showDownloadMenu = false;">
                                    Probation
                                </button>
                                <button type="button"
                                        class="w-full text-left px-3 py-1.5 hover:bg-bg text-text-light"
                                        @click="downloadApplicantsByStatus('regular'); showDownloadMenu = false;">
                                    Regular
                                </button>
                                <button type="button"
                                        class="w-full text-left px-3 py-1.5 hover:bg-bg text-red-600"
                                        @click="downloadApplicantsByStatus('rejected'); showDownloadMenu = false;">
                                    Rejected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="applicantSearchQuery" 
                               @input="filterApplicants()"
                               placeholder="Search by name, email, position, contact no., or status..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>

                <!-- Applicants Table -->
                <div class="overflow-x-auto" x-show="paginatedApplicants.length">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">#</th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        User ID
                                        <button @click="sortApplicants('employee_id')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        Name
                                        <button @click="sortApplicants('name')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        Email
                                        <button @click="sortApplicants('email')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        Position
                                        <button @click="sortApplicants('position')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        Contact No.
                                        <button @click="sortApplicants('contact_no')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        Status
                                        <button @click="sortApplicants('status')" class="text-text-light hover:text-primary">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="text-center py-3 px-4 text-xs font-black text-accent uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(applicant, idx) in paginatedApplicants" :key="applicant.id">
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4 text-xs text-text-light" x-text="(applicantCurrentPage - 1) * applicantPerPage + idx + 1"></td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm font-mono text-primary" x-text="applicant.employee_id || applicant.id"></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm font-semibold text-primary" x-text="applicant.name"></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-text-light" x-text="applicant.email || 'N/A'"></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-text-light" x-text="applicant.position || 'N/A'"></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="text-sm text-text-light" x-text="applicant.contact_no || 'N/A'"></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full border"
                                              :class="getStatusClass(applicant.status || 'Applicant')"
                                              x-text="applicant.status || 'Applicant'"></span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="viewApplicantProfile(applicant)" 
                                                    class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="View applicant profile">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button @click="editApplicant(applicant)" 
                                                    class="p-2 text-accent hover:bg-accent/10 rounded-lg transition-colors"
                                                    title="Edit applicant details">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <!-- Applicants Pagination -->
                <div x-show="filteredApplicants.length > applicantPerPage" class="flex items-center justify-between mt-4 py-3 border-t border-gray-200">
                    <div class="text-xs text-text-light">
                        Showing <span x-text="Math.min((applicantCurrentPage - 1) * applicantPerPage + 1, filteredApplicants.length)"></span>-<span x-text="Math.min(applicantCurrentPage * applicantPerPage, filteredApplicants.length)"></span> of <span x-text="filteredApplicants.length"></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="applicantCurrentPage = Math.max(1, applicantCurrentPage - 1)" 
                                :disabled="applicantCurrentPage <= 1"
                                class="px-3 py-1 text-xs rounded border disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Previous page">
                            Prev
                        </button>
                        <template x-for="p in applicantPageNumbers" :key="p">
                            <button @click="applicantCurrentPage = p" 
                                    :class="p === applicantCurrentPage ? 'bg-primary text-white' : 'bg-gray-100'"
                                    class="w-8 h-8 rounded text-xs font-semibold"
                                    :title="'Page ' + p"
                                    x-text="p"></button>
                        </template>
                        <button @click="applicantCurrentPage = Math.min(applicantPageCount, applicantCurrentPage + 1)" 
                                :disabled="applicantCurrentPage >= applicantPageCount"
                                class="px-3 py-1 text-xs rounded border disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Next page">
                            Next
                        </button>
                    </div>
                </div>
                <div x-show="!filteredApplicants.length" class="text-center py-12 text-sm text-text-light">
                    <span x-show="applicantSearchQuery">No applicants found matching your search.</span>
                    <span x-show="!applicantSearchQuery">No applicants found. <button @click="modalType = 'add-applicant'" class="text-primary font-semibold hover:underline">Add your first candidate</button></span>
                </div>
            </div>

            <!-- Recruitment Tab -->
            <div x-show="activeTab === 'recruitment'" class="main-inner !w-full !max-w-none mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-primary">Recruitment Management</h3>
                    <button @click="modalType = 'create-job'" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-hover transition-colors flex items-center gap-2" title="Create new job posting">
                        <i class="bi bi-plus-circle"></i>
                        <span class="text-sm font-semibold">Add Job</span>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="jobSearchQuery" 
                               @input="filterJobs()"
                               placeholder="Search by job title, department, type, or candidate name..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="space-y-4" x-show="filteredJobs.length">
                    <template x-for="job in filteredJobs" :key="job.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="text-lg font-semibold text-primary" x-text="job.title"></div>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-accent/10 text-accent border border-accent/20" x-text="job.type || 'Full-time'"></span>
                                    </div>
                                    <div class="text-sm text-text-light flex items-center gap-2 mb-3">
                                        <i class="bi bi-building text-accent"></i>
                                        <span x-text="job.department"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-text-light">
                                        <span><strong class="text-primary">Applications:</strong> <span x-text="getRecruitmentApplicants(job).length"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="editJob(job)" 
                                            class="px-3 py-1.5 text-xs font-semibold bg-accent/10 text-accent rounded-lg hover:bg-accent/20 transition-colors"
                                            title="Edit job posting">
                                        Edit
                                    </button>
                                    <button @click="viewJobApplicants(job)" 
                                            class="px-3 py-1.5 text-xs font-semibold bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors"
                                            :title="selectedJobId === job.id ? 'Close applicants list' : 'View applicants for this job'">
                                        <span x-text="selectedJobId === job.id ? 'Close' : 'View Applicants'"></span>
                                    </button>
                                    <button @click="deleteJob(job.id)" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete job posting">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Applicants for this job -->
                            <div x-show="selectedJobId === job.id && getRecruitmentApplicants(job).length" 
                                 class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-primary">Applicants for this position:</h4>
                                    <select @change="sortJobApplicants(job.id, $event.target.value)"
                                            class="text-[11px] px-2 py-1 rounded border bg-bg">
                                        <option value="applied_date_desc">Applied Date (Newest)</option>
                                        <option value="applied_date_asc">Applied Date (Oldest)</option>
                                        <option value="name_asc">Name A–Z</option>
                                        <option value="name_desc">Name Z–A</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="app in getRecruitmentApplicants(job)" :key="app.id">
                                        <div class="p-3 bg-gray-50 rounded-lg space-y-2">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-sm font-medium text-primary" x-text="app.user?.name || 'Unknown'"></div>
                                                    <div class="text-xs text-text-light" x-text="app.user?.email || 'N/A'"></div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-primary/10 text-primary" 
                                                          x-text="app.status || 'Applicant'"></span>
                                                    <button @click="viewJobApplicantProfile(app.user)" 
                                                            class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                            title="View Profile">
                                                        <i class="bi bi-eye text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Documents / Requirements -->
                                            <div class="text-[11px] mt-1" x-show="app.documents && app.documents.length">
                                                <div class="font-semibold text-text-light mb-1">Requirements / Documents:</div>
                                                <div class="space-y-1">
                                                    <template x-for="(doc, index) in app.documents" :key="index">
                                                        <a :href="doc" target="_blank"
                                                           class="inline-flex items-center gap-1 text-primary hover:underline">
                                                            <i class="bi bi-paperclip"></i>
                                                            <span x-text="`Document ${index + 1}`"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                            <!-- Actions: Schedule Interview / Reject -->
                                            <div class="flex items-center gap-2 mt-2">
                                                <button
                                                    @click="openScheduleInterviewModal(app, job)"
                                                    class="px-2 py-1 text-[11px] bg-green-50 text-green-700 rounded hover:bg-green-100">
                                                    Accept to Interview
                                                </button>
                                                <button @click="updateApplicationStatus(app.id, job.id, 'Rejected')"
                                                        class="px-2 py-1 text-[11px] bg-red-50 text-red-700 rounded hover:bg-red-100">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="!filteredJobs.length" class="text-center py-12 text-sm text-text-light">
                    <span x-show="jobSearchQuery">No jobs found matching your search.</span>
                    <span x-show="!jobSearchQuery">No job postings available. <button @click="modalType = 'create-job'" class="text-primary font-semibold hover:underline">Create your first job posting</button></span>
                </div>
            </div>

            <!-- Schedule Interview Modal -->
            <div x-show="modalType === 'schedule-interview'"
                 x-cloak
                 x-transition
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
                <div class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden" x-show="interviewDraft">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-2xl font-black text-primary tracking-tight">Schedule Interview</h3>
                        <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    <div class="p-8 max-h-[85vh] overflow-y-auto">
                        <div class="p-4 bg-gray-50 rounded-xl mb-6">
                            <div class="text-sm font-semibold text-primary" x-text="interviewDraft?.candidate_name || 'Candidate'"></div>
                            <div class="text-xs text-text-light" x-text="interviewDraft?.candidate_email || ''"></div>
                            <div class="text-xs text-text-light mt-1" x-text="interviewDraft?.job_title ? ('Job: ' + interviewDraft.job_title) : ''"></div>
                        </div>

                        <form @submit.prevent="scheduleInterview" class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Interview Date & Time</label>
                                <input type="datetime-local"
                                       x-model="interviewDraft.interview_date"
                                       required
                                       class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Place</label>
                                <input type="text"
                                       x-model="interviewDraft.interview_location"
                                       placeholder="e.g., HR Office / Zoom link / Conference Room"
                                       required
                                       class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Additional Information</label>
                                <textarea rows="4"
                                          x-model="interviewDraft.interview_description"
                                          placeholder="What to bring, who to look for, meeting link, notes..."
                                          class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <input id="sendInterviewEmail"
                                       type="checkbox"
                                       x-model="interviewDraft.send_email"
                                       class="w-4 h-4 text-primary rounded">
                                <label for="sendInterviewEmail" class="text-sm font-semibold text-primary">Send email notification</label>
                            </div>
                            <button type="submit"
                                    class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">
                                Save Interview & Mark as Candidate
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onboarding Tab -->
            <div x-show="activeTab === 'onboarding'" class="main-inner !w-full !max-w-none mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-primary">Onboarding Management</h3>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="onboardingSearchQuery" 
                               @input="filterOnboardingCandidates()"
                               placeholder="Search candidates..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>

                <!-- Onboarding Candidates List -->
                <div class="space-y-4" x-show="filteredOnboardingCandidates.length > 0">
                    <template x-for="candidate in filteredOnboardingCandidates" :key="candidate.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="text-lg font-semibold text-primary" x-text="candidate.name || 'Unknown'"></div>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                              :class="getStatusClass(candidate.status || 'Candidate')"
                                              x-text="candidate.status || 'Candidate'"></span>
                                    </div>
                                    <div class="text-sm text-text-light flex items-center gap-2 mb-2">
                                        <i class="bi bi-envelope text-accent"></i>
                                        <span x-text="candidate.email || 'N/A'"></span>
                                    </div>
                                    <div class="text-xs text-text-light" x-show="candidate.job_title">
                                        <i class="bi bi-briefcase text-accent"></i>
                                        <span x-text="candidate.job_title"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <select @change="updateCandidateOnboardingStatus(candidate.application_id || candidate.id, candidate.id, candidate.job_id, $event.target.value)" 
                                            :value="candidate.status || 'Candidate'"
                                            class="text-xs font-semibold px-3 py-1.5 rounded-full border outline-none"
                                            :class="getStatusClass(candidate.status || 'Candidate')">
                                        <option value="Candidate">Candidate</option>
                                        <option value="Probation">Probation</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                    <button @click="expandedCandidateId = expandedCandidateId === candidate.id ? null : candidate.id" 
                                            class="text-xs px-3 py-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                                        <span x-text="expandedCandidateId === candidate.id ? 'Collapse' : 'Expand'"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Tasks/Requirements Section -->
                            <div x-show="expandedCandidateId === candidate.id" 
                                 class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-primary">Tasks & Requirements</h4>
                                    <div class="flex items-center gap-2">
                                        <button @click="editCandidateTasks(candidate, candidate.job_id)" 
                                                class="text-xs px-2 py-1 bg-accent/10 text-accent rounded hover:bg-accent/20 transition-colors">
                                            <i class="bi bi-pencil"></i> Edit Tasks
                                        </button>
                                        <div class="relative" x-show="candidate.status === 'Candidate' || candidate.status === 'Probation' || candidate.status === 'Regular'">
                                            <button type="button"
                                                    @click="candidate.showPresetMenu = !candidate.showPresetMenu"
                                                    class="text-xs px-2 py-1 bg-primary/5 text-primary rounded hover:bg-primary/10 transition-colors flex items-center gap-1">
                                                <i class="bi bi-magic"></i>
                                                Preset Tasks
                                                <i class="bi" :class="candidate.showPresetMenu ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                            </button>
                                            <div x-show="candidate.showPresetMenu"
                                                 x-cloak
                                                 @click.outside="candidate.showPresetMenu = false"
                                                 class="absolute right-0 mt-1 w-64 bg-white border border-gray-100 rounded-lg shadow-lg text-[11px] z-[140]">
                                                <div class="px-3 pt-2 pb-1 text-[10px] text-text-light border-b border-gray-100">
                                                    <span x-text="candidate.job_title || 'Any Job'"></span>
                                                    <span> • </span>
                                                    <span>Apply preset tasks by status</span>
                                                </div>
                                                <button type="button"
                                                        class="w-full text-left px-3 py-1.5 hover:bg-bg"
                                                        x-show="(candidate.status || 'Candidate') === 'Candidate'"
                                                        @click="applyPresetTasks(candidate, candidate.job_id, 'Candidate'); candidate.showPresetMenu = false;">
                                                    Candidate – Training & Orientation
                                                </button>
                                                <button type="button"
                                                        class="w-full text-left px-3 py-1.5 hover:bg-bg"
                                                        x-show="(candidate.status || 'Candidate') === 'Probation'"
                                                        @click="applyPresetTasks(candidate, candidate.job_id, 'Probation'); candidate.showPresetMenu = false;">
                                                    Probation – Seminars & Evaluation
                                                </button>
                                                <button type="button"
                                                        class="w-full text-left px-3 py-1.5 hover:bg-bg"
                                                        x-show="(candidate.status || 'Candidate') === 'Regular'"
                                                        @click="applyPresetTasks(candidate, candidate.job_id, 'Regular'); candidate.showPresetMenu = false;">
                                                    Regular – Continuous Development
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2" x-show="getCandidateTasks(candidate.id, candidate.job_id).length > 0">
                                    <template x-for="task in getCandidateTasks(candidate.id, candidate.job_id)" :key="task.id">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center gap-3 flex-1">
                                                <input type="checkbox" 
                                                       :checked="task.completed"
                                                       @change="toggleTaskCompletion(task.id, candidate.id)"
                                                       class="w-4 h-4 text-primary rounded cursor-pointer">
                                                <div class="flex items-center gap-2 flex-1">
                                                    <i :class="task.completed ? 'bi bi-check-circle text-green-600' : 'bi bi-circle text-gray-400'"></i>
                                                    <span class="text-sm" 
                                                          :class="task.completed ? 'text-text-light line-through' : 'text-primary font-medium'" 
                                                          x-text="task.title || task.task_title || 'Untitled Task'"></span>
                                                    <span x-show="task.due_date" class="text-[10px] text-text-light" x-text="'Due: ' + (task.due_date || '')"></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button @click="editTask(task)" 
                                                        class="p-1.5 text-accent hover:bg-accent/10 rounded-lg transition-colors"
                                                        title="Edit Task">
                                                    <i class="bi bi-pencil text-xs"></i>
                                                </button>
                                                <button @click="deleteTask(task.id)" 
                                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Delete Task">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="getCandidateTasks(candidate.id, candidate.job_id).length === 0" class="text-center py-4 text-sm text-text-light italic">
                                    No tasks assigned yet. Click "Edit Tasks" to add requirements.
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="filteredOnboardingCandidates.length === 0" class="text-center py-12 text-sm text-text-light">
                    <span x-show="onboardingSearchQuery">No candidates found matching your search.</span>
                    <span x-show="!onboardingSearchQuery">No candidates in Candidate/Probation/Regular status.</span>
                </div>
            </div>

            <!-- Performance Tab -->
            <div x-show="activeTab === 'performance'" class="main-inner !w-full !max-w-none mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-black text-primary">Performance & Assessments</h3>
                    <button @click="modalType = 'create-form'" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-hover transition-colors flex items-center gap-2" title="Create new assessment form">
                        <i class="bi bi-plus-circle"></i>
                        <span class="text-sm font-semibold">Create Form</span>
                    </button>
                </div>

                <!-- Inner tabs: Forms vs Scores -->
                <div class="flex items-center gap-2 mb-6 border-b border-gray-200 pb-2 text-xs font-semibold">
                    <button
                        type="button"
                        @click="performanceTab = 'forms'"
                        :class="performanceTab === 'forms' ? 'text-primary border-b-2 border-primary pb-1' : 'text-text-light pb-1'">
                        Forms
                    </button>
                    <button
                        type="button"
                        @click="performanceTab = 'scores'"
                        :class="performanceTab === 'scores' ? 'text-primary border-b-2 border-primary pb-1' : 'text-text-light pb-1'">
                        Scores
                    </button>
                </div>

                <!-- Assign Assessments to Jobs (Forms tab) -->
                <div x-show="performanceTab === 'forms'" class="mb-6 p-4 bg-primary/5 rounded-xl border border-primary/20">
                    <h4 class="text-sm font-bold text-primary mb-3">Assign Assessments to Jobs</h4>
                    <p class="text-xs text-text-light mb-3">Link assessments to applicable job positions so candidates can complete them during onboarding.</p>
                    <div class="flex flex-wrap gap-3 items-end">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-[10px] font-semibold text-text-light uppercase mb-1">Assessment</label>
                            <select x-model="assignAssessmentFormId" class="w-full p-2 text-sm rounded-lg border bg-white">
                                <option value="">Select assessment</option>
                                <template x-for="form in questionSets" :key="form.id">
                                    <option :value="form.id" x-text="form.title"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-[10px] font-semibold text-text-light uppercase mb-1">Job</label>
                            <select x-model="assignAssessmentJobId" class="w-full p-2 text-sm rounded-lg border bg-white">
                                <option value="">Select job</option>
                                <template x-for="job in jobs" :key="job.id">
                                    <option :value="job.id" x-text="job.title"></option>
                                </template>
                            </select>
                        </div>
                        <button @click="assignAssessmentToJob()" 
                                :disabled="!assignAssessmentFormId || !assignAssessmentJobId"
                                class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Assign this assessment to the selected job">
                            Assign
                        </button>
                    </div>
                </div>

                <!-- Forms: Search & List -->
                <div class="mb-6" x-show="performanceTab === 'forms'">
                    <div class="relative">
                        <input type="text" 
                               x-model="questionSetSearchQuery" 
                               @input="filterQuestionSets()"
                               placeholder="Search forms/question sets..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>

                <!-- Question Sets List -->
                <div class="mb-6" x-show="performanceTab === 'forms'">
                    <h4 class="text-lg font-semibold text-primary mb-4">Available Forms/Assessments</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="filteredQuestionSets.length">
                        <template x-for="form in filteredQuestionSets" :key="form.id">
                            <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="text-base font-semibold text-primary" x-text="form.title"></div>
                                        <div class="text-xs text-text-light mt-1" x-text="form.job_title ? 'For: ' + form.job_title : 'General Assessment'"></div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="editForm(form)" class="p-2 text-accent hover:bg-accent/10 rounded-lg" title="Edit assessment form">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button @click="deleteForm(form.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete assessment form">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-text-light mb-3">
                                    <span x-text="form.questions ? form.questions.length : 0"></span> questions
                                </div>
                                <div class="space-y-2 mb-3">
                                    <template x-for="(question, index) in (form.questions || []).slice(0, 3)" :key="index">
                                        <div class="text-xs text-text-light p-2 bg-gray-50 rounded">
                                            <span class="font-semibold" x-text="(index + 1) + '.'"></span>
                                            <span x-text="question.question_text || question.text || question"></span>
                                        </div>
                                    </template>
                                    <div x-show="(form.questions || []).length > 3" class="text-xs text-primary font-semibold">
                                        + <span x-text="(form.questions || []).length - 3"></span> more questions
                                    </div>
                                </div>
                                <button @click="performanceTab = 'scores'; viewFormCandidates(form)" 
                                        class="w-full mt-3 text-xs font-semibold text-primary hover:underline"
                                        title="View assessment scores">
                                    View Assessment/Form (<span x-text="getFormCandidatesCount(form.id)"></span>)
                                </button>
                            </div>
                        </template>
                    </div>
                    <div x-show="!filteredQuestionSets.length" class="text-center py-8 text-sm text-text-light">
                        <span x-show="questionSetSearchQuery">No question sets found matching your search.</span>
                        <span x-show="!questionSetSearchQuery">No question sets created yet.</span>
                    </div>
                </div>

                <!-- Scores Tab: All Assessments with Scores -->
                <div x-show="performanceTab === 'scores'" class="mt-6">
                    <h4 class="text-lg font-semibold text-primary mb-4">Assessment Scores</h4>
                    <div class="space-y-6">
                        <template x-for="form in questionSets" :key="form.id">
                            <div class="p-5 bg-white rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-semibold text-primary" x-text="form.title"></div>
                                    <button @click="viewFormCandidates(form); selectedFormForScores = form" 
                                            class="text-xs text-primary hover:underline"
                                            title="View details">
                                        View details
                                    </button>
                                </div>
                                <div x-show="getFormCandidates(form.id).length > 0" class="space-y-2">
                                    <template x-for="candidate in getFormCandidates(form.id)" :key="candidate.id">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
                                            <span x-text="candidate.name || candidate.user?.name || 'Unknown'"></span>
                                            <span class="font-semibold text-primary" x-text="candidate.score || candidate.total_score || 'N/A'"></span>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="getFormCandidates(form.id).length === 0" class="text-center py-6 text-sm text-text-light italic">
                                    No scores available
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="!questionSets || questionSets.length === 0" class="text-center py-8 text-sm text-text-light">
                        No assessments created yet.
                    </div>
                </div>
            </div>

            <!-- Recognition Tab -->
            <div x-show="activeTab === 'recognition'" class="main-inner !w-full !max-w-none mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-primary">Recognition & Culture</h3>
                    <button @click="modalType = 'add-recognition'" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-hover transition-colors flex items-center gap-2" title="Post a new recognition">
                        <i class="bi bi-trophy"></i>
                        <span class="text-sm font-semibold">Nominate Outstanding</span>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="recognitionSearchQuery" 
                               @input="filterRecognitions()"
                               placeholder="Search recognitions by candidate, reason, or award type..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>

                <!-- Recognitions List -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="filteredRecognitions.length">
                    <template x-for="recognition in filteredRecognitions" :key="recognition.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="bi bi-trophy text-yellow-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="text-base font-semibold text-primary" x-text="recognition.to || 'Outstanding Candidate'"></div>
                                        <div class="text-xs text-text-light" x-text="recognition.award_type || 'N/A'"></div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="editRecognition(recognition)" class="p-2 text-accent hover:bg-accent/10 rounded-lg" title="Edit recognition">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button @click="deleteRecognition(recognition.id)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete recognition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-sm text-text-light mb-3" x-text="recognition.reason || recognition.description || 'Recognized for outstanding performance'"></div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                          :class="recognition.is_most_outstanding ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'">
                                        <span x-text="recognition.is_most_outstanding ? '⭐ Most Outstanding' : 'Outstanding'"></span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-text-light">
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span class="font-semibold" x-text="recognition.congratulations || 0"></span>
                                        <span>Likes</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-lightning"></i>
                                        <span class="font-semibold" x-text="recognition.boosts || 0"></span>
                                        <span>Boosts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="!filteredRecognitions.length" class="text-center py-12 text-sm text-text-light">
                    <span x-show="recognitionSearchQuery">No recognitions found matching your search.</span>
                    <span x-show="!recognitionSearchQuery">No recognitions posted yet.</span>
                </div>
            </div>

            <!-- Profile Tab -->
            <div x-show="activeTab === 'profile'" class="main-inner !w-full !max-w-none mt-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-primary">Admin Profile</h3>
                    <button @click="editingProfile = !editingProfile" 
                            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-hover transition-colors flex items-center gap-2">
                        <i :class="editingProfile ? 'bi bi-x' : 'bi bi-pencil'"></i>
                        <span class="text-sm font-semibold" x-text="editingProfile ? 'Cancel' : 'Edit Profile'"></span>
                    </button>
                </div>

                <div class="max-w-2xl">
                    <template x-if="!editingProfile">
                        <div class="space-y-4">
                            <!-- Profile Picture -->
                            <div class="flex items-start gap-6 mb-6">
                                <div class="flex-shrink-0">
                                    <div class="w-32 h-32 rounded-full bg-primary/10 flex items-center justify-center overflow-hidden border-4 border-primary/20">
                                        <img x-show="adminProfile.profile_picture" 
                                             :src="adminProfile.profile_picture" 
                                             :alt="adminProfile.name"
                                             class="w-full h-full object-cover">
                                        <i x-show="!adminProfile.profile_picture" class="bi bi-person-circle text-6xl text-primary/50"></i>
                                    </div>
                                </div>
                                <div class="flex-1 pt-4">
                                    <h4 class="text-2xl font-black text-primary mb-1" x-text="adminProfile.name || 'Admin User'"></h4>
                                    <p class="text-sm text-text-light" x-text="adminProfile.email || 'admin@example.com'"></p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Name</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="adminProfile.name || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Email</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="adminProfile.email || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Contact Number</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="adminProfile.contact_no || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Date of Employment</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="adminProfile.date_of_employment || 'N/A'"></div>
                            </div>
                        </div>
                    </template>

                    <template x-if="editingProfile">
                        <form @submit.prevent="updateProfile" class="space-y-4">
                            <!-- Profile Picture Upload -->
                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Profile Picture</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center overflow-hidden border-4 border-primary/20 flex-shrink-0">
                                        <img x-show="adminProfile.profile_picture" 
                                             :src="adminProfile.profile_picture" 
                                             :alt="adminProfile.name"
                                             class="w-full h-full object-cover">
                                        <i x-show="!adminProfile.profile_picture" class="bi bi-person-circle text-4xl text-primary/50"></i>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" 
                                               @change="handleProfilePictureChange($event)"
                                               accept="image/*"
                                               class="text-sm w-full p-2 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                                        <p class="text-xs text-text-light mt-1">Upload a profile picture (JPG, PNG, max 2MB)</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Name</label>
                                <input type="text" x-model="adminProfile.name" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary"
                                       required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Email</label>
                                <input type="email" x-model="adminProfile.email" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary"
                                       required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Contact Number</label>
                                <input type="tel" x-model="adminProfile.contact_no" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Date of Employment</label>
                                <input type="date" x-model="adminProfile.date_of_employment" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                            </div>
                            <button type="submit" 
                                    class="w-full bg-primary text-white py-3 rounded-xl font-semibold hover:bg-primary-hover transition-colors">
                                Save Changes
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Modals must be inside x-data scope -->
    @include('admin.hr1.shared.modals')
    @include('admin.hr1.partials.modals')
</div>

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
        role: 'admin',
        activeTab: 'dashboard',
        sidebarOpen: true,
        modalType: null,
        selectedJob: null,
        selectedJobId: null,
        selectedApplicant: null,
        selectedOnboardingJob: null,
        expandedOnboardingJobId: null,
        expandedCandidateId: null,
        showDownloadMenu: false,
        interviewDraft: null,
        performanceTab: 'forms',
        selectedFormForScores: null,
        scoreFilterType: 'name',
        assignAssessmentFormId: '',
        assignAssessmentJobId: '',
        onboardingSearchQuery: '',
        filteredOnboardingJobs: [],
        filteredOnboardingCandidates: [],
        candidateTasks: @json($candidateTasks ?? []),
        onboardingCandidates: @json($onboardingCandidates ?? []),
        assessmentScores: @json($assessmentScores ?? []),
        editingProfile: false,
        editingForm: null,
        editingTaskSet: null,
        editingRecognition: null,
        editingApplicant: false,
        editingTask: null,
        editingCandidateTasks: null,
        applicants: @json($applicants ?? []),
        filteredApplicants: @json($applicants ?? []),
        applicantSearchQuery: '',
        applicantSortField: 'name',
        applicantSortDirection: 'asc',
        applicantPerPage: 10,
        applicantCurrentPage: 1,
        get paginatedApplicants() {
            const start = (this.applicantCurrentPage - 1) * this.applicantPerPage;
            return this.filteredApplicants.slice(start, start + this.applicantPerPage);
        },
        get applicantPageCount() {
            return Math.ceil((this.filteredApplicants?.length || 0) / this.applicantPerPage) || 1;
        },
        get applicantPageNumbers() {
            const count = this.applicantPageCount;
            const curr = this.applicantCurrentPage;
            const start = Math.max(1, Math.min(curr - 2, count - 4));
            const end = Math.min(count, start + 4);
            return Array.from({ length: end - start + 1 }, (_, i) => start + i);
        },
        jobs: @json($jobs ?? []),
        filteredJobs: @json($jobs ?? []),
        jobSearchQuery: '',
        recognitions: @json($recognitions ?? []),
        filteredRecognitions: @json($recognitions ?? []),
        recognitionSearchQuery: '',
        tasks: @json($tasks ?? []),
        taskSets: @json($taskSets ?? []),
        filteredTaskSets: @json($taskSets ?? []),
        taskSetSearchQuery: '',
        questionSets: @json($questionSets ?? []),
        filteredQuestionSets: @json($questionSets ?? []),
        questionSetSearchQuery: '',
        awardCategories: @json($awardCategories ?? []),
        evalCriteria: @json($evalCriteria ?? []),
        availableModules: @json($availableModules ?? []),
        onboardingCandidates: @json($onboardingCandidates ?? []),
        analytics: @json($analytics ?? []),
        adminProfile: (() => {
            const profile = @json($adminProfile ?? []);
            return {
                name: profile.name || '',
                email: profile.email || '',
                contact_no: profile.contact_no || '',
                date_of_employment: profile.date_of_employment || '',
                profile_picture: profile.profile_picture || ''
            };
        })(),

        downloadApplicantsByStatus(status) {
            const normalized = (status || '').toString().toLowerCase();
            const statusMap = {
                rejected: 'Rejected',
                applicant: 'Applicant',
                candidate: 'Candidate',
                candidates: 'Candidate',
                probation: 'Probation',
                regular: 'Regular'
            };
            const mapped = statusMap[normalized] || status;
            const url = `/api/hr1/applicants/export?status=${encodeURIComponent(mapped)}`;
            window.open(url, '_blank');
        },
        
        getStatusClass(status) {
            const classes = {
                'applicant': 'bg-blue-50 text-blue-700 border-blue-200',
                'Applicant': 'bg-blue-50 text-blue-700 border-blue-200',
                'candidate': 'bg-purple-50 text-purple-700 border-purple-200',
                'Candidate': 'bg-purple-50 text-purple-700 border-purple-200',
                'probation': 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'Probation': 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'regular': 'bg-green-50 text-green-700 border-green-200',
                'Regular': 'bg-green-50 text-green-700 border-green-200',
                'rejected': 'bg-red-50 text-red-700 border-red-200',
                'Rejected': 'bg-red-50 text-red-700 border-red-200'
            };
            return classes[status] || 'bg-gray-50 text-gray-700 border-gray-200';
        },
        
        filterApplicants() {
            const query = this.applicantSearchQuery.toLowerCase();
            if (!query) {
                this.filteredApplicants = [...this.applicants];
            } else {
                this.filteredApplicants = this.applicants.filter(applicant => 
                    (applicant.name || '').toLowerCase().includes(query) ||
                    (applicant.email || '').toLowerCase().includes(query) ||
                    (applicant.position || '').toLowerCase().includes(query) ||
                    (applicant.contact_no || '').toLowerCase().includes(query) ||
                    (applicant.status || '').toLowerCase().includes(query) ||
                    (applicant.employee_id || '').toLowerCase().includes(query)
                );
            }
            this.applicantCurrentPage = 1;
            this.sortApplicants(this.applicantSortField);
        },
        
        sortApplicants(field) {
            if (this.applicantSortField === field) {
                this.applicantSortDirection = this.applicantSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.applicantSortField = field;
                this.applicantSortDirection = 'asc';
            }
            
            this.filteredApplicants.sort((a, b) => {
                const aVal = (a[field] || '').toString().toLowerCase();
                const bVal = (b[field] || '').toString().toLowerCase();
                if (this.applicantSortDirection === 'asc') {
                    return aVal.localeCompare(bVal);
                } else {
                    return bVal.localeCompare(aVal);
                }
            });
        },
        
        filterJobs() {
            const query = this.jobSearchQuery.toLowerCase();
            if (!query) {
                this.filteredJobs = [...this.jobs];
                return;
            }
            this.filteredJobs = this.jobs.filter(job => 
                (job.title || '').toLowerCase().includes(query) ||
                (job.department || '').toLowerCase().includes(query) ||
                (job.type || '').toLowerCase().includes(query) ||
                this.getRecruitmentApplicants(job).some(app => 
                    (app.user?.name || '').toLowerCase().includes(query) ||
                    (app.user?.email || '').toLowerCase().includes(query)
                )
            );
        },

        getRecruitmentApplicants(job) {
            const apps = job?.applications_hr1 || [];
            return apps.filter(app => (app.status || 'Applicant') === 'Applicant');
        },
        syncJobsFromApplicant(applicant) {
            this.jobs.forEach(job => {
                if (!job.applications_hr1) return;
                job.applications_hr1.forEach(app => {
                    if ((app.user_id || app.user?.id) == applicant.id) {
                        app.status = applicant.status;
                        if (app.user) app.user.status = applicant.status;
                    }
                });
            });
            this.jobs = [...this.jobs];
            this.filteredJobs = [...this.filteredJobs];
        },
        
        filterTaskSets() {
            const query = this.taskSetSearchQuery.toLowerCase();
            if (!query) {
                this.filteredTaskSets = [...this.taskSets];
                return;
            }
            this.filteredTaskSets = this.taskSets.filter(ts => 
                (ts.name || '').toLowerCase().includes(query) ||
                (ts.description || '').toLowerCase().includes(query)
            );
        },
        
        filterQuestionSets() {
            const query = this.questionSetSearchQuery.toLowerCase();
            if (!query) {
                this.filteredQuestionSets = [...this.questionSets];
                return;
            }
            this.filteredQuestionSets = this.questionSets.filter(qs => 
                (qs.title || '').toLowerCase().includes(query) ||
                (qs.description || '').toLowerCase().includes(query)
            );
        },
        
        filterRecognitions() {
            const query = this.recognitionSearchQuery.toLowerCase();
            if (!query) {
                this.filteredRecognitions = [...this.recognitions];
                return;
            }
            this.filteredRecognitions = this.recognitions.filter(rec => 
                (rec.to || '').toLowerCase().includes(query) ||
                (rec.from || '').toLowerCase().includes(query) ||
                (rec.reason || '').toLowerCase().includes(query) ||
                (rec.award_type || '').toLowerCase().includes(query)
            );
        },
        
        updateApplicantStatus(id, status) {
            // Map incoming keys to canonical casing
            const statusMap = {
                'applicant': 'Applicant',
                'candidate': 'Candidate',
                'probation': 'Probation',
                'regular': 'Regular',
                'rejected': 'Rejected'
            };
            const mappedStatus = statusMap[(status || '').toLowerCase()] || status || 'Applicant';
            
            fetch(`/api/hr1/applicants/${id}/status`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: mappedStatus })
            }).then(res => res.json()).then(data => {
                const applicant = this.applicants.find(a => a.id == id);
                if (applicant) {
                    applicant.status = mappedStatus;
                    this.filterApplicants();
                }
            }).catch(err => {
                console.error('Error updating status:', err);
                alert('Failed to update status');
            });
        },
        
        viewApplicantProfile(applicant) {
            this.selectedApplicant = applicant;
            this.modalType = 'view-profile';
        },
        
        
        addApplicant() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch('/api/hr1/applicants', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to add applicant');
                }
                return data;
            }).then(data => {
                this.applicants.push(data);
                this.filterApplicants();
                // If new user has applications, add them to jobs so recruitment list updates
                if (data.applications_hr1 && data.applications_hr1.length) {
                    data.applications_hr1.forEach(app => {
                        const job = this.jobs.find(j => j.id == app.job_posting_id);
                        if (job) {
                            if (!job.applications_hr1) job.applications_hr1 = [];
                            job.applications_hr1.push({ ...app, user: data });
                            this.jobs = [...this.jobs];
                            this.filteredJobs = [...this.filteredJobs];
                        }
                    });
                }
                this.modalType = null;
                form.reset();
                alert('Candidate added successfully!');
            }).catch(err => {
                console.error('Error adding applicant:', err);
                alert(err.message || 'Failed to add candidate. Please try again.');
            });
        },
        
        viewJobApplicants(job) {
            this.selectedJobId = this.selectedJobId === job.id ? null : job.id;
        },
        
        filterOnboardingCandidates() {
            const query = this.onboardingSearchQuery.toLowerCase();
            let candidates = this.onboardingCandidates || [];
            
            // If no onboarding candidates from backend, get from jobs
            if (candidates.length === 0) {
                candidates = [];
                this.jobs.forEach(job => {
                    if (job.applications_hr1) {
                        job.applications_hr1.forEach(app => {
                            if (app.status === 'Candidate' || app.status === 'Probation' || app.status === 'Regular') {
                                candidates.push({
                                    id: app.user?.id || app.user_id,
                                    name: app.user?.name,
                                    email: app.user?.email,
                                    status: app.status,
                                    application_id: app.id,
                                    job_id: job.id,
                                    job_title: job.title
                                });
                            }
                        });
                    }
                });
            }
            
            if (!query) {
                this.filteredOnboardingCandidates = candidates;
                return;
            }
            
            this.filteredOnboardingCandidates = candidates.filter(candidate => 
                (candidate.name || '').toLowerCase().includes(query) ||
                (candidate.email || '').toLowerCase().includes(query) ||
                (candidate.job_title || '').toLowerCase().includes(query)
            );
        },
        
        getOnboardingCandidatesForJob(jobId) {
            if (!jobId) return [];
            const job = this.jobs.find(j => j.id == jobId);
            if (!job || !job.applications_hr1) return [];
            return job.applications_hr1.filter(app => 
                app.status === 'Candidate' || app.status === 'Probation' || app.status === 'Regular'
            );
        },

        sortJobApplicants(jobId, mode) {
            const job = this.jobs.find(j => j.id == jobId);
            if (!job || !job.applications_hr1) return;

            const sorted = [...job.applications_hr1].sort((a, b) => {
                if (mode === 'name_asc' || mode === 'name_desc') {
                    const aName = (a.user?.name || '').toLowerCase();
                    const bName = (b.user?.name || '').toLowerCase();
                    return mode === 'name_asc' ? aName.localeCompare(bName) : bName.localeCompare(aName);
                }
                if (mode === 'applied_date_asc' || mode === 'applied_date_desc') {
                    const aDate = new Date(a.applied_date || a.created_at || a.user?.applied_date || 0);
                    const bDate = new Date(b.applied_date || b.created_at || b.user?.applied_date || 0);
                    return mode === 'applied_date_asc' ? aDate - bDate : bDate - aDate;
                }
                return 0;
            });

            // Re-assign to ensure Alpine reacts (avoid in-place mutation pitfalls)
            job.applications_hr1 = sorted;
            this.jobs = [...this.jobs];
            this.filteredJobs = [...this.filteredJobs];
        },

        updateApplicationStatus(applicationId, jobId, status) {
            fetch(`/api/hr1/applications/${applicationId}`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status })
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update application');
                }
                return data;
            }).then(data => {
                const job = this.jobs.find(j => j.id == jobId);
                if (job && job.applications_hr1) {
                    const app = job.applications_hr1.find(a => a.id == applicationId);
                    if (app) {
                        app.status = data.status;
                    }
                }
                this.filterJobs();
                alert('Application status updated');
            }).catch(err => {
                console.error('Error updating application:', err);
                alert(err.message || 'Failed to update application');
            });
        },

        openScheduleInterviewModal(app, job) {
            this.interviewDraft = {
                application_id: app?.id,
                job_id: job?.id,
                job_title: job?.title || '',
                candidate_name: app?.user?.name || 'Candidate',
                candidate_email: app?.user?.email || '',
                interview_date: '',
                interview_location: '',
                interview_description: '',
                send_email: true
            };
            this.modalType = 'schedule-interview';
        },

        scheduleInterview() {
            if (!this.interviewDraft?.application_id) {
                alert('Missing application reference.');
                return;
            }

            fetch(`/api/hr1/applications/${this.interviewDraft.application_id}/interview`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    interview_date: this.interviewDraft.interview_date,
                    interview_location: this.interviewDraft.interview_location,
                    interview_description: this.interviewDraft.interview_description
                })
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || data.error || 'Failed to schedule interview');
                return data;
            }).then(data => {
                // Expect application returned (or at least status) from backend
                const job = this.jobs.find(j => j.id == this.interviewDraft.job_id);
                if (job && job.applications_hr1) {
                    const app = job.applications_hr1.find(a => a.id == this.interviewDraft.application_id);
                    if (app) {
                        app.status = data.status || 'Candidate';
                        if (data.interview_date) app.interview_date = data.interview_date;
                        if (data.interview_location) app.interview_location = data.interview_location;
                        if (data.interview_description) app.interview_description = data.interview_description;
                    }
                }
                this.filterJobs();
                this.modalType = null;
                this.interviewDraft = null;
                alert('Interview scheduled and status updated.');
            }).catch(err => {
                console.error('Error scheduling interview:', err);
                alert(err.message || 'Failed to schedule interview.');
            });
        },
        
        getCandidateTasks(userId, jobId) {
            if (!userId) return [];
            let tasks = this.candidateTasks.filter(task => 
                task.user_id == userId && (task.job_posting_id == jobId || !jobId)
            );
            // Ensure tasks have title field
            return tasks.map(task => ({
                ...task,
                title: task.title || task.task_title || 'Untitled Task'
            }));
        },

        applyPresetTasks(candidate, jobId, phase) {
            if (!candidate || !jobId) return;

            const userId = candidate.user_id || candidate.id;
            const jobTitle = (candidate.job_title || '').toLowerCase();
            const currentPhase = (candidate.status || '').toString();

            // Enforce: only apply presets matching the candidate's current status
            if (currentPhase && currentPhase !== phase) {
                alert(`Preset tasks for ${phase} can only be applied when status is ${phase}.`);
                return;
            }

            // Default presets for any job (includes evaluation/assessment tasks)
            const genericPresets = {
                Candidate: [
                    'Complete onboarding orientation',
                    'Submit required HR documents',
                    'Review hospital policies and procedures',
                    'Complete initial performance assessment',
                ],
                Probation: [
                    'Attend monthly feedback session with supervisor',
                    'Complete required training modules',
                    'Participate in peer evaluation session',
                    'Complete probation period assessment',
                ],
                Regular: [
                    'Enroll in continuous education program',
                    'Attend annual performance review',
                    'Participate in department improvement project',
                    'Complete annual performance evaluation',
                ],
            };

            // Specialized presets for Registered Nurse roles
            const rnPresets = {
                Candidate: [
                    'Complete RN orientation module',
                    'Shadow senior nurse for 3 shifts',
                    'Review emergency protocols handbook',
                    'Complete initial clinical assessment',
                ],
                Probation: [
                    'Attend patient safety seminar',
                    'Present case study to nursing supervisor',
                    'Complete advanced skills checklist',
                    'Complete probation evaluation assessment',
                ],
                Regular: [
                    'Lead a nursing team huddle',
                    'Facilitate one clinical skills training',
                    'Submit quarterly patient care improvement report',
                    'Complete annual nursing performance evaluation',
                ],
            };

            const isRN = jobTitle.includes('registered nurse');
            const bank = isRN ? rnPresets : genericPresets;
            const titles = bank[phase] || genericPresets[phase] || [];
            if (!titles.length) return;

            // Fire requests sequentially to reuse existing API
            const createOne = (title) => {
                const formData = new FormData();
                formData.append('title', title);
                formData.append('description', '');
                formData.append('user_id', userId);
                formData.append('job_posting_id', jobId);

                return fetch('/api/hr1/applicant-tasks', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                }).then(res => res.ok ? res.json() : res.json().then(d => Promise.reject(d)));
            };

            (async () => {
                try {
                    for (const title of titles) {
                        const task = await createOne(title);
                        this.candidateTasks.push(task);
                    }
                    alert('Preset tasks added successfully.');
                } catch (err) {
                    console.error('Error applying preset tasks:', err);
                    alert('Failed to apply preset tasks.');
                }
            })();
        },
        
        updateCandidateOnboardingStatus(applicationId, userId, jobId, status) {
            const statusMap = {
                'candidate': 'Candidate',
                'probation': 'Probation',
                'regular': 'Regular',
                'rejected': 'Rejected'
            };
            const mappedStatus = statusMap[(status || '').toString().toLowerCase()] || status;

            if (!confirm('Are you sure you want to update this user\'s status to "' + mappedStatus + '"?')) {
                return;
            }

            // Use method spoofing + form payload for maximum CSRF compatibility
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const formData = new FormData();
            formData.append('_token', csrf);
            formData.append('_method', 'PATCH');
            formData.append('status', mappedStatus);

            fetch(`/api/hr1/applications/${applicationId}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update status');
                }
                return data;
            }).then(data => {
                // Update in jobs
                const job = this.jobs.find(j => j.id == jobId);
                if (job && job.applications_hr1) {
                    const app = job.applications_hr1.find(a => a.id == applicationId);
                    if (app) {
                        app.status = data.status || mappedStatus;
                    }
                }
                // Update in onboarding candidates
                const candidate = this.onboardingCandidates.find(c => c.id == userId);
                if (candidate) {
                    candidate.status = data.status || mappedStatus;
                }
                this.filterOnboardingCandidates();
            }).catch(err => {
                console.error('Error updating status:', err);
                alert(err.message || 'Failed to update candidate status');
            });
        },
        
        editCandidateTasks(candidate, jobId) {
            this.editingCandidateTasks = { ...candidate, jobId };
            this.modalType = 'edit-candidate-tasks';
        },

        addTaskToCandidate() {
            if (!this.editingCandidateTasks) {
                alert('No candidate selected');
                return;
            }

            const form = event.target;
            const formData = new FormData(form);

            formData.append(
                'user_id',
                this.editingCandidateTasks.user_id || this.editingCandidateTasks.id
            );
            formData.append('job_posting_id', this.editingCandidateTasks.jobId);

            fetch('/api/hr1/applicant-tasks', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to add task');
                }
                return data;
            }).then(data => {
                // Push new task into candidateTasks collection so UI updates immediately
                this.candidateTasks.push(data);
                this.modalType = 'edit-candidate-tasks';
                form.reset();
                alert('Task added successfully!');
            }).catch(err => {
                console.error('Error adding task:', err);
                alert(err.message || 'Failed to add task. Please try again.');
            });
        },
        
        deleteOnboardingCandidate(applicationId, userId) {
            if (confirm('Remove this candidate from onboarding?')) {
                fetch(`/api/hr1/applications/${applicationId}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to remove candidate');
                    }
                    return data;
                }).then(data => {
                    // Remove from jobs
                    this.jobs.forEach(job => {
                        if (job.applications_hr1) {
                            job.applications_hr1 = job.applications_hr1.filter(a => a.id != applicationId);
                        }
                    });
                    // Remove from onboarding candidates
                    this.onboardingCandidates = this.onboardingCandidates.filter(c => c.id != userId);
                    this.filterOnboardingCandidates();
                    alert('Candidate removed successfully!');
                }).catch(err => {
                    console.error('Error deleting candidate:', err);
                    alert(err.message || 'Failed to remove candidate. Please try again.');
                });
            }
        },
        
        toggleTaskCompletion(taskId, userId) {
            const task = this.candidateTasks.find(t => t.id == taskId);
            if (!task) {
                console.error('Task not found:', taskId);
                alert('Task not found');
                return;
            }
            
            const newCompleted = !task.completed;
            
            fetch(`/api/hr1/applicant-tasks/${taskId}/status`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ completed: newCompleted })
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update task status');
                }
                return data;
            }).then(data => {
                task.completed = newCompleted;
                if (task.completed) {
                    task.completed_at = new Date().toISOString();
                } else {
                    task.completed_at = null;
                }
            }).catch(err => {
                console.error('Error updating task:', err);
                alert(err.message || 'Failed to update task status');
            });
        },
        
        editTask(task) {
            this.editingTask = { ...task };
            this.modalType = 'edit-task';
        },
        
        updateTask() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/applicant-tasks/${this.editingTask.id}`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update task');
                }
                return data;
            }).then(data => {
                const index = this.candidateTasks.findIndex(t => t.id == data.id);
                if (index !== -1) {
                    this.candidateTasks[index] = { ...this.candidateTasks[index], ...data };
                }
                this.modalType = null;
                this.editingTask = null;
                alert('Task updated successfully!');
            }).catch(err => {
                console.error('Error updating task:', err);
                alert(err.message || 'Failed to update task. Please try again.');
            });
        },
        
        deleteTask(taskId) {
            if (confirm('Delete this task?')) {
                fetch(`/api/hr1/applicant-tasks/${taskId}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to delete task');
                    }
                    return data;
                }).then(data => {
                    this.candidateTasks = this.candidateTasks.filter(t => t.id != taskId);
                    alert('Task deleted successfully!');
                }).catch(err => {
                    console.error('Error deleting task:', err);
                    alert(err.message || 'Failed to delete task. Please try again.');
                });
            }
        },
        
        viewFormCandidates(form) {
            this.selectedFormForScores = form;
        },
        
        assignAssessmentToJob() {
            if (!this.assignAssessmentFormId || !this.assignAssessmentJobId) return;
            fetch(`/api/hr1/question-sets/${this.assignAssessmentFormId}/assign-job`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ job_posting_id: parseInt(this.assignAssessmentJobId) })
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Failed to assign');
                return data;
            }).then(data => {
                const form = this.questionSets.find(q => q.id == this.assignAssessmentFormId);
                if (form) {
                    form.job_posting_id = data.job_posting_id;
                    form.job_title = this.jobs.find(j => j.id == this.assignAssessmentJobId)?.title || null;
                }
                this.assignAssessmentFormId = '';
                this.assignAssessmentJobId = '';
                alert('Assessment assigned to job successfully!');
            }).catch(err => {
                console.error('Error assigning assessment:', err);
                alert(err.message || 'Failed to assign assessment.');
            });
        },
        
        getFormCandidatesCount(formId) {
            if (!formId) return 0;
            return this.assessmentScores.filter(score => score.question_set_id == formId).length;
        },
        
        getFormCandidates(formId) {
            if (!formId) return [];
            let candidates = this.assessmentScores.filter(score => score.question_set_id == formId);
            
            // Apply sorting
            if (this.scoreFilterType === 'name') {
                candidates.sort((a, b) => {
                    const aName = (a.name || a.user?.name || '').toLowerCase();
                    const bName = (b.name || b.user?.name || '').toLowerCase();
                    return aName.localeCompare(bName);
                });
            } else if (this.scoreFilterType === 'score') {
                candidates.sort((a, b) => {
                    const aScore = parseFloat(a.score || a.total_score || 0);
                    const bScore = parseFloat(b.score || b.total_score || 0);
                    return bScore - aScore; // Highest to lowest
                });
            }
            
            return candidates;
        },
        
        deleteJob(id) {
            if (confirm('Delete this job posting?')) {
                fetch(`/api/hr1/jobs/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to delete job');
                    }
                    return data;
                }).then(data => {
                    this.jobs = this.jobs.filter(j => j.id != id);
                    this.filterJobs();
                    alert('Job deleted successfully!');
                }).catch(err => {
                    console.error('Error deleting job:', err);
                    alert(err.message || 'Failed to delete job. Please try again.');
                });
            }
        },
        
        createJob() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch('/api/hr1/jobs', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to create job');
                }
                return data;
            }).then(data => {
                this.jobs.push(data);
                this.filterJobs();
                this.modalType = null;
                form.reset();
                alert('Job posted successfully!');
            }).catch(err => {
                console.error('Error creating job:', err);
                alert(err.message || 'Failed to create job. Please try again.');
            });
        },
        
        editJob(job) {
            this.selectedJob = { ...job };
            this.modalType = 'edit-job';
        },
        
        updateJob() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/jobs/${this.selectedJob.id}`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update job');
                }
                return data;
            }).then(data => {
                const index = this.jobs.findIndex(j => j.id == data.id);
                if (index !== -1) {
                    this.jobs[index] = data;
                    this.filterJobs();
                }
                this.modalType = null;
                alert('Job updated successfully!');
            }).catch(err => {
                console.error('Error updating job:', err);
                alert(err.message || 'Failed to update job. Please try again.');
            });
        },
        
        getApplicantsForJob(jobId) {
            if (!jobId) return this.onboardingCandidates;
            const job = this.jobs.find(j => j.id == jobId);
            if (!job || !job.applications_hr1) return this.onboardingCandidates;
            return job.applications_hr1
                .filter(app => app.user && (app.status === 'Candidate' || app.status === 'Probation' || app.status === 'Regular'))
                .map(app => app.user)
                .filter(Boolean);
        },
        
        viewJobApplicantProfile(applicant) {
            this.selectedApplicant = applicant;
            this.modalType = 'view-profile';
        },
        
        getRemainingTasks(applicantId) {
            // Get tasks for this applicant from applicant_tasks_hr1 table
            // This would need to be fetched from the backend
            // For now, return empty array - will be populated when backend is ready
            return [];
        },
        
        assignTaskSetToJob(taskSetId) {
            if (!this.selectedOnboardingJob) {
                alert('Please select a job first');
                return;
            }
            // Implementation needed
        },
        
        editTaskSet(taskSet) {
            this.editingTaskSet = taskSet;
            this.modalType = 'edit-task-set';
        },
        
        deleteTaskSet(id) {
            if (confirm('Delete this task set?')) {
                fetch(`/api/hr1/task-sets/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to delete task set');
                    }
                    return data;
                }).then(data => {
                    this.taskSets = this.taskSets.filter(ts => ts.id != id);
                    this.filterTaskSets();
                    alert('Task set deleted successfully!');
                }).catch(err => {
                    console.error('Error deleting task set:', err);
                    alert(err.message || 'Failed to delete task set. Please try again.');
                });
            }
        },
        
        editForm(form) {
            this.editingForm = form;
            this.modalType = 'edit-form';
        },
        
        deleteForm(id) {
            if (confirm('Delete this form?')) {
                fetch(`/api/hr1/question-sets/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to delete form');
                    }
                    return data;
                }).then(data => {
                    this.questionSets = this.questionSets.filter(qs => qs.id != id);
                    this.filterQuestionSets();
                    alert('Form deleted successfully!');
                }).catch(err => {
                    console.error('Error deleting form:', err);
                    alert(err.message || 'Failed to delete form. Please try again.');
                });
            }
        },
        
        editRecognition(recognition) {
            this.editingRecognition = recognition;
            this.modalType = 'edit-recognition';
        },
        
        deleteRecognition(id) {
            if (confirm('Delete this recognition?')) {
                fetch(`/api/hr1/recognitions/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Failed to delete recognition');
                    }
                    return data;
                }).then(data => {
                    this.recognitions = this.recognitions.filter(r => r.id != id);
                    this.filterRecognitions();
                    alert('Recognition deleted successfully!');
                }).catch(err => {
                    console.error('Error deleting recognition:', err);
                    alert(err.message || 'Failed to delete recognition. Please try again.');
                });
            }
        },
        
        handleProfilePictureChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.adminProfile.profile_picture = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        updateProfile() {
            const formData = new FormData();
            formData.append('name', this.adminProfile.name);
            formData.append('email', this.adminProfile.email);
            formData.append('contact_no', this.adminProfile.contact_no);
            formData.append('date_of_employment', this.adminProfile.date_of_employment);
            
            // If profile picture is a data URL, convert it to file
            if (this.adminProfile.profile_picture && this.adminProfile.profile_picture.startsWith('data:')) {
                // For now, just send the data URL. In production, upload to server first
                formData.append('profile_picture', this.adminProfile.profile_picture);
            }
            
            fetch('/api/hr1/admin/profile', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update profile');
                }
                return data;
            }).then(data => {
                this.adminProfile = { ...this.adminProfile, ...data };
                this.editingProfile = false;
                alert('Profile updated successfully');
            }).catch(err => {
                console.error('Error updating profile:', err);
                alert(err.message || 'Failed to update profile. Please try again.');
            });
        },
        
        updateApplicantInfo() {
            if (!this.selectedApplicant || !this.editingApplicant) {
                alert('Please click Edit button first');
                return;
            }
            
            const form = event.target;
            const formData = new FormData(form);
            const newStatus = formData.get('status');
            const oldStatus = this.selectedApplicant.status;
            
            // Confirm if status is being changed
            if (newStatus && (newStatus !== oldStatus) && !confirm('Are you sure you want to update this user\'s status to "' + newStatus + '"?')) {
                return;
            }
            
            // Normalize status casing if provided
            if (newStatus) {
                const statusMap = {
                    'applicant': 'Applicant',
                    'candidate': 'Candidate',
                    'probation': 'Probation',
                    'regular': 'Regular',
                    'rejected': 'Rejected'
                };
                const rawStatus = (newStatus || '').toString().toLowerCase();
                formData.set('status', statusMap[rawStatus] || newStatus);
            }
            
            fetch(`/api/hr1/applicants/${this.selectedApplicant.id}`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update applicant');
                }
                return data;
            }).then(data => {
                const index = this.applicants.findIndex(a => a.id == data.id);
                if (index !== -1) {
                    this.applicants[index] = data;
                    this.filterApplicants();
                }
                // Update onboarding candidates in the current session if present
                const candidate = this.onboardingCandidates.find(c => c.id == data.id);
                if (candidate) {
                    candidate.status = data.status;
                }
                // Keep embedded user info in job applications in sync so onboarding view reflects changes
                this.jobs.forEach(job => {
                    if (!job.applications_hr1) return;
                    job.applications_hr1.forEach(app => {
                        if (app.user && app.user.id == data.id) {
                            app.user = { ...app.user, ...data };
                            if (data.status) {
                                app.status = data.status;
                            }
                        }
                    });
                });
                this.filterOnboardingCandidates();
                this.modalType = null;
                this.editingApplicant = false;
                alert('Applicant updated successfully');
            }).catch(err => {
                console.error('Error updating applicant:', err);
                alert(err.message || 'Failed to update applicant. Please try again.');
            });
        },
        
        editApplicant(applicant) {
            this.selectedApplicant = { ...applicant };
            this.editingApplicant = true;
            this.modalType = 'edit-applicant';
        },
        
        createTaskSet() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch('/api/hr1/task-sets', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to create task set');
                }
                return data;
            }).then(data => {
                this.taskSets.push(data);
                this.filterTaskSets();
                this.modalType = null;
                form.reset();
                alert('Task set created successfully!');
            }).catch(err => {
                console.error('Error creating task set:', err);
                alert(err.message || 'Failed to create task set. Please try again.');
            });
        },
        
        updateTaskSet() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/task-sets/${this.editingTaskSet.id}`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update task set');
                }
                return data;
            }).then(data => {
                const index = this.taskSets.findIndex(ts => ts.id == data.id);
                if (index !== -1) {
                    this.taskSets[index] = data;
                    this.filterTaskSets();
                }
                this.modalType = null;
                this.editingTaskSet = null;
                alert('Task set updated successfully!');
            }).catch(err => {
                console.error('Error updating task set:', err);
                alert(err.message || 'Failed to update task set. Please try again.');
            });
        },
        
        createForm() {
            const form = event.target;
            const formData = new FormData(form);
            
            // Collect questions data from the form
            const questions = [];
            const questionInputs = form.querySelectorAll('[name^="questions["]');
            
            // Group by question index
            const questionMap = {};
            questionInputs.forEach(input => {
                const match = input.name.match(/questions\[(\d+)\]\[(\w+)\](?:\[(\d+)\])?/);
                if (match) {
                    const index = parseInt(match[1]);
                    const field = match[2];
                    const optIndex = match[3] ? parseInt(match[3]) : null;
                    
                    if (!questionMap[index]) {
                        questionMap[index] = { options: [] };
                    }
                    
                    if (optIndex !== null) {
                        // This is an option
                        if (!questionMap[index].options[optIndex]) {
                            questionMap[index].options[optIndex] = '';
                        }
                        questionMap[index].options[optIndex] = input.value;
                    } else {
                        // This is a question field
                        questionMap[index][field] = input.value;
                    }
                }
            });
            
            // Convert map to array
            Object.keys(questionMap).forEach(index => {
                const q = questionMap[index];
                if (q.question_text) {
                    questions.push({
                        question_text: q.question_text,
                        question_type: q.question_type || 'text',
                        options: q.options && q.options.length > 0 ? q.options.filter(opt => opt) : null,
                        is_required: true
                    });
                }
            });
            
            // Add questions as JSON
            formData.append('questions', JSON.stringify(questions));
            
            fetch('/api/hr1/question-sets', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to create form');
                }
                // Ensure newly created form has questions available in UI
                if (!data.questions) data.questions = questions;
                return data;
            }).then(data => {
                this.questionSets.push(data);
                this.filterQuestionSets();
                this.modalType = null;
                form.reset();
                alert('Form created successfully!');
            }).catch(err => {
                console.error('Error creating form:', err);
                alert(err.message || 'Failed to create form. Please try again.');
            });
        },
        
        updateForm() {
            const form = event.target;
            const formData = new FormData(form);
            
            // If job_id is provided, add it
            if (formData.get('job_id')) {
                formData.append('job_id', formData.get('job_id'));
            }

            // Collect questions (supports editing + adding + removing)
            const questions = [];
            const questionInputs = form.querySelectorAll('[name^="questions["]');
            const questionMap = {};
            questionInputs.forEach(input => {
                const match = input.name.match(/questions\[(\d+)\]\[(\w+)\](?:\[(\d+)\])?/);
                if (match) {
                    const index = parseInt(match[1]);
                    const field = match[2];
                    const optIndex = match[3] ? parseInt(match[3]) : null;

                    if (!questionMap[index]) {
                        questionMap[index] = { options: [] };
                    }

                    if (optIndex !== null) {
                        if (!questionMap[index].options[optIndex]) {
                            questionMap[index].options[optIndex] = '';
                        }
                        questionMap[index].options[optIndex] = input.value;
                    } else {
                        questionMap[index][field] = input.value;
                    }
                }
            });

            Object.keys(questionMap).forEach(index => {
                const q = questionMap[index];
                if (q.question_text) {
                    const payload = {
                        question_text: q.question_text,
                        question_type: q.question_type || 'text',
                        options: q.options && q.options.length > 0 ? q.options.filter(opt => opt) : null,
                        is_required: true,
                    };
                    if (q.id) payload.id = q.id;
                    questions.push(payload);
                }
            });

            formData.append('questions', JSON.stringify(questions));
            
            fetch(`/api/hr1/question-sets/${this.editingForm.id}`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update form');
                }
                // Keep UI consistent even if backend omits questions in response
                if (!data.questions) data.questions = questions;
                return data;
            }).then(data => {
                const index = this.questionSets.findIndex(qs => qs.id == data.id);
                if (index !== -1) {
                    this.questionSets[index] = data;
                    this.filterQuestionSets();
                }
                this.modalType = null;
                this.editingForm = null;
                alert('Form updated successfully!');
            }).catch(err => {
                console.error('Error updating form:', err);
                alert(err.message || 'Failed to update form. Please try again.');
            });
        },
        
        createRecognition() {
            const form = event.target;
            const formData = new FormData(form);
            
            // Ensure is_most_outstanding is set correctly
            if (!formData.has('is_most_outstanding')) {
                formData.append('is_most_outstanding', '0');
            }
            
            fetch('/api/hr1/recognitions', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to create recognition');
                }
                return data;
            }).then(data => {
                this.recognitions.push(data);
                this.filterRecognitions();
                this.modalType = null;
                form.reset();
                alert('Recognition posted successfully!');
            }).catch(err => {
                console.error('Error creating recognition:', err);
                alert(err.message || 'Failed to post recognition. Please try again.');
            });
        },
        
        updateRecognition() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/recognitions/${this.editingRecognition.id}`, {
                method: 'PATCH',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Failed to update recognition');
                }
                return data;
            }).then(data => {
                const index = this.recognitions.findIndex(r => r.id == data.id);
                if (index !== -1) {
                    this.recognitions[index] = data;
                    this.filterRecognitions();
                }
                this.modalType = null;
                this.editingRecognition = null;
                alert('Recognition updated successfully!');
            }).catch(err => {
                console.error('Error updating recognition:', err);
                alert(err.message || 'Failed to update recognition. Please try again.');
            });
        },
        
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
        },
        
        init() {
            // Initialize filtered lists
            this.filteredOnboardingJobs = [...this.jobs];
            this.filterOnboardingCandidates();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    
    // Initialize chart - Recruitment Performance: Applicant, Candidate, Probation, Regular, Rejected
    const ctx = document.getElementById('recruitmentChart');
    if (ctx && typeof Chart !== 'undefined') {
        const analytics = @json($analytics ?? []);
        const statusCounts = analytics.statusCounts || {};
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected'],
                datasets: [{
                    label: 'Count',
                    data: [
                        statusCounts.Applicant || 0,
                        statusCounts.Candidate || 0,
                        statusCounts.Probation || 0,
                        statusCounts.Regular || 0,
                        statusCounts.Rejected || 0
                    ],
                    backgroundColor: ['#3B82F6', '#8B5CF6', '#F59E0B', '#10B981', '#EF4444'],
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
