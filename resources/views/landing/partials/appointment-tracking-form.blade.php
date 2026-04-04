        <div class="appointment-lookup-divider">
            <span>or</span>
        </div>
        <div class="appointment-tracking-header">
            <p>Track your appointment or cancel it.</p>
        </div>

        <form @submit.prevent="trackAppointment()" class="appointment-lookup-form">
            <div class="appointment-lookup-input-group">
                <input type="text" x-model="trackingReference" placeholder="Enter your reference number" class="appointment-lookup-input" required>
                <button type="submit" class="btn btn-track" :disabled="trackingLoading">
                    <i x-show="!trackingLoading" class="bi bi-search mr-1"></i>
                    <svg x-show="trackingLoading" style="display: none;" class="animate-spin h-4 w-4 mr-1" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Track Appointment
                </button>
            </div>
            <p x-show="trackingError" x-text="trackingError" style="display: none;" class="appointment-lookup-error"></p>
        </form>