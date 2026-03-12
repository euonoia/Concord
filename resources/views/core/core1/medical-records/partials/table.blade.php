<div class="core1-table-container shadow-none">
    <table class="core1-table" style="width: 100%;">
        <thead>
            <tr>
                <th style="padding: 16px 24px; font-weight: 600; font-size: 12px; color: var(--text-gray); text-transform: uppercase;">Patient</th>
                <th style="padding: 16px 24px; font-weight: 600; font-size: 12px; color: var(--text-gray); text-transform: uppercase;">Medical Info</th>
                <th style="padding: 16px 24px; font-weight: 600; font-size: 12px; color: var(--text-gray); text-transform: uppercase;">Latest Record</th>
                <th style="padding: 16px 24px; font-weight: 600; font-size: 12px; color: var(--text-gray); text-transform: uppercase;">Last Update</th>
                <th class="text-center" style="padding: 16px 24px; font-weight: 600; font-size: 12px; color: var(--text-gray); text-transform: uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $patient)
                @php
                    $latestRecord = $patient->medicalRecords->first();
                    $latestAppointment = $patient->appointments->first();
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px;">
                                {{ strtoupper(substr($patient->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-dark); font-size: 14px;">{{ $patient->name }}</div>
                                <div style="font-size: 12px; color: var(--text-gray); font-family: monospace; margin-top: 2px;">{{ $patient->patient_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-dark);">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 6px; background: var(--danger-light); color: var(--danger);">
                                <i class="bi bi-droplet-fill" style="font-size: 11px;"></i>
                            </span>
                            {{ $patient->blood_type ?? 'Unknown' }}
                        </div>
                    </td>
                    <td style="padding: 16px 24px;">
                        @if($latestRecord)
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--success-light); color: var(--success); border-radius: 6px; font-size: 12px; font-weight: 500;">
                                <i class="bi bi-journal-medical"></i> {{ $latestRecord->record_type }}
                            </span>
                        @elseif($latestAppointment)
                            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: var(--warning-light-more); color: var(--warning); border-radius: 6px; font-size: 12px; font-weight: 500;">
                                <i class="bi bi-calendar-event"></i> {{ ucfirst($latestAppointment->type ?? 'N/A') }}
                            </span>
                        @else
                            <span style="color: var(--text-gray); font-size: 13px;">No clinical records</span>
                        @endif
                    </td>
                    <td style="padding: 16px 24px; font-size: 13px; color: var(--text-gray);">
                        @if($latestRecord)
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <i class="bi bi-clock-history"></i>
                                {{ $latestRecord->record_date->format('M d, Y') }}
                            </div>
                        @elseif($latestAppointment)
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <i class="bi bi-clock-history"></i>
                                {{ \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('M d, Y') }}
                            </div>
                        @else
                            <span style="color: var(--text-light);">---</span>
                        @endif
                    </td>
                    <td class="text-center" style="padding: 16px 24px;">
                        <button type="button" onclick="openRecordModal('{{ route('core1.medical-records.show', $patient->id) }}')"
                            class="core1-btn-sm" style="background: transparent; border: 1px solid var(--primary); color: var(--primary); padding: 6px 14px; border-radius: 6px; font-weight: 500; transition: all 0.2s;">
                            <i class="bi bi-eye mr-5"></i> View Record
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 60px 20px;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: var(--bg-light); color: var(--text-light); margin-bottom: 16px;">
                            <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                        </div>
                        <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: var(--text-dark);">No Records Found</h3>
                        <p style="margin: 0; font-size: 13px; color: var(--text-gray);">There are no medical records available for the current search.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($records->hasPages())
<div style="padding: 16px 24px; border-top: 1px solid var(--border-color); background: var(--bg);">
    {{ $records->links() }}
</div>
@endif
