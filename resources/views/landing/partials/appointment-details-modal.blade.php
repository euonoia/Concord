<!-- Appointment Details Modal -->
<div x-show="detailsModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div x-show="detailsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="detailsModal = false"></div>

    <!-- Panel -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="detailsModal" x-trap.noscroll="detailsModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

            <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold leading-6 text-gray-900" id="modal-title">Appointment Details</h3>
                    <p class="mt-2 text-sm text-gray-500">Review your appointment information below.</p>
                </div>

                <div class="space-y-6">
                    <!-- Appointment Status -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Status</h4>
                                <p class="text-sm text-gray-600" x-text="selectedAppointment?.status || 'Loading...'"></p>
                            </div>
                            <div class="flex items-center" x-show="selectedAppointment?.status">
                                <span x-show="selectedAppointment.status === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="bi bi-clock mr-1"></i> Pending
                                </span>
                                <span x-show="selectedAppointment.status === 'approved'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-check-circle mr-1"></i> Approved
                                </span>
                                <span x-show="selectedAppointment.status === 'rejected'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="bi bi-x-circle mr-1"></i> Rejected
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Information -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Patient Information</h4>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium">Name:</span> <span x-text="selectedAppointment?.patient_name || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Date of Birth:</span> <span x-text="selectedAppointment?.date_of_birth || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Phone:</span> <span x-text="selectedAppointment?.phone || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Email:</span> <span x-text="selectedAppointment?.email || 'Loading...'"></span></p>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Appointment Details</h4>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium">Service:</span> <span x-text="selectedAppointment?.service_type || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Date:</span> <span x-text="selectedAppointment?.appointment_date || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Time:</span> <span x-text="selectedAppointment?.appointment_time || 'Loading...'"></span></p>
                                <p class="text-sm"><span class="font-medium">Doctor:</span> <span x-text="selectedAppointment?.doctor_name || 'Not assigned'"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Reason for Visit -->
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Reason for Visit</h4>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3" x-text="selectedAppointment?.reason_for_visit || 'Loading...'"></p>
                    </div>

                    <!-- Medical History -->
                    <div x-show="selectedAppointment?.medical_history_summary">
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Medical History</h4>
                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3" x-text="selectedAppointment?.medical_history_summary || 'None provided'"></p>
                    </div>

                    <!-- Insurance Information -->
                    <div x-show="selectedAppointment?.insurance_provider">
                        <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Insurance Information</h4>
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-medium">Provider:</span> <span x-text="selectedAppointment?.insurance_provider || 'None'"></span></p>
                            <p class="text-sm"><span class="font-medium">Policy Number:</span> <span x-text="selectedAppointment?.policy_number || 'None'"></span></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                        <button @click="detailsModal = false" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Close
                        </button>
                        <button x-show="selectedAppointment?.status === 'pending'" @click="cancelAppointment(selectedAppointment.id)" class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            Cancel Appointment
                        </button>
                        <button x-show="selectedAppointment?.status === 'approved'" @click="rescheduleAppointment(selectedAppointment.id)" class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Reschedule
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 rounded-b-2xl">
                <p class="text-xs text-center text-gray-500 flex items-center justify-center gap-1">
                    <i class="bi bi-info-circle"></i> Need help? Contact our support team.
                </p>
            </div>
        </div>
    </div>
</div>