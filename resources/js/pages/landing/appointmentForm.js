/**
 * Appointment Form Alpine Component
 */
export default function registerAppointmentFormComponent() {
    const register = () => {
        Alpine.data('appointmentForm', (config) => ({
            // --- UI State ---
            open: config.open || false,
            detailsModal: false, // Fixes ReferenceError: detailsModal
            showDetails: config.showDetails || false,
            showCancelConfirm: false,
            submitted: false,
            agreedToTerms: false,

            // --- Form State ---
            showDoctor: config.showDoctor || false,
            doctors: [],
            loadingDoctors: false,
            selectedDoctor: config.selectedDoctor || '',
            selectedSpecialization: config.selectedSpecialization || '',
            slots: [],
            loadingSlots: false,
            slotsMsg: '',

            // --- Tracking State ---
            trackingReference: '', 
            trackedAppointment: config.trackedAppointment || null,
            // We use this for the details modal to fix the "selectedAppointment is not defined" error
            selectedAppointment: config.trackedAppointment || null, 
            trackingLoading: false,
            trackingError: '',
            cancelLoading: false,
            cancelError: '',
            cancelSuccess: config.cancelSuccess || '',
            cancellationReason: '',

            // --- Endpoints ---
            lookupUrl: config.lookupUrl || '',
            cancelUrlFormat: config.cancelUrlFormat || '',
            doctorsUrl: config.doctorsUrl || '',
            checkAvailabilityUrl: config.checkAvailabilityUrl || '',
            csrfToken: config.csrfToken || '',

            init() {
                // Pre-load doctors if service type exists
                const serviceTypeField = document.getElementById('service_type');
                if (serviceTypeField && serviceTypeField.value) {
                    this.fetchDoctors(serviceTypeField.value);
                }
            },

            async trackAppointment() {
                if (!this.trackingReference) return;
                this.trackingLoading = true;
                this.trackingError = '';
                
                try {
                    const response = await fetch(this.lookupUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ appointment_reference: this.trackingReference })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.trackedAppointment = data.appointment;
                        this.selectedAppointment = data.appointment;
                        this.detailsModal = true;
                    } else {
                        this.trackingError = data.message || 'Appointment not found.';
                    }
                } catch (e) {
                    this.trackingError = 'Connection error.';
                } finally {
                    this.trackingLoading = false;
                }
            },

            fetchDoctors(serviceType) {
                if (!serviceType) {
                    this.showDoctor = false;
                    return;
                }
                this.loadingDoctors = true;
                this.showDoctor = true;
                fetch(`${this.doctorsUrl}?service_type=${serviceType}`)
                    .then(res => res.json())
                    .then(data => { this.doctors = data.doctors || []; })
                    .finally(() => { this.loadingDoctors = false; });
            },

            updateSpecialization(event) {
                const opt = event.target.selectedOptions[0];
                this.selectedSpecialization = opt ? opt.dataset.specialization : '';
                this.fetchSlots();
            },

            async fetchSlots() {
                const dateInput = document.getElementById('appointment_date');
                if (!this.selectedDoctor || !dateInput?.value) return;
                this.loadingSlots = true;
                try {
                    const res = await fetch(`${this.checkAvailabilityUrl}?doctor_id=${this.selectedDoctor}&date=${dateInput.value}`);
                    const data = await res.json();
                    this.slots = data.slots || [];
                    this.slotsMsg = this.slots.length === 0 ? 'No slots available' : '';
                } finally {
                    this.loadingSlots = false;
                }
            }
        }));
    };

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', register);
    }
}

