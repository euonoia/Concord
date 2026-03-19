<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Receptionist Overview</h1>
            <p class="core1-subtitle">Manage appointments and patient registrations</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Today's Appointments</p>
                <p class="core1-title text-blue">{{ $stats['today_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Scheduled for today</p>
            </div>
            <div class="core1-icon-box core1-icon-blue">
                <i class="bi bi-calendar2-check"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Today's Registrations</p>
                <p class="core1-title text-green">{{ $stats['today_registrations'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">New patients today</p>
            </div>
            <div class="core1-icon-box core1-icon-green">
                <i class="bi bi-person-plus-fill"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Total Patients</p>
                <p class="core1-title text-purple">{{ $stats['total_patients'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">In the system</p>
            </div>
            <div class="core1-icon-box core1-icon-purple">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <div class="core1-stat-card">
            <div>
                <p class="text-sm text-gray mb-5">Pending Appointments</p>
                <p class="core1-title text-orange" id="pending-count">{{ $stats['pending_appointments'] ?? 0 }}</p>
                <p class="text-xs text-gray mt-5">Awaiting action</p>
            </div>
            <div class="core1-icon-box core1-icon-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="core1-modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;" role="dialog" aria-modal="true">
        <div class="core1-modal-content core1-card" style="width: 500px; max-width: 90%; padding: 0; border-radius: 14px; overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 9px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="bi bi-x-octagon-fill"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-dark);">Reject Appointment <span id="rejectRefNo" style="color: var(--danger);"></span></h3>
                    <p style="margin: 0; font-size: 12px; color: var(--text-gray);">This reason will be sent to the patient.</p>
                </div>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div style="padding: 20px 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 6px;">Rejection Reason <span style="color: var(--danger);">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" required placeholder="Provide a clear reason for rejecting this appointment..."
                        style="width: 100%; padding: 10px 12px; border: 1.5px solid var(--border-color); border-radius: 8px; font-size: 13px; color: var(--text-dark); background: var(--bg); resize: vertical; font-family: inherit;"></textarea>
                </div>
                <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeRejectModal()" class="core1-btn core1-btn-outline">Cancel</button>
                    <button type="submit" class="core1-btn core1-btn-danger" style="background: var(--danger); color: white; border: none;">Reject Appointment</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Dashboard Split --}}
    <div class="core1-dashboard-split">

        {{-- Today's Appointments --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--primary-light); color: var(--primary); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-list-check"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Today's Appointments</h2>
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
                            <td><div class="font-bold text-blue">{{ $appointment->patient->name ?? 'N/A' }}</div></td>
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
                            <td colspan="5" class="text-center p-40">
                                <i class="bi bi-calendar-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No appointments today.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Registrations --}}
        <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card" style="padding:0; border-radius: 12px;">
            <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <div class="core1-icon-box" style="background: var(--success-light); color: var(--success); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Recent Patient Registrations</h2>
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
                            <td class="text-xs text-gray font-mono">{{ $patient->mrn ?? $patient->id }}</td>
                            <td class="font-bold text-blue">{{ $patient->name }}</td>
                            <td>{{ ucfirst($patient->gender) }}</td>
                            <td>{{ $patient->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-40">
                                <i class="bi bi-person-x" style="font-size: 1.8rem; color: var(--text-light); display: block; margin-bottom: 6px;"></i>
                                No recent registrations.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pending Online Bookings --}}
    <div class="core1-card no-hover has-header overflow-hidden core1-scroll-card mb-4" style="padding:0; border-radius: 12px; margin-top: 24px;">
        <div class="core1-card-header" style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
            <div class="d-flex items-center gap-3">
                <div class="core1-icon-box" style="background: var(--warning-light-more); color: var(--warning); width:34px; height:34px; border-radius:7px; font-size:1rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-globe"></i>
                </div>
                <h2 class="core1-title core1-section-title mb-0" style="font-size:15px;">Pending Online Bookings</h2>
            </div>
            <span style="font-size: 11px; font-weight: 700; background: var(--warning-light-more); color: var(--warning); padding: 3px 10px; border-radius: 999px; border: 1px solid var(--warning);">
                Live
            </span>
        </div>
        <div class="core1-table-container shadow-none core1-scroll-area">
            <table class="core1-table">
                <thead>
                    <tr>
                        <th>Ref No</th>
                        <th>Patient</th>
                        <th>Details</th>
                        <th>Date/Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="online-bookings-tbody">
                    @forelse($onlineBookings as $booking)
                    <tr>
                        <td><div class="font-bold" style="color: var(--primary);">{{ $booking->appointment_no }}</div></td>
                        <td>
                            <div class="font-bold text-blue">{{ $booking->patient->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray">{{ $booking->patient->email ?? '' }}</div>
                        </td>
                        <td>
                            <div class="font-bold">{{ ucfirst(str_replace('_', ' ', $booking->service_type ?? $booking->type)) }}</div>
                            @php
                                $docName = $booking->doctor_name;
                                if (!$docName && $booking->doctor && $booking->doctor->employee) {
                                    $docName = $booking->doctor->employee->name;
                                }
                            @endphp
                            @if($docName)
                            <div class="text-xs text-gray mt-1">Dr: {{ $docName }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}</div>
                            <div class="text-xs text-gray">{{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <form action="{{ route('core1.receptionist.online-appointments.approve', $booking->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Approve this appointment?');">
                                    @csrf
                                    <button type="submit" class="core1-btn-sm core1-btn-primary" style="padding: 5px 12px; font-size: 11px;">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="core1-btn-sm" onclick="openRejectModal('{{ $booking->id }}', '{{ $booking->appointment_no }}')"
                                    style="padding: 5px 12px; font-size: 11px; background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); border-radius: 7px; cursor: pointer;">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-40">
                            <i class="bi bi-check2-circle" style="font-size: 1.8rem; color: var(--success); display: block; margin-bottom: 6px;"></i>
                            No pending online bookings.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openRejectModal(id, refNo) {
        let baseUrl = "{{ route('core1.receptionist.online-appointments.reject', ':id') }}";
        document.getElementById('rejectForm').action = baseUrl.replace(':id', id);
        document.getElementById('rejectRefNo').innerText = refNo;
        document.getElementById('rejectModal').style.display = 'flex';
        if (document.getElementById('rejection_reason')) {
            document.getElementById('rejection_reason').value = '';
        }
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('rejectModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeRejectModal();
            });
        }

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
            fetch(pollUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('online-bookings-tbody');
                const pendingCount = document.getElementById('pending-count');
                if (!tbody) return;

                if (pendingCount) pendingCount.textContent = data.pending_count;

                if (data.bookings.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center p-40"><i class="bi bi-check2-circle" style="font-size:1.8rem;color:var(--success);display:block;margin-bottom:6px;"></i>No pending online bookings.</td></tr>';
                    return;
                }

                let html = '';
                data.bookings.forEach(function(booking) {
                    const approveUrl = approveBaseUrl.replace(':id', booking.id);
                    const serviceType = booking.service_type ? booking.service_type.replace(/_/g, ' ') : 'N/A';
                    const serviceTypeDisplay = serviceType !== 'N/A' ? (serviceType.charAt(0).toUpperCase() + serviceType.slice(1)) : 'N/A';

                    html += '<tr>';
                    html += '<td><div class="font-bold" style="color:var(--primary);">' + escapeHtml(booking.appointment_no) + '</div></td>';
                    html += '<td><div class="font-bold text-blue">' + escapeHtml(booking.name) + '</div>';
                    html += '<div class="text-xs text-gray">' + escapeHtml(booking.email) + '</div></td>';
                    html += '<td><div class="font-bold">' + escapeHtml(serviceTypeDisplay) + '</div>';
                    if (booking.doctor_name) {
                        html += '<div class="text-xs text-gray mt-1">Dr: ' + escapeHtml(booking.doctor_name) + '</div>';
                    }
                    html += '</td>';
                    html += '<td><div>' + formatDate(booking.appointment_date) + '</div>';
                    html += '<div class="text-xs text-gray">' + formatTime(booking.appointment_time) + '</div></td>';
                    html += '<td><div style="display:flex;align-items:center;gap:8px;">';
                    html += '<form action="' + approveUrl + '" method="POST" style="margin:0;" onsubmit="return confirm(\'Approve this appointment?\');">';
                    html += '<input type="hidden" name="_token" value="' + csrfToken + '">';
                    html += '<button type="submit" class="core1-btn-sm core1-btn-primary" style="padding:5px 12px;font-size:11px;"><i class="bi bi-check-lg"></i> Approve</button></form>';
                    html += '<button type="button" class="core1-btn-sm" onclick="openRejectModal(\'' + booking.id + '\', \'' + escapeHtml(booking.appointment_no) + '\')" style="padding:5px 12px;font-size:11px;background:var(--danger-light);color:var(--danger);border:1px solid var(--danger);border-radius:7px;cursor:pointer;"><i class="bi bi-x-lg"></i> Reject</button>';
                    html += '</div></td>';
                    html += '</tr>';
                });

                tbody.innerHTML = html;
            })
            .catch(err => console.error('Polling error:', err));
        }

        setInterval(refreshBookings, 10000);
    });
</script>
