<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        }
    }
</script>

<div class="core1-header">
    <h2 class="core1-title">Receptionist Overview</h2>
    <p class="core1-subtitle">Manage appointments and patient registrations</p>
</div>

<div class="core1-stats-grid">
    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Appointments</h3>
            <p class="core1-title text-blue">{{ $stats['today_appointments'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Today's Registrations</h3>
            <p class="core1-title text-green">{{ $stats['today_registrations'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Total Patients</h3>
            <p class="core1-title text-purple">{{ $stats['total_patients'] ?? 0 }}</p>
        </div>
    </div>

    <div class="core1-stat-card">
        <div class="d-flex flex-col items-center w-full">
            <h3 class="core1-info-item h3 text-center mb-10">Pending Appointments</h3>
            <p class="core1-title text-orange" id="pending-count">{{ $stats['pending_appointments'] ?? 0 }}</p>
        </div>
    </div>
</div>



<!-- Rejection Modal -->
<div id="rejectModal" class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" style="z-index: -1;"></div>
    
    <!-- Modal Panel -->
    <div class="flex min-h-screen w-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="mb-6">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4">
                            <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold leading-6 text-gray-900 text-center" id="modal-title">
                            Reject Appointment <span id="rejectRefNo" class="text-red-700"></span>
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 text-center">
                            Please provide a reason for rejecting this appointment. This will be sent to the patient.
                        </p>
                    </div>

                    <div class="relative">
                        <textarea 
                            name="rejection_reason" 
                            id="rejection_reason"
                            rows="4" 
                            class="peer block w-full rounded-lg border-gray-300 px-3 pt-5 pb-2 text-gray-900 focus:border-red-600 focus:ring-red-600 placeholder-transparent sm:text-sm" 
                            placeholder="Reason for rejection..."
                            required
                        ></textarea>
                        <label for="rejection_reason" class="absolute left-3 top-1 z-10 origin-[0] -translate-y-2 scale-75 transform text-sm text-red-600 duration-300 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:scale-100 peer-focus:-translate-y-2 peer-focus:scale-75 peer-focus:text-red-700">
                            Rejection Reason
                        </label>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeRejectModal()" 
                        class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all"
                    >
                        Reject Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRejectModal(id, refNo) {
        // Use the route helper but with a placeholder for the ID
        let baseUrl = "{{ route('core1.receptionist.online-appointments.reject', ':id') }}";
        document.getElementById('rejectForm').action = baseUrl.replace(':id', id);
        
        document.getElementById('rejectRefNo').innerText = refNo;
        document.getElementById('rejectModal').style.display = 'block';
        // Clear previous input
        if (document.getElementById('rejection_reason')) {
            document.getElementById('rejection_reason').value = '';
        }
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
    
    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('rejectModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                // Check if click is on the backdrop or the div that acts as the backdrop
                if (e.target === modal || e.target.classList.contains('backdrop-blur-sm') || e.target.classList.contains('min-h-screen')) {
                    closeRejectModal();
                }
            });
        }
    });
</script>

<div class="core1-dashboard-split">
    <!-- Today's Appointments -->
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Today's Appointments</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $appointment)
                    <tr>
                        <td>
                            <div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                        <td>{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : 'N/A' }}</td>
                        <td>{{ ucfirst($appointment->type ?? 'N/A') }}</td>
                        <td>
                            @php
                                $statusClass = 'tag-pending';
                                if($appointment->status == 'scheduled') $statusClass = 'core1-tag-recovering';
                                if($appointment->status == 'completed') $statusClass = 'core1-tag-stable';
                            @endphp
                            <span class="core1-status-tag {{ $statusClass }}">{{ ucfirst($appointment->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state-cell text-center p-40">No appointments scheduled for today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card">
        <div class="core1-card-header">
            <h2 class="core1-title core1-section-title mb-0">Recent Patient Registrations</h2>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRegistrations as $patient)
                    <tr>
                        <td class="text-xs text-gray font-mono">{{ $patient->patient_id ?? $patient->id }}</td>
                        <td class="font-bold text-blue">{{ $patient->name }}</td>
                        <td>{{ ucfirst($patient->gender) }}</td>
                        <td>{{ $patient->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state-cell text-center p-40">No recent registrations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Online Bookings -->
<div class="core1-card no-hover has-header overflow-hidden core1-scroll-card mb-4">
    <div class="core1-card-header">
        <h2 class="core1-title core1-section-title mb-0">Pending Online Bookings</h2>
    </div>
    <div class="core1-table-container shadow-none core1-scroll-area">
        <table class="core1-table">
            <thead>
                <tr>
                    <th class="text-left">Ref No</th>
                    <th class="text-left">Patient</th>
                    <th class="text-left">Details</th>
                    <th class="text-left">Date/Time</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="online-bookings-tbody">
                @forelse($onlineBookings as $booking)
                <tr>
                    <td>
                        <div class="font-bold text-blue">{{ $booking->appointment_no }}</div>
                    </td>
                    <td>
                        <div class="font-bold text-blue">{{ $booking->name }}</div>
                        <div class="text-xs text-gray-500">{{ $booking->email }}</div>
                    </td>
                    <td>
                        <div class="font-bold">{{ ucfirst(str_replace('_', ' ', $booking->service_type)) }}</div>
                        @if($booking->doctor_name)
                        <div class="text-xs text-gray-500 mt-1">Dr: {{ $booking->doctor_name }}</div>
                        @endif
                    </td>
                    <td>
                        <div>{{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <form action="{{ route('core1.receptionist.online-appointments.approve', $booking->id) }}" method="POST" onsubmit="return confirm('Approve this appointment?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success text-white">Approve</button>
                            </form>
                            
                            <button type="button" class="btn btn-sm btn-danger text-white" onclick="openRejectModal('{{ $booking->id }}', '{{ $booking->appointment_no }}')">Reject</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-state-cell text-center p-40">No pending online bookings.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pollUrl = "{{ route('core1.receptionist.online-appointments.pending') }}";
        const approveBaseUrl = "{{ route('core1.receptionist.online-appointments.approve', ':id') }}";
        const csrfToken = "{{ csrf_token() }}";

        function formatDate(dateStr) {
            const d = new Date(dateStr);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return months[d.getMonth()] + ' ' + String(d.getDate()).padStart(2, '0') + ', ' + d.getFullYear();
        }

        function formatTime(timeStr) {
            const parts = timeStr.split(':');
            let h = parseInt(parts[0]);
            const m = parts[1];
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return h + ':' + m + ' ' + ampm;
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function refreshBookings() {
            fetch(pollUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('online-bookings-tbody');
                const pendingCount = document.getElementById('pending-count');
                if (!tbody) return;

                if (pendingCount) {
                    pendingCount.textContent = data.pending_count;
                }

                if (data.bookings.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="empty-state-cell text-center p-40">No pending online bookings.</td></tr>';
                    return;
                }

                let html = '';
                data.bookings.forEach(function(booking) {
                    const approveUrl = approveBaseUrl.replace(':id', booking.id);
                    const serviceType = booking.service_type.replace(/_/g, ' ');
                    const serviceTypeDisplay = serviceType.charAt(0).toUpperCase() + serviceType.slice(1);

                    html += '<tr>';
                    html += '<td><div class="font-bold text-blue">' + escapeHtml(booking.appointment_no) + '</div></td>';
                    html += '<td><div class="font-bold text-blue">' + escapeHtml(booking.name) + '</div>';
                    html += '<div class="text-xs text-gray-500">' + escapeHtml(booking.email) + '</div></td>';
                    html += '<td><div class="font-bold">' + escapeHtml(serviceTypeDisplay) + '</div>';
                    if (booking.doctor_name) {
                        html += '<div class="text-xs text-gray-500 mt-1">Dr: ' + escapeHtml(booking.doctor_name) + '</div>';
                    }
                    html += '</td>';
                    html += '<td><div>' + formatDate(booking.appointment_date) + '</div>';
                    html += '<div class="text-xs text-gray-500">' + formatTime(booking.appointment_time) + '</div></td>';
                    html += '<td><div class="flex gap-2">';
                    html += '<form action="' + approveUrl + '" method="POST" onsubmit="return confirm(\'Approve this appointment?\');">';
                    html += '<input type="hidden" name="_token" value="' + csrfToken + '">';
                    html += '<button type="submit" class="btn btn-sm btn-success text-white">Approve</button></form>';
                    html += '<button type="button" class="btn btn-sm btn-danger text-white" onclick="openRejectModal(\'' + booking.id + '\', \'' + escapeHtml(booking.appointment_no) + '\')">Reject</button>';
                    html += '</div></td>';
                    html += '</tr>';
                });

                tbody.innerHTML = html;
            })
            .catch(err => console.error('Polling error:', err));
        }

        // Poll every 10 seconds
        setInterval(refreshBookings, 10000);
    });
</script>
