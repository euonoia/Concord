<!-- View Applicant Profile Modal -->
<div x-show="modalType === 'view-profile'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Applicant Profile</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="selectedApplicant">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">User ID</label>
                    <div class="text-base font-mono font-semibold text-primary mt-1" x-text="selectedApplicant?.employee_id || selectedApplicant?.id || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Name</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.name || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Email</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.email || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Password</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.password ? '••••••••' : 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Position</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.position || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Status</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.status || 'N/A'"></div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <label class="text-xs font-semibold text-text-light uppercase tracking-wide">Contact Number</label>
                    <div class="text-base font-semibold text-primary mt-1" x-text="selectedApplicant?.contact_no || 'N/A'"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Applicant Modal -->
<div x-show="modalType === 'edit-applicant'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Applicant</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="updateApplicantInfo" class="space-y-6" x-show="selectedApplicant">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Name</label>
                        <input type="text" x-model="selectedApplicant.name" 
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Email</label>
                        <input type="email" x-model="selectedApplicant.email" 
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Password</label>
                        <input type="password" x-model="selectedApplicant.password" 
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Contact Number</label>
                        <input type="tel" x-model="selectedApplicant.contact_no" 
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Position</label>
                    <select x-model="selectedApplicant.position"
                            class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="">Select Position</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.title" x-text="job.title"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Status</label>
                    <select name="status"
                            x-model="selectedApplicant.status" 
                            class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="Applicant">Applicant</option>
                        <option value="Candidate">Candidate</option>
                        <option value="Probation">Probation</option>
                        <option value="Regular">Regular</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Applicant</button>
            </form>
        </div>
    </div>
</div>

<!-- Add Applicant/User Modal -->
<div x-show="modalType === 'add-applicant'"
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-3xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Add User</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="addApplicant" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Name</label>
                        <input type="text" name="name" required
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Email</label>
                        <input type="email" name="email" required
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Password</label>
                        <input type="password" name="password" required
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Contact Number</label>
                        <input type="tel" name="contact_no"
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Position / Job</label>
                    <select name="position"
                            class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required>
                        <option value="">Select Position</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.title" x-text="job.title"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Link to Job (Optional)</label>
                    <select name="job_posting_id"
                            class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="">No specific job</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.id" x-text="job.title"></option>
                        </template>
                    </select>
                    <span class="text-[10px] text-text-light">Link applicant to a job for recruitment visibility</span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Status</label>
                    <select name="status"
                            class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="Applicant">Applicant</option>
                        <option value="Candidate">Candidate</option>
                        <option value="Probation">Probation</option>
                        <option value="Regular">Regular</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">
                    Save User
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Add Task Set Modal -->
<div x-show="modalType === 'add-task-set'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Create Task Set</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="createTaskSet" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Task Set Name</label>
                    <input type="text" name="name" required placeholder="e.g., Medical License Requirements" 
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Tasks</label>
                    <div class="space-y-3" x-data="{ newTasks: [] }" x-init="newTasks = []">
                        <template x-for="(task, index) in newTasks" :key="index">
                            <div class="flex gap-2">
                                <input type="text" :name="'tasks[' + index + ']'" 
                                       x-model="task.title"
                                       placeholder="Task title" 
                                       class="flex-1 p-3 bg-bg rounded-xl outline-none font-bold text-sm">
                                <button type="button" @click="newTasks.splice(index, 1)" 
                                        class="p-3 text-red-600 hover:bg-red-50 rounded-xl">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="newTasks.push({title: ''})" 
                                class="w-full p-3 border-2 border-dashed border-gray-300 rounded-xl text-text-light hover:border-primary transition-colors">
                            <i class="bi bi-plus-circle"></i> Add Task
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Create Task Set</button>
            </form>
        </div>
    </div>
</div>

<!-- Create Form/Question Builder Modal -->
<div x-show="modalType === 'create-form'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Create Assessment Form</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="createForm" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Form Title</label>
                    <input type="text" name="title" required placeholder="e.g., Technical Skills Assessment" 
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job (Optional)</label>
                    <select name="job_id" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="">General Assessment</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.id" x-text="job.title"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Questions</label>
                    <div class="space-y-4" x-data="{ newQuestions: [] }" x-init="newQuestions = []">
                        <template x-for="(question, index) in newQuestions" :key="index">
                            <div class="p-4 bg-gray-50 rounded-xl space-y-3">
                                <div class="flex gap-2">
                                    <input type="text" :name="'questions[' + index + '][question_text]'" 
                                           x-model="question.text"
                                           placeholder="Question text" 
                                           class="flex-1 p-3 bg-bg rounded-xl outline-none font-bold text-sm"
                                           required>
                                    <select :name="'questions[' + index + '][question_type]'" x-model="question.type" 
                                            @change="question.type = $event.target.value"
                                            class="p-3 bg-bg rounded-xl outline-none font-bold text-sm">
                                        <option value="text">Text</option>
                                        <option value="multiple-choice">Multiple Choice (Radio)</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                    <button type="button" @click="newQuestions.splice(index, 1)" 
                                            class="p-3 text-red-600 hover:bg-red-50 rounded-xl">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <!-- Options for multiple choice questions -->
                                <div x-show="question.type === 'multiple-choice'" class="space-y-2">
                                    <div class="text-xs font-semibold text-text-light">Options (Radio Buttons):</div>
                                    <div class="space-y-2" x-data="{ options: question.options || [''] }" x-init="question.options = question.options || ['']">
                                        <template x-for="(option, optIndex) in options" :key="optIndex">
                                            <div class="flex gap-2">
                                                <input type="text" 
                                                       x-model="options[optIndex]"
                                                       :name="'questions[' + index + '][options][' + optIndex + ']'"
                                                       placeholder="Option text" 
                                                       class="flex-1 p-2 bg-bg rounded-lg outline-none font-bold text-sm">
                                                <button type="button" @click="options.splice(optIndex, 1); question.options = options" 
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="options.push(''); question.options = options" 
                                                class="w-full p-2 border border-dashed border-gray-300 rounded-lg text-text-light hover:border-primary transition-colors text-xs">
                                            <i class="bi bi-plus-circle"></i> Add Option
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button" @click="newQuestions.push({text: '', type: 'text', options: []})" 
                                class="w-full p-3 border-2 border-dashed border-gray-300 rounded-xl text-text-light hover:border-primary transition-colors">
                            <i class="bi bi-plus-circle"></i> Add Question
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Create Form</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Form Modal -->
<div x-show="modalType === 'edit-form'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Assessment Form</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto"
             x-show="editingForm"
             x-data="{ editQuestions: [] }"
             x-init="
                const hydrate = (val) => {
                    editQuestions = (val?.questions || []).map(q => ({
                        id: q.id || null,
                        text: q.question_text || q.text || '',
                        type: q.question_type || 'text',
                        options: (() => {
                            if (!q.options) return [];
                            if (Array.isArray(q.options)) return q.options;
                            try { return JSON.parse(q.options) || []; } catch (e) { return []; }
                        })()
                    }));
                };
                hydrate(editingForm);
                $watch('editingForm', (val) => hydrate(val));
             ">
            <form @submit.prevent="updateForm" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Form Title</label>
                    <input type="text" name="title" x-model="editingForm.title" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job (Optional)</label>
                    <select name="job_id" x-model="editingForm.job_id" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="">General Assessment</option>
                        <template x-for="job in jobs" :key="job.id">
                            <option :value="job.id" x-text="job.title"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Description</label>
                    <textarea name="description" x-model="editingForm.description" rows="3"
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Questions</label>
                    <div class="space-y-4">
                        <template x-for="(question, index) in editQuestions" :key="question.id || index">
                            <div class="p-4 bg-gray-50 rounded-xl space-y-3">
                                <input type="hidden" :name="'questions[' + index + '][id]'" x-model="question.id">
                                <div class="flex gap-2">
                                    <input type="text"
                                           :name="'questions[' + index + '][question_text]'"
                                           x-model="question.text"
                                           placeholder="Question text"
                                           class="flex-1 p-3 bg-bg rounded-xl outline-none font-bold text-sm"
                                           required>
                                    <select :name="'questions[' + index + '][question_type]'"
                                            x-model="question.type"
                                            @change="question.type = $event.target.value"
                                            class="p-3 bg-bg rounded-xl outline-none font-bold text-sm">
                                        <option value="text">Text</option>
                                        <option value="multiple-choice">Multiple Choice (Radio)</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                    <button type="button" @click="editQuestions.splice(index, 1)"
                                            class="p-3 text-red-600 hover:bg-red-50 rounded-xl">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div x-show="question.type === 'multiple-choice'" class="space-y-2">
                                    <div class="text-xs font-semibold text-text-light">Options (Radio Buttons):</div>
                                    <div class="space-y-2"
                                         x-data="{ options: question.options || [''] }"
                                         x-init="question.options = question.options && question.options.length ? question.options : ['']">
                                        <template x-for="(option, optIndex) in options" :key="optIndex">
                                            <div class="flex gap-2">
                                                <input type="text"
                                                       x-model="options[optIndex]"
                                                       :name="'questions[' + index + '][options][' + optIndex + ']'"
                                                       placeholder="Option text"
                                                       class="flex-1 p-2 bg-bg rounded-lg outline-none font-bold text-sm">
                                                <button type="button"
                                                        @click="options.splice(optIndex, 1); question.options = options"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button"
                                                @click="options.push(''); question.options = options"
                                                class="w-full p-2 border border-dashed border-gray-300 rounded-lg text-text-light hover:border-primary transition-colors text-xs">
                                            <i class="bi bi-plus-circle"></i> Add Option
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <button type="button"
                                @click="editQuestions.push({id: null, text: '', type: 'text', options: []})"
                                class="w-full p-3 border-2 border-dashed border-gray-300 rounded-xl text-text-light hover:border-primary transition-colors">
                            <i class="bi bi-plus-circle"></i> Add Question
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Form</button>
            </form>
        </div>
    </div>
</div>

<!-- Add Recognition Modal -->
<div x-show="modalType === 'add-recognition'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Nominate Outstanding Candidate</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="createRecognition" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Candidate Name</label>
                    <select name="to" required class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="">Select Candidate</option>
                        <template x-for="applicant in applicants" :key="applicant.id">
                            <option :value="applicant.name" x-text="applicant.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Award Type</label>
                    <input type="text" name="award_type" required placeholder="e.g., Employee of the Month" 
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Reason/Description</label>
                    <textarea name="reason" rows="4" required placeholder="Why is this candidate outstanding?" 
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_most_outstanding" value="1" 
                               class="w-4 h-4 text-primary rounded">
                        <span class="text-sm font-semibold text-primary">Mark as Most Outstanding</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Post Recognition</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Recognition Modal -->
<div x-show="modalType === 'edit-recognition'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Recognition</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="editingRecognition">
            <form @submit.prevent="updateRecognition" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Candidate Name</label>
                    <input type="text" x-model="editingRecognition.to" 
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Award Type</label>
                    <input type="text" x-model="editingRecognition.award_type" 
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Reason/Description</label>
                    <textarea x-model="editingRecognition.reason" rows="4" 
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="editingRecognition.is_most_outstanding" 
                               class="w-4 h-4 text-primary rounded">
                        <span class="text-sm font-semibold text-primary">Mark as Most Outstanding</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Recognition</button>
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
            <h3 class="text-2xl font-black text-primary tracking-tight">Create Job Posting</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="createJob" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job Title</label>
                    <input type="text" name="title" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                           placeholder="e.g., Registered Nurse - Emergency Department">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Department</label>
                        <input type="text" name="department" required
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                               placeholder="e.g., Emergency">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Location</label>
                        <input type="text" name="location" required
                               class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                               placeholder="e.g., Main Hospital">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job Type</label>
                        <select name="type" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Status</label>
                        <select name="status" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                            <option value="Open">Open</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Description</label>
                    <textarea name="description" rows="6"
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                              placeholder="Responsibilities, qualifications, shift details, etc."></textarea>
                </div>

                <div class="p-4 bg-gray-50 rounded-2xl space-y-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="require_resume" value="1" checked class="w-4 h-4 text-primary rounded">
                        <span class="text-sm font-semibold text-primary">Require applicants to upload resume</span>
                    </label>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Attach a file (optional)</label>
                        <input type="file" name="job_attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary text-sm">
                        <div class="text-xs text-text-light mt-1">You can attach a PDF/DOC (job details, requirements, etc.).</div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Post Job</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Job Modal -->
<div x-show="modalType === 'edit-job'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Job Posting</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="selectedJob">
            <form @submit.prevent="updateJob" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job Title</label>
                    <input type="text" name="title" x-model="selectedJob.title" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Department</label>
                    <input type="text" name="department" x-model="selectedJob.department" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Location</label>
                    <input type="text" name="location" x-model="selectedJob.location"
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Job Type</label>
                    <select name="type" x-model="selectedJob.type" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Contract">Contract</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Description</label>
                    <textarea name="description" x-model="selectedJob.description" rows="6"
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl space-y-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="require_resume" value="1"
                               :checked="selectedJob?.require_resume ? true : false"
                               class="w-4 h-4 text-primary rounded">
                        <span class="text-sm font-semibold text-primary">Require applicants to upload resume</span>
                    </label>
                    <div>
                        <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Replace attachment (optional)</label>
                        <input type="file" name="job_attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full p-3 bg-bg rounded-xl border border-gray-200 outline-none focus:border-primary text-sm">
                        <div class="text-xs text-text-light mt-1">Leave empty to keep existing attachment.</div>
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Job</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div x-show="modalType === 'edit-task'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Task/Requirement</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="editingTask">
            <form @submit.prevent="updateTask" class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Task Title</label>
                    <input type="text" x-model="editingTask.title" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Description</label>
                    <textarea x-model="editingTask.description" rows="4"
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Due Date / Deadline</label>
                    <input type="date" x-model="editingTask.due_date"
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                           name="due_date">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" x-model="editingTask.completed"
                               class="w-4 h-4 text-primary rounded">
                        <span class="text-sm font-semibold text-primary">Mark as Completed</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Update Task</button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Candidate Tasks Modal -->
<div x-show="modalType === 'edit-candidate-tasks'" 
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Edit Candidate Tasks</h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="editingCandidateTasks">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="text-sm font-semibold text-primary" x-text="editingCandidateTasks.user?.name || editingCandidateTasks.name || 'Unknown'"></div>
                    <div class="text-xs text-text-light" x-text="editingCandidateTasks.user?.email || editingCandidateTasks.email || 'N/A'"></div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Tasks & Requirements</div>
                    <div class="space-y-2">
                        <template x-for="task in getCandidateTasks(editingCandidateTasks.user_id || editingCandidateTasks.id, editingCandidateTasks.jobId)" :key="task.id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center gap-2 flex-1">
                                    <i :class="task.completed ? 'bi bi-check-circle text-green-600' : 'bi bi-clock text-yellow-600'"></i>
                                    <span class="text-sm" :class="task.completed ? 'text-text-light line-through' : 'text-primary font-medium'" x-text="task.title || task.task_title"></span>
                                    <span x-show="task.due_date" class="text-[10px] text-text-light ml-2" x-text="'Due: ' + (task.due_date || '')"></span>
                                </div>
                                <button @click="editTask(task)" class="p-1.5 text-accent hover:bg-accent/10 rounded" title="Edit task">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <button @click="modalType = 'add-task-to-candidate'" class="w-full p-3 border-2 border-dashed border-gray-300 rounded-xl text-text-light hover:border-primary transition-colors">
                    <i class="bi bi-plus-circle"></i> Add New Task
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Task to Candidate Modal -->
<div x-show="modalType === 'add-task-to-candidate'"
     x-cloak
     x-transition
     class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = 'edit-candidate-tasks'"></div>
    <div class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight">Add Task for Candidate</h3>
            <button @click="modalType = 'edit-candidate-tasks'" class="p-2 hover:bg-bg rounded-full transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto" x-show="editingCandidateTasks">
            <form @submit.prevent="addTaskToCandidate" class="space-y-6">
                <div class="p-4 bg-gray-50 rounded-xl mb-4">
                    <div class="text-sm font-semibold text-primary" x-text="editingCandidateTasks.user?.name || editingCandidateTasks.name || 'Unknown'"></div>
                    <div class="text-xs text-text-light" x-text="editingCandidateTasks.user?.email || editingCandidateTasks.email || 'N/A'"></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Task Title</label>
                    <input type="text" name="title" required
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                           placeholder="e.g., Submit government ID, Upload medical clearance">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Description (Optional)</label>
                    <textarea name="description" rows="4"
                              class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                              placeholder="Add any notes or instructions for this requirement"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-light uppercase tracking-wide mb-2">Due Date / Deadline (Optional)</label>
                    <input type="date" name="due_date"
                           class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm"
                           placeholder="Set deadline for this task">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">
                    Save Task
                </button>
            </form>
        </div>
    </div>
</div>

