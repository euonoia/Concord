@extends('core.core1.layouts.app')

@section('title', 'Medical Records')

@section('content')
<link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="core1-container">

    {{-- Page Header --}}
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Medical Records</h1>
            <p class="core1-subtitle">View and manage patient health records</p>
        </div>
        <div style="font-size: 12px; color: var(--text-gray); background: var(--bg); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
            <i class="bi bi-clock" style="color: var(--primary);"></i>
            <span>{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="core1-card no-hover has-header overflow-hidden" style="padding:0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div class="core1-card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: var(--card-bg);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="core1-icon-box" style="background: var(--info-light); color: var(--info); width:40px; height:40px; border-radius:10px; font-size:1.2rem; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h2 class="core1-title core1-section-title mb-0" style="font-size:16px; font-weight: 600;">Patient Health Records</h2>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: var(--text-gray);">Comprehensive clinical documentation</p>
                </div>
            </div>
            
            {{-- Search/Filter Form --}}
            <form method="GET" action="{{ route('core1.medical-records.index') }}" style="display: flex; align-items: center; gap: 10px;">
                <div style="display: flex; align-items: center; background: var(--bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; height: 38px;">
                    <i class="bi bi-search" style="color: var(--text-gray); font-size: 13px; margin-right: 8px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patients..." style="border: none; background: transparent; outline: none; font-size: 13px; width: 200px; color: var(--text-dark);">
                </div>
                <button type="submit" class="core1-btn" style="height: 38px; padding: 0 16px; background: #476a8a; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 500;">
                    Search
                </button>
            </form>
        </div>

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
    </div>
</div>

{{-- Medical Record Modal --}}
<div id="medicalRecordModal" class="core1-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index:1100; align-items:center; justify-content:center; padding: 20px;" role="dialog" aria-modal="true">
    <div class="core1-modal-content core1-card" style="width: 100%; max-width: 950px; padding:0; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid rgba(255,255,255,0.1);">

        {{-- Modal Header --}}
        <div style="padding: 20px 28px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; background: #ffffff;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: var(--text-dark); letter-spacing: -0.01em;">Clinical Overview</h3>
                    <p style="margin: 0; font-size: 13px; color: var(--text-gray); display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-shield-check" style="color: var(--success);"></i> Secure Health Record
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeRecordModal()" style="background: var(--bg-hover); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-gray); transition: all 0.2s;">
                <i class="bi bi-x-lg" style="font-size: 14px;"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="flex: 1; overflow-y: auto; padding: 0; background: var(--bg);" id="modalContentWrapper">
            <div id="modalLoader" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding: 80px 0; background: #ffffff;">
                <div style="width: 48px; height: 48px; border: 3px solid var(--primary-light); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 16px;"></div>
                <h4 style="margin: 0 0 8px 0; font-weight: 600; color: var(--text-dark); font-size: 15px;">Retrieving Records</h4>
                <p style="font-size: 13px; color: var(--text-gray); margin: 0;">Accessing secure clinical database...</p>
            </div>
            <div id="modalContentInner" class="w-full" style="padding: 24px;"></div>
        </div>
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.core1-btn-sm:hover {
    background: var(--primary) !important;
    color: white !important;
}

/* Fix Laravel Tailwind pagination without Tailwind classes */
nav[role="navigation"] {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    margin-top: 8px;
}
/* Hide the mobile pagination div */
nav[role="navigation"] > div:first-of-type {
    display: none !important;
}
/* Flex the desktop pagination container - placed on left */
nav[role="navigation"] > div:last-of-type {
    display: flex !important;
    width: 100%;
    align-items: center;
    justify-content: flex-start !important;
    gap: 16px;
}
/* Ensure text container is displayed and moved to the right */
nav[role="navigation"] > div:last-of-type > div:first-child {
    display: block !important;
    order: 2; /* Place after buttons */
}
/* The pagination links wrapper (moved to the left) */
nav[role="navigation"] > div:last-of-type > div:last-child {
    order: 1; /* Place before text */
}
/* Text ("Showing X to Y...") */
nav[role="navigation"] p {
    font-size: 13px;
    color: var(--text-gray);
    margin: 0;
    display: block !important; /* Overrides Laravel's .hidden class */
}
nav[role="navigation"] p span {
    font-weight: 600;
    color: var(--text-dark);
}
/* The pagination links wrapper */
nav[role="navigation"] .shadow-sm {
    display: flex;
    gap: 6px;
    box-shadow: none !important;
}

/* Structural clear: Remove borders from wrapper spans */
nav[role="navigation"] .shadow-sm > span {
    border: none !important;
    background: transparent !important;
    padding: 0 !important;
    box-shadow: none !important;
}

/* Base style for physical buttons */
nav[role="navigation"] .shadow-sm a,
nav[role="navigation"] .shadow-sm span[aria-disabled="true"] > span,
nav[role="navigation"] .shadow-sm span[aria-current="page"] > span {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 10px;
    border: 1px solid var(--border-color) !important;
    background: white !important;
    color: var(--text-dark) !important;
    font-size: 13px;
    border-radius: 6px !important;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: none !important;
    margin: 0 !important;
}

/* Hover state for clickable links */
nav[role="navigation"] .shadow-sm a:hover {
    background: var(--bg-hover) !important;
    color: var(--primary) !important;
}

/* Active state */
nav[role="navigation"] .shadow-sm span[aria-current="page"] > span {
    background: var(--primary) !important;
    color: white !important;
    border-color: var(--primary) !important;
}

/* Disabled state */
nav[role="navigation"] .shadow-sm span[aria-disabled="true"] > span {
    opacity: 0.5;
    background: var(--bg-light) !important;
    color: var(--text-gray) !important;
}

/* Fix SVG size */
nav[role="navigation"] svg {
    width: 16px;
    height: 16px;
}
</style>

<script>
    function openRecordModal(url) {
        const modal = document.getElementById('medicalRecordModal');
        const loader = document.getElementById('modalLoader');
        const content = document.getElementById('modalContentInner');

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        loader.style.display = 'flex';
        content.innerHTML = '';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const titles = doc.querySelectorAll('h1');
            titles.forEach(h => {
                if (h.innerText.includes('Medical Record Details')) {
                    const row = h.closest('.flex.justify-between');
                    if(row) row.remove();
                }
            });
            content.innerHTML = doc.body.innerHTML;
            loader.style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching record:', error);
            content.innerHTML = `<div style="padding:40px;text-align:center;color:var(--danger);font-weight:700;"><i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;display:block;margin-bottom:8px;"></i>Failed to load record details. Please try again.</div>`;
            loader.style.display = 'none';
        });
    }

    function closeRecordModal() {
        document.getElementById('medicalRecordModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('medicalRecordModal').addEventListener('click', function(e) {
            if (e.target === this) closeRecordModal();
        });
    });
</script>
@endsection