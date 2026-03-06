document.addEventListener('alpine:init', () => {
    Alpine.data('appointmentForm', (config) => ({
        open: config.open ?? false,
        submitted: false,
        showDoctor: config.showDoctor ?? false,
        doctors: [],
        loadingDoctors: false,
        selectedDoctor: config.selectedDoctor ?? '',
        selectedSpecialization: config.selectedSpecialization ?? '',
        showDetails: config.showDetails ?? false,
        showCancelConfirm: false,
        agreedToTerms: false,

        // AJAX Tracking State
        trackingReference: '',
        trackedAppointment: config.trackedAppointment ?? null,
        trackingLoading: false,
        trackingError: '',
        cancelLoading: false,
        cancelError: '',
        cancelSuccess: config.cancelSuccess ?? '',

        // Endpoints & Tokens passed from Blade
        lookupUrl: config.lookupUrl ?? '',
        cancelUrlFormat: config.cancelUrlFormat ?? '',
        doctorsUrl: config.doctorsUrl ?? '',
        csrfToken: config.csrfToken ?? '',

        async trackAppointment() {
            if (!this.trackingReference) return;
            this.trackingLoading = true;
            this.trackingError = '';
            this.cancelSuccess = '';

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
                    this.showDetails = true;
                } else {
                    this.trackingError = data.message || data.error || 'Appointment not found.';
                    this.trackedAppointment = null;
                }
            } catch (error) {
                this.trackingError = 'An error occurred while tracking. Please try again.';
            } finally {
                this.trackingLoading = false;
            }
        },

        async cancelAppointment() {
            if (!this.trackedAppointment) return;

            this.cancelLoading = true;
            this.cancelError = '';

            try {
                const url = this.cancelUrlFormat.replace(':id', this.trackedAppointment.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    this.trackedAppointment.status = 'cancelled';
                    this.cancelSuccess = 'Appointment cancelled successfully.';
                    this.showCancelConfirm = false;
                } else {
                    this.cancelError = data.error || 'Failed to cancel appointment.';
                }
            } catch (error) {
                this.cancelError = 'An error occurred while cancelling. Please try again.';
            } finally {
                this.cancelLoading = false;
            }
        },

        closeModal() {
            this.open = false;
            this.submitted = false;
            this.agreedToTerms = false;
        },

        fetchDoctors(serviceType) {
            this.doctors = [];

            if (!serviceType) {
                this.showDoctor = false;
                return;
            }

            this.loadingDoctors = true;
            this.showDoctor = true;

            fetch(`${this.doctorsUrl}?service_type=${serviceType}`)
                .then(response => response.json())
                .then(data => {
                    this.doctors = data.doctors || [];
                    if (this.selectedDoctor) {
                        const doctorExists = this.doctors.find(d => d.name === this.selectedDoctor);
                        if (!doctorExists) {
                            this.selectedDoctor = '';
                            this.selectedSpecialization = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching doctors:', error);
                    this.doctors = [];
                })
                .finally(() => {
                    this.loadingDoctors = false;
                });
        },

        updateSpecialization(event) {
            const selectedOption = event.target.selectedOptions[0];
            if (selectedOption) {
                this.selectedSpecialization = selectedOption.dataset.specialization;
            } else {
                this.selectedSpecialization = '';
            }
        }
    }));
});

document.addEventListener("DOMContentLoaded", () => {
    // Sub NavLink Highlight JS
    const sections = document.querySelectorAll("section[id]");
    const subLinks = document.querySelectorAll(".sub-link");

    if (sections.length > 0 && subLinks.length > 0) {
        window.addEventListener("scroll", () => {
            let scrollPos = window.scrollY + 150;
            sections.forEach(section => {
                if (scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight) {
                    subLinks.forEach(link => {
                        link.classList.remove("active");
                        if (link.getAttribute("href") === "#" + section.id) {
                            link.classList.add("active")
                        }
                    });
                }
            });
        });
    }
});
