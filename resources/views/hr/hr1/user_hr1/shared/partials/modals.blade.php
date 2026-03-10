<!-- Apply Modal -->
<div x-show="modalType === 'apply'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight" x-text="'Job Application: ' + (selectedJob?.title || '')"></h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="submitApplication" class="space-y-8">
                <div class="p-4 bg-gray-50 rounded-xl mb-4">
                    <div class="text-sm font-semibold text-primary mb-2" x-text="selectedJob?.title"></div>
                    <div class="text-xs text-text-light" x-text="selectedJob?.department"></div>
                    <div class="text-xs text-text-light mt-1" x-text="selectedJob?.description"></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-accent">
                        Upload Documents (Resume/CV, License, IDs, Certificates)
                        <span class="text-text-light font-semibold normal-case tracking-normal" x-show="selectedJob?.require_resume">— resume required</span>
                    </label>
                    <div class="p-12 border-4 border-dashed border-gray-100 rounded-[2.5rem] flex flex-col items-center justify-center text-text-light hover:border-accent hover:bg-bg transition-all cursor-pointer"
                         @click="document.getElementById('documents').click()">
                        <i data-lucide="upload" class="w-12 h-12 mb-4 text-accent"></i>
                        <p class="font-bold text-sm">Select files or drag and drop</p>
                        <p class="text-xs mt-1">PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</p>
                        <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden" id="documents" :required="selectedJob?.require_resume ? true : false">
                    </div>
                    <div id="file-list" class="mt-2 space-y-1"></div>
                </div>
                <input type="hidden" name="job_posting_id" :value="selectedJob?.id">
                <button type="submit" class="w-full bg-primary text-white py-6 rounded-2xl font-black text-xs uppercase shadow-2xl">Confirm Application</button>
            </form>
        </div>
    </div>
</div>

<!-- View Application Details Modal -->
<div x-show="modalType === 'view-application'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Application Details</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="selectedApplication">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Job Title</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplication?.job_posting_hr1?.title || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Department</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplication?.job_posting_hr1?.department || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Status</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplication?.status || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Applied Date</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="formatDate(selectedApplication?.applied_date)"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl" x-show="selectedApplication?.interview_date">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Interview Date & Time</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="formatDateTime(selectedApplication?.interview_date)"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl" x-show="selectedApplication?.interview_location">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Interview Location</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplication?.interview_location"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl" x-show="selectedApplication?.documents">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Submitted Documents</label>
                    <div class="mt-2 space-y-1">
                        <template x-for="(doc, index) in getDocumentsList(selectedApplication?.documents)" :key="index">
                            <div class="flex items-center gap-2 text-sm text-primary">
                                <i class="bi bi-paperclip"></i>
                                <span x-text="doc"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Application Modal -->
<div x-show="modalType === 'edit-application'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Application</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="updateApplication" class="space-y-6" x-show="selectedApplication">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="text-sm font-semibold text-primary mb-1" x-text="selectedApplication?.job_posting_hr1?.title"></div>
                    <div class="text-xs text-text-light" x-text="selectedApplication?.job_posting_hr1?.department"></div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-accent">Update Documents</label>
                    <div class="p-12 border-4 border-dashed border-gray-100 rounded-[2.5rem] flex flex-col items-center justify-center text-text-light hover:border-accent hover:bg-bg transition-all cursor-pointer"
                         @click="document.getElementById('edit-documents').click()">
                        <i data-lucide="upload" class="w-12 h-12 mb-4 text-accent"></i>
                        <p class="font-bold text-sm">Select files or drag and drop</p>
                        <p class="text-xs mt-1">PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</p>
                        <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="hidden" id="edit-documents">
                    </div>
                    <div class="mt-2 text-xs text-text-light">
                        <p>Current documents:</p>
                        <template x-for="(doc, index) in getDocumentsList(selectedApplication?.documents)" :key="index">
                            <div class="flex items-center gap-2 mt-1">
                                <i class="bi bi-paperclip"></i>
                                <span x-text="doc"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Application</button>
            </form>
        </div>
    </div>
</div>

<!-- Take Assessment Modal -->
<div x-show="modalType === 'take-assessment'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight" x-text="selectedQuestionSet?.title || 'Assessment'"></h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="submitAssessment" class="space-y-6" x-show="selectedQuestionSet">
                <template x-for="(question, index) in selectedQuestionSet?.questions || []" :key="question.id">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <label class="text-sm font-semibold text-primary mb-2 block">
                            <span x-text="(index + 1) + '. '"></span>
                            <span x-text="question.question_text"></span>
                            <span x-show="question.is_required" class="text-red-500">*</span>
                        </label>
                        <div x-show="question.question_type === 'text'">
                            <textarea :name="'question_' + question.id" 
                                      :required="question.is_required"
                                      class="w-full p-3 bg-white rounded-xl border border-gray-200 outline-none focus:border-primary"
                                      rows="3"></textarea>
                        </div>
                        <div x-show="question.question_type === 'multiple-choice'">
                            <template x-for="option in getQuestionOptions(question.options)" :key="option">
                                <label class="flex items-center gap-2 p-2 hover:bg-white rounded cursor-pointer">
                                    <input type="radio" :name="'question_' + question.id" :value="option" :required="question.is_required">
                                    <span class="text-sm" x-text="option"></span>
                                </label>
                            </template>
                        </div>
                        <div x-show="question.question_type === 'rating'">
                            <div class="flex items-center gap-2">
                                <template x-for="i in 5" :key="i">
                                    <label class="cursor-pointer">
                                        <input type="radio" :name="'question_' + question.id" :value="i" :required="question.is_required" class="hidden">
                                        <i class="bi bi-star text-2xl" :class="'text-gray-300 hover:text-yellow-400'"></i>
                                    </label>
                                </template>
                            </div>
                        </div>
                        <div x-show="question.question_type === 'yes-no'">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" :name="'question_' + question.id" value="Yes" :required="question.is_required">
                                    <span>Yes</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" :name="'question_' + question.id" value="No" :required="question.is_required">
                                    <span>No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Submit Assessment</button>
            </form>
        </div>
    </div>
</div>

<!-- View Module Modal -->
<div x-show="modalType === 'view-module'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight" x-text="selectedModule?.name || 'Learning Module'"></h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <div class="space-y-4" x-show="selectedModule">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="text-sm font-semibold text-primary mb-2">Module Information</div>
                    <div class="text-xs text-text-light">This module content will be displayed here.</div>
                </div>
                <button @click="markModuleComplete(selectedModule.assignment_id)" 
                        x-show="!selectedModule.completed"
                        class="w-full bg-primary text-white py-3 rounded-xl font-semibold hover:bg-primary-hover transition-colors">
                    Mark as Completed
                </button>
                <div x-show="selectedModule.completed" class="text-center py-4 text-green-600 font-semibold">
                    <i class="bi bi-check-circle text-2xl"></i>
                    <p class="mt-2">Module Completed</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Applicant Modal -->
<div x-show="modalType === 'add-applicant'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Register Manual Candidate</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="addApplicant" class="space-y-6">
                <input name="name" required placeholder="Full Name" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <input name="email" required type="email" placeholder="Email Address" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <input name="password" required type="password" placeholder="System Password" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">
                        Initial Position (Job)
                    </label>
                    <select name="position" required
                            class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none">
                        <option value="">Select Job</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.title" x-text="job.title"></option>
                        </template>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Add Candidate</button>
            </form>
        </div>
    </div>
</div>

<!-- Create Job Modal -->
<div x-show="modalType === 'create-job'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Publish New Vacancy</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="createJob" class="space-y-6">
                <input name="title" required placeholder="Role Title" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <input name="department" required placeholder="Department" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <select name="type" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none">
                    <option>Full-time</option>
                    <option>Part-time</option>
                    <option>Contract</option>
                </select>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Post to Board</button>
            </form>
        </div>
    </div>
</div>

