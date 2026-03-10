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
                    Hospital Command Center: <span class="text-accent font-black uppercase bg-accent/5 px-3 py-1 rounded-xl">Candidate Context</span>.
                </div>
            </div>
            
            <!-- Candidate Dashboard Content -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center group-hover:bg-yellow-200 transition-colors flex-shrink-0">
                                <i class="bi bi-clock text-yellow-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Pending Tasks</h4>
                                    <p class="text-[10px] text-text-light">Awaiting action</p>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="getPendingTasksCount()"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                                <i class="bi bi-check-circle text-green-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Completed Tasks</h4>
                                    <p class="text-[10px] text-text-light">Finished</p>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="getCompletedTasksCount()"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card !w-full group cursor-pointer text-left">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                                <i class="bi bi-graph-up text-purple-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h4 class="text-[10px] font-semibold text-text-light uppercase tracking-wide">Assessment Progress</h4>
                                    <p class="text-[10px] text-text-light">Completion rate</p>
                                </div>
                                <div class="text-3xl font-black text-primary" x-text="getAssessmentProgress() + '%'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Tab (Onboarding Tasks) -->
            <div x-show="activeTab === 'onboarding'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Status</h3>

                <!-- Job & Status Card (at top) -->
                <div class="mb-8 p-6 bg-white rounded-xl border-2 border-primary/20 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center">
                                <i class="bi bi-briefcase text-primary text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-text-light uppercase tracking-wide">Your Position</div>
                                <div class="text-xl font-black text-primary" x-text="candidateJob?.title || candidateProfile?.position || 'Not assigned'"></div>
                                <div class="text-sm text-text-light" x-show="candidateJob?.department" x-text="candidateJob?.department"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold text-text-light uppercase tracking-wide mb-1">Status</div>
                            <span class="inline-block text-sm font-bold px-4 py-2 rounded-xl border-2"
                                  :class="getStatusClass(candidateProfile?.status)"
                                  x-text="candidateProfile?.status || 'N/A'"></span>
                            <div x-show="candidateProfile?.status === 'Probation'" class="mt-2 text-sm text-text-light">
                                <i class="bi bi-clock-history"></i> Probation duration: at least 5 months
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="text-lg font-bold text-primary mb-4">Onboarding Tasks</h4>
                
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="taskSearchQuery" 
                               @input="filterTasks()"
                               placeholder="Search by task name..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>
                
                <!-- Filter by Status -->
                <div class="mb-6">
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-semibold text-primary">Filter by Status:</label>
                        <select x-model="taskStatusFilter" 
                                @change="filterTasks()"
                                class="p-2 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                            <option value="">All Tasks</option>
                            <option value="completed">Accomplished</option>
                            <option value="pending">Not Accomplished</option>
                        </select>
                    </div>
                </div>
                
                <!-- Tasks by Job -->
                <div class="space-y-6" x-show="filteredTasksByJob.length">
                    <template x-for="jobTask in filteredTasksByJob" :key="jobTask.job_id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                            <div>
                                    <h4 class="text-base font-semibold text-primary mb-1" x-text="jobTask.job_title"></h4>
                                    <div class="text-xs text-text-light">
                                        <span x-text="jobTask.completed_count"></span> of <span x-text="jobTask.total_count"></span> tasks completed
                                    </div>
                                </div>
                                <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center">
                                    <i class="bi bi-briefcase text-primary text-xl"></i>
                                </div>
                            </div>
                            
                            <!-- Accomplished Tasks -->
                            <div class="mb-4" x-show="jobTask.accomplished.length">
                                <h5 class="text-sm font-semibold text-green-600 mb-2 flex items-center gap-2">
                                    <i class="bi bi-check-circle"></i>
                                    Accomplished Tasks
                                </h5>
                                <div class="space-y-2">
                                    <template x-for="task in jobTask.accomplished" :key="task.id">
                                        <div class="p-3 bg-green-50 rounded-lg border border-green-200 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i class="bi bi-check-circle text-green-600"></i>
                                                <span class="text-sm text-primary" x-text="task.task_title"></span>
                                            </div>
                                            <span class="text-xs text-green-600" x-text="formatDate(task.completed_at)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Remaining Tasks -->
                            <div x-show="jobTask.remaining.length">
                                <h5 class="text-sm font-semibold text-yellow-600 mb-2 flex items-center gap-2">
                                    <i class="bi bi-clock"></i>
                                    Remaining Requirements
                                </h5>
                                <div class="space-y-2">
                                    <template x-for="task in jobTask.remaining" :key="task.id">
                                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i class="bi bi-clock text-yellow-600"></i>
                                                <span class="text-sm text-primary" x-text="task.task_title"></span>
                                            </div>
                                            <span class="text-xs text-yellow-600">Pending</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="!filteredTasksByJob.length" class="text-center py-12 text-sm text-text-light">
                    <span x-show="taskSearchQuery || taskStatusFilter">No tasks found matching your criteria.</span>
                    <span x-show="!taskSearchQuery && !taskStatusFilter">You have no onboarding tasks assigned yet.</span>
                </div>
            </div>

            <!-- Self Assessment Tab -->
            <div x-show="activeTab === 'performance'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Self Assessment</h3>

                <!-- Assessment completion filter -->
                <div class="mb-6 flex flex-wrap items-center gap-4">
                    <label class="text-sm font-semibold text-primary">Filter:</label>
                    <select x-model="assessmentFilter" 
                            @change="filterAssessments()"
                            class="p-2.5 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                        <option value="all">All Assessments</option>
                        <option value="completed">Completed</option>
                        <option value="not-completed">Not Completed</option>
                    </select>
                </div>

                <!-- Score summary for completed assessments -->
                <div class="mb-8 p-6 bg-white rounded-xl border border-gray-200" x-show="completedAssessmentSummary.length">
                    <h4 class="text-base font-bold text-primary mb-4 flex items-center gap-2">
                        <i class="bi bi-graph-up"></i>
                        Assessment Scores Summary
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="item in completedAssessmentSummary" :key="item.id">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="text-sm font-semibold text-primary mb-1" x-text="item.title"></div>
                                <div class="text-2xl font-black text-primary" x-text="item.scoreDisplay"></div>
                                <div class="text-xs text-text-light mt-1">Completed</div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="filteredQuestionSets.length">
                    <template x-for="questionSet in filteredQuestionSets" :key="questionSet.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="text-base font-semibold text-primary mb-2" x-text="questionSet.title"></div>
                                    <div class="text-xs text-text-light mb-3" x-text="questionSet.description || 'No description'"></div>
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs font-semibold text-text-light">Progress:</span>
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-primary to-accent rounded-full transition-all duration-500" 
                                                 :style="'width: ' + questionSet.progress + '%'"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-primary" x-text="questionSet.progress + '%'"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-text-light">
                                        <span><strong>Questions:</strong> <span x-text="questionSet.questions?.length || 0"></span></span>
                                        <span><strong>Answered:</strong> <span x-text="questionSet.responses?.length || 0"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold px-3 py-1.5 rounded-full" 
                                      :class="questionSet.completed ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200'"
                                      x-text="questionSet.completed ? 'Completed' : 'In Progress'"></span>
                                <button @click="startAssessment(questionSet)" 
                                        class="px-4 py-2 text-xs font-semibold bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors">
                                    <span x-text="questionSet.completed ? 'Review' : 'Start Assessment'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Learning Modules -->
                <div class="mt-8" x-show="myLearningModules.length">
                    <h4 class="text-lg font-semibold text-primary mb-4">Learning Modules</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="module in myLearningModules" :key="module.assignment_id">
                            <div class="p-4 bg-white rounded-xl border border-gray-200 hover:border-primary/30 transition-all">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-sm font-semibold text-primary" x-text="module.name"></div>
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full" 
                                          :class="module.completed ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200'"
                                          x-text="module.completed ? 'Completed' : 'Pending'"></span>
                                </div>
                                <button @click="viewModule(module)" 
                                        class="w-full mt-2 px-3 py-2 text-xs font-semibold bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                                    <span x-text="module.completed ? 'Review Module' : 'View Module'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div x-show="!filteredQuestionSets.length && !myLearningModules.length" class="text-center py-12 text-sm text-text-light">
                    <span x-show="assessmentFilter !== 'all'">No assessments match the selected filter.</span>
                    <span x-show="assessmentFilter === 'all'">No assessments or modules assigned yet.</span>
                </div>
                <div x-show="filteredQuestionSets.length && myLearningModules.length" class="mt-8"></div>
            </div>

            <!-- Culture Tab -->
            <div x-show="activeTab === 'recognition'" class="main-inner !w-full !max-w-none mt-8">
                <h3 class="text-xl font-black text-primary mb-6">Culture & Recognition</h3>
                
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               x-model="recognitionSearchQuery" 
                               @input="filterRecognitions()"
                               placeholder="Search by name, reason, or award type..." 
                               class="w-full p-4 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary pl-12">
                        <i class="bi bi-search absolute left-4 top-1/2 transform -translate-y-1/2 text-text-light"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="filteredRecognitions.length">
                    <template x-for="recognition in filteredRecognitions" :key="recognition.id">
                        <div class="p-5 bg-white rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                    <i class="bi bi-trophy text-yellow-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-base font-semibold text-primary mb-1" x-text="recognition.to"></div>
                                    <div class="text-xs text-text-light mb-2" x-text="recognition.award_type"></div>
                                    <div class="text-sm text-text-light" x-text="recognition.reason"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center gap-4 text-xs text-text-light">
                                    <button @click="congratulateRecognition(recognition.id)" 
                                            class="flex items-center gap-1 hover:text-primary transition-colors">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span x-text="recognition.congratulations || 0"></span>
                                    </button>
                                    <button @click="boostRecognition(recognition.id)" 
                                            class="flex items-center gap-1 hover:text-accent transition-colors">
                                        <i class="bi bi-lightning"></i>
                                        <span x-text="recognition.boosts || 0"></span>
                                    </button>
                                </div>
                                <div class="text-xs text-text-light" x-text="formatDate(recognition.date)"></div>
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
                    <h3 class="text-xl font-black text-primary">Profile</h3>
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
                                        <img x-show="candidateProfile?.profile_picture" 
                                             :src="candidateProfile?.profile_picture" 
                                             :alt="candidateProfile?.name"
                                             class="w-full h-full object-cover">
                                        <i x-show="!candidateProfile?.profile_picture" class="bi bi-person-circle text-6xl text-primary/50"></i>
                                    </div>
                                </div>
                                <div class="flex-1 pt-4">
                                    <h4 class="text-2xl font-black text-primary mb-1" x-text="candidateProfile?.name || 'Candidate'"></h4>
                                    <p class="text-sm text-text-light" x-text="candidateProfile?.email || 'candidate@example.com'"></p>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Name</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="candidateProfile?.name || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Email</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="candidateProfile?.email || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Contact Number</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="candidateProfile?.contact_no || candidateProfile?.phone || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Position</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="candidateProfile?.position || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Status</label>
                                <div class="text-base font-semibold text-primary mt-1" x-text="candidateProfile?.status || 'N/A'"></div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl" x-show="candidateProfile?.status">
                                <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Update Status</label>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <template x-if="candidateProfile?.status === 'Candidate'">
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button"
                                                    @click="updateCandidateStatus('Probation')"
                                                    class="px-3 py-2 text-[11px] bg-yellow-50 text-yellow-800 rounded-lg hover:bg-yellow-100 font-bold">
                                                Move to Probation
                                            </button>
                                            <button type="button"
                                                    @click="updateCandidateStatus('Rejected')"
                                                    class="px-3 py-2 text-[11px] bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-bold">
                                                Reject
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="candidateProfile?.status === 'Probation'">
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button"
                                                    @click="updateCandidateStatus('Regular')"
                                                    class="px-3 py-2 text-[11px] bg-green-50 text-green-700 rounded-lg hover:bg-green-100 font-bold">
                                                Promote to Regular
                                            </button>
                                            <button type="button"
                                                    @click="updateCandidateStatus('Probation')"
                                                    class="px-3 py-2 text-[11px] bg-primary/10 text-primary rounded-lg hover:bg-primary/20 font-bold">
                                                Retain (Keep Probation)
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div class="text-[11px] text-text-light mt-2">
                                    This updates your HR1 status and may affect onboarding/performance access.
                                </div>
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
                                        <img x-show="candidateProfile?.profile_picture" 
                                             :src="candidateProfile?.profile_picture" 
                                             :alt="candidateProfile?.name"
                                             class="w-full h-full object-cover">
                                        <i x-show="!candidateProfile?.profile_picture" class="bi bi-person-circle text-4xl text-primary/50"></i>
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
                                <input type="text" x-model="candidateProfile.name" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary"
                                       required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Email</label>
                                <input type="email" x-model="candidateProfile.email" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary"
                                       required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Contact Number</label>
                                <input type="tel" x-model="candidateProfile.contact_no" 
                                       class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Position</label>
                                <input type="text" x-model="candidateProfile.position" 
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
        role: 'candidate',
        activeTab: 'dashboard',
        sidebarOpen: true,
        modalType: null,
        selectedJob: null,
        selectedApplication: null,
        editingProfile: false,
        applicants: @json($applicants ?? []),
        jobs: @json($jobs ?? []),
        filteredJobs: @json($jobs ?? []),
        jobSearchQuery: '',
        recognitions: @json($recognitions ?? []),
        filteredRecognitions: @json($recognitions ?? []),
        recognitionSearchQuery: '',
        tasks: @json($tasks ?? []),
        myApplications: @json($myApplications ?? []),
        filteredApplications: @json($myApplications ?? []),
        applicationSearchQuery: '',
        applicantTasks: @json($applicantTasks ?? []),
        filteredTasksByJob: [],
        taskSearchQuery: '',
        taskStatusFilter: '',
        myQuestionSets: @json($myQuestionSets ?? []),
        filteredQuestionSets: [],
        assessmentFilter: 'all',
        myLearningModules: @json($myLearningModules ?? []),
        candidateProfile: (() => {
            const profile = @json($candidateProfile ?? []);
            return {
                id: profile.id || null,
                name: profile.name || '',
                email: profile.email || '',
                contact_no: profile.contact_no || profile.phone || '',
                position: profile.position || '',
                status: profile.status || '',
                profile_picture: profile.profile_picture || ''
            };
        })(),
        candidateJob: @json($candidateJob ?? null),
        
        init() {
            this.filterApplications();
            this.filterJobs();
            this.filterRecognitions();
            this.filterTasks();
            this.filterAssessments();
        },
        
        getStatusClass(status) {
            const classes = {
                'Applicant': 'bg-blue-50 text-blue-700 border-blue-200',
                'Candidate': 'bg-purple-50 text-purple-700 border-purple-200',
                'Probation': 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'Regular': 'bg-green-50 text-green-700 border-green-200',
                'Rejected': 'bg-red-50 text-red-700 border-red-200'
            };
            return classes[status] || 'bg-gray-50 text-gray-700 border-gray-200';
        },
        
        getPendingTasksCount() {
            return this.applicantTasks.filter(t => !t.completed).length;
        },
        
        getCompletedTasksCount() {
            return this.applicantTasks.filter(t => t.completed).length;
        },
        
        getAssessmentProgress() {
            if (!this.myQuestionSets.length) return 0;
            const total = this.myQuestionSets.reduce((sum, qs) => sum + (qs.questions?.length || 0), 0);
            const answered = this.myQuestionSets.reduce((sum, qs) => sum + (qs.responses?.length || 0), 0);
            return total > 0 ? Math.round((answered / total) * 100) : 0;
        },
        
        getApplicationProgress(app) {
            // Calculate progress based on status
            const statusProgress = {
                'Applicant': 25,
                'Candidate': 50,
                'Probation': 75,
                'Regular': 100,
                'Rejected': 0
            };
            return statusProgress[app.status] || 0;
        },
        
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        
        formatDateTime(datetime) {
            if (!datetime) return 'N/A';
            return new Date(datetime).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },
        
        getDocumentCount(documents) {
            if (!documents) return 0;
            try {
                const docs = typeof documents === 'string' ? JSON.parse(documents) : documents;
                return Array.isArray(docs) ? docs.length : 0;
            } catch {
                return 0;
            }
        },
        
        getDocumentsList(documents) {
            if (!documents) return [];
            try {
                const docs = typeof documents === 'string' ? JSON.parse(documents) : documents;
                return Array.isArray(docs) ? docs : [];
            } catch {
                return [];
            }
        },
        
        getQuestionOptions(options) {
            if (!options) return [];
            try {
                const opts = typeof options === 'string' ? JSON.parse(options) : options;
                return Array.isArray(opts) ? opts : [];
            } catch {
                return [];
            }
        },
        
        submitAssessment() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/question-sets/${this.selectedQuestionSet.id}/submit`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(res => {
                if (!res.ok) throw new Error('Failed to submit assessment');
                return res.json();
            }).then(data => {
                // Update question set progress
                const index = this.myQuestionSets.findIndex(qs => qs.id == this.selectedQuestionSet.id);
                if (index !== -1) {
                    this.myQuestionSets[index].responses = data.responses;
                    this.myQuestionSets[index].progress = data.progress;
                    this.myQuestionSets[index].completed = data.completed;
                }
                this.modalType = null;
                alert('Assessment submitted successfully!');
            }).catch(err => {
                console.error('Error submitting assessment:', err);
                alert('Failed to submit assessment. Please try again.');
            });
        },
        
        markModuleComplete(assignmentId) {
            fetch(`/api/hr1/modules/complete/${assignmentId}`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (!res.ok) throw new Error('Failed to mark module as complete');
                return res.json();
            }).then(data => {
                const index = this.myLearningModules.findIndex(m => m.assignment_id == assignmentId);
                if (index !== -1) {
                    this.myLearningModules[index].completed = true;
                }
                alert('Module marked as completed!');
            }).catch(err => {
                console.error('Error:', err);
                alert('Failed to update module status.');
            });
        },
        
        hasAppliedToJob(jobId) {
            return this.myApplications.some(app => app.job_posting_id == jobId);
        },
        
        filterApplications() {
            const query = this.applicationSearchQuery.toLowerCase();
            if (!query) {
                this.filteredApplications = [...this.myApplications];
                return;
            }
            this.filteredApplications = this.myApplications.filter(app => 
                (app.job_posting_hr1?.title || '').toLowerCase().includes(query) ||
                (app.job_posting_hr1?.department || '').toLowerCase().includes(query) ||
                (app.status || '').toLowerCase().includes(query)
            );
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
                (job.location || '').toLowerCase().includes(query)
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
        
        filterTasks() {
            const query = this.taskSearchQuery.toLowerCase();
            const statusFilter = this.taskStatusFilter;
            const jobFilter = this.candidateJob?.id || null;
            
            // Group tasks by job (candidate's job is fixed - no selector)
            const tasksByJob = {};
            
            this.applicantTasks.forEach(task => {
                if (jobFilter && task.job_id != jobFilter) return;
                if (query && !(task.job_title || '').toLowerCase().includes(query) && !(task.task_title || '').toLowerCase().includes(query)) return;
                if (statusFilter === 'completed' && !task.completed) return;
                if (statusFilter === 'pending' && task.completed) return;
                
                if (!tasksByJob[task.job_id]) {
                    tasksByJob[task.job_id] = {
                        job_id: task.job_id,
                        job_title: task.job_title,
                        accomplished: [],
                        remaining: [],
                        completed_count: 0,
                        total_count: 0
                    };
                }
                
                if (task.completed) {
                    tasksByJob[task.job_id].accomplished.push(task);
                    tasksByJob[task.job_id].completed_count++;
                } else {
                    tasksByJob[task.job_id].remaining.push(task);
                }
                tasksByJob[task.job_id].total_count++;
            });
            
            this.filteredTasksByJob = Object.values(tasksByJob);
        },
        
        filterAssessments() {
            const filter = this.assessmentFilter;
            if (filter === 'all') {
                this.filteredQuestionSets = [...this.myQuestionSets];
                return;
            }
            if (filter === 'completed') {
                this.filteredQuestionSets = this.myQuestionSets.filter(qs => qs.completed);
                return;
            }
            if (filter === 'not-completed') {
                this.filteredQuestionSets = this.myQuestionSets.filter(qs => !qs.completed);
                return;
            }
            this.filteredQuestionSets = [...this.myQuestionSets];
        },
        
        get completedAssessmentSummary() {
            return this.myQuestionSets
                .filter(qs => qs.completed)
                .map(qs => ({
                    id: qs.id,
                    title: qs.title,
                    score: qs.score,
                    scoreDisplay: (qs.score != null && qs.score !== '' && !isNaN(Number(qs.score))) ? Number(qs.score) : 'N/A'
                }));
        },
        
        viewApplicationDetails(app) {
            this.selectedApplication = app;
            this.modalType = 'view-application';
        },
        
        editApplication(app) {
            this.selectedApplication = { ...app };
            this.modalType = 'edit-application';
        },
        
        cancelApplication(appId) {
            if (confirm('Are you sure you want to cancel this application?')) {
                fetch(`/api/hr1/applications/${appId}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(res => {
                    if (!res.ok) throw new Error('Failed to cancel application');
                    this.myApplications = this.myApplications.filter(app => app.id != appId);
                    this.filterApplications();
                    alert('Application cancelled successfully!');
                }).catch(err => {
                    console.error('Error cancelling application:', err);
                    alert('Failed to cancel application. Please try again.');
                });
            }
        },
        
        submitApplication() {
            const form = event.target;
            const formData = new FormData(form);
            formData.append('job_posting_id', this.selectedJob.id);

            // Enforce resume upload if job requires it (frontend guard)
            if (this.selectedJob?.require_resume) {
                const docs = formData.getAll('documents[]') || [];
                const hasFile = docs.some(d => d instanceof File && d.name);
                if (!hasFile) {
                    alert('Please upload your resume before submitting.');
                    return;
                }
            }
            
            fetch('/api/hr1/applications', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(res => {
                if (!res.ok) throw new Error('Failed to submit application');
                return res.json();
            }).then(data => {
                this.myApplications.push(data);
                this.filterApplications();
                this.modalType = null;
                form.reset();
                alert('Application submitted successfully!');
            }).catch(err => {
                console.error('Error submitting application:', err);
                alert('Failed to submit application. Please try again.');
            });
        },
        
        updateApplication() {
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/api/hr1/applications/${this.selectedApplication.id}`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(res => {
                if (!res.ok) throw new Error('Failed to update application');
                return res.json();
            }).then(data => {
                const index = this.myApplications.findIndex(app => app.id == data.id);
                if (index !== -1) {
                    this.myApplications[index] = data;
                    this.filterApplications();
                }
                this.modalType = null;
                alert('Application updated successfully!');
            }).catch(err => {
                console.error('Error updating application:', err);
                alert('Failed to update application. Please try again.');
            });
        },
        
        congratulateRecognition(id) {
            fetch(`/api/hr1/recognitions/${id}/congratulate`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                const index = this.recognitions.findIndex(r => r.id == id);
                if (index !== -1) {
                    this.recognitions[index] = data;
                    this.filterRecognitions();
                }
            }).catch(err => console.error('Error:', err));
        },
        
        boostRecognition(id) {
            fetch(`/api/hr1/recognitions/${id}/boost`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                const index = this.recognitions.findIndex(r => r.id == id);
                if (index !== -1) {
                    this.recognitions[index] = data;
                    this.filterRecognitions();
                }
            }).catch(err => console.error('Error:', err));
        },
        
        startAssessment(questionSet) {
            this.selectedQuestionSet = questionSet;
            this.modalType = 'take-assessment';
        },
        
        viewModule(module) {
            this.selectedModule = module;
            this.modalType = 'view-module';
        },
        
        handleProfilePictureChange(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.candidateProfile.profile_picture = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        updateProfile() {
            const formData = new FormData();
            formData.append('name', this.candidateProfile.name);
            formData.append('email', this.candidateProfile.email);
            formData.append('contact_no', this.candidateProfile.contact_no);
            formData.append('position', this.candidateProfile.position);
            
            if (this.candidateProfile.profile_picture && this.candidateProfile.profile_picture.startsWith('data:')) {
                formData.append('profile_picture', this.candidateProfile.profile_picture);
            }
            
            fetch(`/api/hr1/candidate/profile`, {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }).then(res => {
                if (!res.ok) throw new Error('Failed to update profile');
                return res.json();
            }).then(data => {
                this.candidateProfile = { ...this.candidateProfile, ...data };
                this.editingProfile = false;
                alert('Profile updated successfully');
            }).catch(err => {
                console.error('Error updating profile:', err);
                alert('Failed to update profile. Please try again.');
            });
        },

        updateCandidateStatus(status) {
            const desired = (status || '').toString();
            if (!desired) return;

            fetch('/api/hr1/candidate/status', {
                method: 'PATCH',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: desired })
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || data.error || 'Failed to update status');
                return data;
            }).then(data => {
                this.candidateProfile.status = data.status || desired;
                alert('Status updated successfully.');
            }).catch(err => {
                console.error('Error updating status:', err);
                alert(err.message || 'Failed to update status. Please try again.');
            });
        },
        
        get navItems() {
            return [
                { id: 'dashboard', label: 'Dashboard', icon: 'layout-dashboard' },
                { id: 'onboarding', label: 'Status', icon: 'check-square' },
                { id: 'performance', label: 'Self Assessment', icon: 'target' },
                { id: 'recognition', label: 'Culture', icon: 'star' },
                { id: 'profile', label: 'Profile', icon: 'user-circle' }
            ];
        },
        
        openApplyModal(job) {
            if (this.hasAppliedToJob(job.id)) {
                alert('You have already applied to this job.');
                return;
            }
            this.selectedJob = job;
            this.modalType = 'apply';
        }
    }
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    
    // Initialize dashboard filters
    const dashboardEl = document.querySelector('[x-data="dashboard()"]');
    if (dashboardEl && dashboardEl._x_dataStack) {
        const dashboard = dashboardEl._x_dataStack[0];
        if (dashboard.init) {
            dashboard.init();
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>
@endpush

