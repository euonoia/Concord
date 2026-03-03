document.addEventListener("alpine:init", () => {
    Alpine.data("landingForm", () => ({
        open: window.hasErrors || window.hasSuccess,
        submitted: false,
        showDoctor: window.hasOldServiceType,
        doctors: [],
        loadingDoctors: false,
        selectedDoctor: window.oldDoctorName || '',
        selectedSpecialization: window.oldSpecialization || '',
        showDetails: window.hasTrackedAppointment,
        showCancelConfirm: false,
        agreedToTerms: false,

        // AJAX Tracking State
        trackingReference: '',
        trackedAppointment: window.trackedAppointmentData,
        trackingLoading: false,
        trackingError: '',
        cancelLoading: false,
        cancelError: '',
        cancelSuccess: window.cancelSuccessMsg || '',

        async trackAppointment() {
            if (!this.trackingReference) return;
            this.trackingLoading = true;
            this.trackingError = '';
            this.cancelSuccess = '';

            try {
                const response = await fetch(window.lookupRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({ appointment_reference: this.trackingReference })
                });

                const data = await response.json();

                if (response.ok) {
                    this.trackedAppointment = data;
                    this.showDetails = true;
                } else {
                    this.trackingError = data.error || 'Appointment not found.';
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
                const response = await fetch(window.cancelRoute.replace(':id', this.trackedAppointment.id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
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

        fetchDoctors() {
            const serviceType = document.getElementById('service_type').value;
            this.doctors = [];

            if (!serviceType) {
                this.showDoctor = false;
                return;
            }

            this.loadingDoctors = true;
            this.showDoctor = true;

            fetch(`${window.doctorsRoute}?service_type=${serviceType}`)
                .then(response => response.json())
                .then(data => {
                    this.doctors = data;
                    if (this.selectedDoctor) {
                        const doctorExists = this.doctors.find(d => `${d.first_name} ${d.last_name}` === this.selectedDoctor);
                        if (!doctorExists) {
                            this.selectedDoctor = '';
                            this.selectedSpecialization = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching doctors:', error);
                })
                .finally(() => {
                    this.loadingDoctors = false;
                });
        },

        updateSpecialization() {
            const selectedDoc = this.doctors.find(d => `${d.first_name} ${d.last_name}` === this.selectedDoctor);
            if (selectedDoc) {
                this.selectedSpecialization = selectedDoc.specialization;
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
