<!-- Apply Modal -->
<div x-show="modalType === 'apply'" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-md" @click="modalType = null"></div>
    <div class="relative bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-black text-primary tracking-tight" x-text="'Job Application: ' + (selectedJob?.title || '')"></h3>
            <button @click="modalType = null" class="p-2 hover:bg-bg rounded-full transition-colors"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>
        <div class="p-8 max-h-[85vh] overflow-y-auto">
            <form @submit.prevent="submitApplication" class="space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-accent">Full Name</label>
                        <input class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-accent">Email</label>
                        <input type="email" class="w-full p-4 bg-bg rounded-2xl outline-none font-bold text-sm" required />
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-accent">Upload Documents (CV, License, IDs)</label>
                    <div class="p-12 border-4 border-dashed border-gray-100 rounded-[2.5rem] flex flex-col items-center justify-center text-text-light hover:border-accent hover:bg-bg transition-all cursor-pointer">
                        <i data-lucide="upload" class="w-12 h-12 mb-4 text-accent"></i>
                        <p class="font-bold text-sm">Select files or drag and drop</p>
                        <input type="file" name="documents[]" multiple class="hidden" id="documents">
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-6 rounded-2xl font-black text-xs uppercase shadow-2xl">Confirm Application</button>
            </form>
        </div>
    </div>
</div>

<!-- Add Applicant Modal -->
<div x-show="modalType === 'add-applicant'" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
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
                <input name="position" required placeholder="Initial Position" class="w-full p-4 bg-bg rounded-2xl font-bold text-sm outline-none" />
                <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-xs uppercase shadow-xl">Add Candidate</button>
            </form>
        </div>
    </div>
</div>

<!-- Create Job Modal -->
<div x-show="modalType === 'create-job'" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
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

