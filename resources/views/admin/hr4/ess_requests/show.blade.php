@extends('admin.hr4.layouts.app')

@section('title', 'ESS Request Details - HR4')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:           #eef3f7;
        --c-surface:      #ffffff;
        --c-border:       #d4e3ee;
        --c-teal:         #0a7c6e;
        --c-teal-light:   #e4f4f1;
        --c-teal-mid:     #b8e0da;
        --c-blue:         #1a5f8a;
        --c-blue-light:   #e8f2f9;
        --c-green:        #1a7a52;
        --c-green-light:  #e4f5ed;
        --c-green-mid:    #b2ddc8;
        --c-red:          #be123c;
        --c-red-light:    #fce7ef;
        --c-yellow:       #92400e;
        --c-yellow-light: #fefce8;
        --c-yellow-border:#fde68a;
        --c-text:         #1b2b3a;
        --c-muted:        #5c798e;
        --c-line:         #dde8f0;
        --shadow-sm:      0 1px 4px rgba(10,50,80,.07);
        --shadow-hover:   0 10px 36px rgba(10,124,110,.13);
    }

    .aj * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .aj {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Back Link ── */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: var(--c-blue);
        font-size: .83rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: color .2s;
    }

    .back-link:hover { color: var(--c-teal); text-decoration: none; }

    /* ── Layout Grid ── */
    .aj-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 900px) {
        .aj-layout { grid-template-columns: 1fr; }
    }

    /* ── Card ── */
    .aj-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 1.5rem;
        animation: fadeUp .45s ease both;
    }

    .aj-card:last-child { margin-bottom: 0; }

    .aj-card-body { padding: 1.5rem; }

    /* ── Card Header ── */
    .aj-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
    }

    .aj-card-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 1.8rem;
        color: var(--c-text);
        line-height: 1.1;
        margin: 0;
    }

    .aj-card-header h1 em { color: var(--c-teal); font-style: italic; }
    .aj-card-header p { font-size: .82rem; color: var(--c-muted); margin-top: .3rem; margin-bottom: 0; }

    /* ── Section Title ── */
    .section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .83rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--c-muted);
        margin-bottom: 1rem;
    }

    .section-title i { color: var(--c-blue); font-size: .9rem; }

    /* ── Info Box ── */
    .info-box {
        background: var(--c-bg);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: 1.1rem 1.2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .info-item .info-label {
        font-size: .71rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--c-muted);
        margin-bottom: .25rem;
    }

    .info-item .info-value {
        font-size: .88rem;
        font-weight: 500;
        color: var(--c-text);
    }

    .info-stack { display: flex; flex-direction: column; gap: .9rem; }

    /* ── Description Box ── */
    .desc-box {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 8px;
        padding: 1rem;
        margin-top: .5rem;
        font-size: .85rem;
        color: var(--c-text);
        white-space: pre-wrap;
        line-height: 1.6;
    }

    /* ── Section Divider ── */
    .aj-section {
        padding: 1.5rem;
        border-bottom: 1px solid var(--c-line);
    }

    .aj-section:last-child { border-bottom: none; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .35rem .9rem;
        border-radius: 99px;
        font-size: .75rem;
        font-weight: 700;
    }

    .badge-green  { background: var(--c-green-light);  color: var(--c-green); }
    .badge-red    { background: var(--c-red-light);    color: var(--c-red); }
    .badge-yellow { background: var(--c-yellow-light); color: var(--c-yellow); border: 1px solid var(--c-yellow-border); }

    /* ── Action Panel (sticky sidebar) ── */
    .aj-sidebar { position: sticky; top: 1.5rem; }

    .aj-sidebar .aj-card { margin-bottom: 0; }

    /* ── Buttons ── */
    .btn-approve {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        width: 100%;
        background: var(--c-green);
        color: #fff;
        font-size: .85rem;
        font-weight: 700;
        padding: .75rem 1rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        margin-bottom: .75rem;
        box-shadow: 0 2px 8px rgba(26,122,82,.2);
        transition: background .2s, transform .2s;
    }

    .btn-approve:hover { background: #156640; transform: translateY(-1px); }

    .btn-reject {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        width: 100%;
        background: var(--c-red-light);
        color: var(--c-red);
        font-size: .85rem;
        font-weight: 700;
        padding: .75rem 1rem;
        border-radius: 9px;
        border: 1.5px solid #f9a8c0;
        cursor: pointer;
        transition: background .2s;
    }

    .btn-reject:hover { background: #fad0de; }

    /* ── Timeline ── */
    .timeline { display: flex; flex-direction: column; gap: 0; }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        position: relative;
    }

    .timeline-dot-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
        padding-top: .1rem;
    }

    .timeline-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dot-blue  { background: var(--c-blue); }
    .dot-green { background: var(--c-green); }
    .dot-red   { background: var(--c-red); }

    .timeline-line {
        width: 1.5px;
        height: 2rem;
        background: var(--c-line);
        margin: .25rem 0;
    }

    .timeline-label {
        font-size: .82rem;
        font-weight: 600;
        color: var(--c-text);
        margin-bottom: .1rem;
    }

    .timeline-date { font-size: .72rem; color: var(--c-muted); }

    /* ── Meta Row ── */
    .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .8rem;
        padding: .35rem 0;
        border-bottom: 1px solid var(--c-line);
    }

    .meta-row:last-child { border-bottom: none; }
    .meta-row span:first-child { color: var(--c-muted); }
    .meta-row span:last-child  { font-weight: 600; color: var(--c-text); }

    /* ── Sidebar section label ── */
    .sidebar-label {
        font-size: .71rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--c-muted);
        margin-bottom: .9rem;
    }

    /* ── Modal ── */
    .aj-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,30,50,.45);
        z-index: 100;
        align-items: center;
        justify-content: center;
    }

    .aj-modal-overlay.open { display: flex; }

    .aj-modal {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 16px;
        box-shadow: var(--shadow-hover);
        padding: 1.75rem;
        width: 100%;
        max-width: 440px;
        animation: fadeUp .3s ease both;
    }

    .aj-modal h2 {
        font-family: 'Instrument Serif', serif;
        font-size: 1.4rem;
        color: var(--c-text);
        margin: 0 0 1.2rem;
    }

    .aj-modal h2 em { color: var(--c-red); font-style: italic; }

    .aj-modal label {
        display: block;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--c-muted);
        margin-bottom: .4rem;
    }

    .aj-modal textarea {
        width: 100%;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        padding: .65rem .85rem;
        font-size: .85rem;
        color: var(--c-text);
        background: var(--c-bg);
        resize: vertical;
        outline: none;
        min-height: 100px;
        margin-bottom: 1.2rem;
        transition: border .2s;
    }

    .aj-modal textarea:focus { border-color: var(--c-red); }

    .modal-actions { display: flex; gap: .75rem; }

    .btn-modal-cancel {
        flex: 1;
        padding: .65rem;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: var(--c-bg);
        color: var(--c-muted);
        font-size: .83rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }

    .btn-modal-cancel:hover { background: var(--c-line); }

    .btn-modal-reject {
        flex: 1;
        padding: .65rem;
        border-radius: 8px;
        border: none;
        background: var(--c-red);
        color: #fff;
        font-size: .83rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
    }

    .btn-modal-reject:hover { background: #9b0f32; }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity: 0; transform: translateY(18px);  } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="aj">

    {{-- ── Back Link ── --}}
    <a href="{{ route('hr4.ess_requests.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Payroll Requests
    </a>

    <div class="aj-layout">

        {{-- ══════════════════ LEFT COLUMN ══════════════════ --}}
        <div>

            {{-- ── Card: Header ── --}}
            <div class="aj-card">
                <div class="aj-card-header">
                    <div>
                        <h1><em>ESS</em> Payroll Request</h1>
                        <p>Request ID: #{{ $essRequest->id }}</p>
                    </div>
                    @if($essRequest->status === 'pending')
                        <span class="badge badge-yellow">
                            <i class="bi bi-clock-history" style="font-size:.65rem;"></i> Pending
                        </span>
                    @elseif($essRequest->status === 'approved')
                        <span class="badge badge-green">
                            <i class="bi bi-check-circle" style="font-size:.65rem;"></i> Approved
                        </span>
                    @else
                        <span class="badge badge-red">
                            <i class="bi bi-x-circle" style="font-size:.65rem;"></i> Rejected
                        </span>
                    @endif
                </div>

                {{-- Employee Information --}}
                <div class="aj-section">
                    <div class="section-title">
                        <i class="bi bi-person-badge"></i> Employee Information
                    </div>
                    <div class="info-box">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">
                                    {{ $essRequest->employee->first_name ?? 'N/A' }} {{ $essRequest->employee->last_name ?? '' }}
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Employee ID</div>
                                <div class="info-value">{{ $essRequest->employee_id }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Department</div>
                                <div class="info-value">{{ $essRequest->employee->department->name ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Position</div>
                                <div class="info-value">{{ $essRequest->employee->position->position_title ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Request Details --}}
                <div class="aj-section">
                    <div class="section-title">
                        <i class="bi bi-file-earmark-text"></i> Request Details
                    </div>
                    <div class="info-box">
                        <div class="info-stack">
                            <div class="info-item">
                                <div class="info-label">Request Type</div>
                                <div class="info-value">{{ ucfirst($essRequest->request_type) }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Request Date</div>
                                <div class="info-value">{{ $essRequest->requested_date?->format('M d, Y') ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Description</div>
                                <div class="desc-box">{{ $essRequest->details ?? 'No details provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payroll Information --}}
                @php
                    // Get salary/net_pay from multiple sources (priority: DirectCompensation > HR2 > Position)
                    $salary = null;
                    $netPay = null;
                    $source = 'N/A';

                    // Fetch HR2 request reference
                    $hr2Request = \Illuminate\Support\Facades\DB::table('payroll_request_hr2')
                        ->where('employee_id', $essRequest->employee_id)
                        ->where('details', $essRequest->details)
                        ->orderByDesc('created_at')
                        ->first();

                    // Source 1: Latest direct compensation in HR4 (current month or past)
                    $compensation = \App\Models\admin\Hr\hr4\DirectCompensation::where('employee_id', $essRequest->employee_id)
                        ->where('month', '<=', now()->format('Y-m'))
                        ->orderByDesc('month')
                        ->first();

                    if ($compensation && $compensation->total_compensation > 0) {
                        $salary = $compensation->total_compensation;
                        // Calculate net pay with standard deductions
                        $deductionRate = 0.045 + 0.04 + 0.02 + 0.15; // SSS + PhilHealth + PAG-IBIG + Income Tax
                        $netPay = $salary - ($salary * $deductionRate);
                        $netPay = max(0, $netPay); // Ensure not negative
                        $source = 'Direct Compensation (HR4)';
                    }

                    // Source 2: HR2 sync table if DirectComp not available
                    if ((!$salary || $salary <= 0) && $hr2Request) {
                        $salary = $hr2Request->salary ?? 0;
                        $netPay = $hr2Request->net_pay ?? $salary;
                        $source = 'HR2 Sync';
                    }

                    // Source 3: Position base salary fallback
                    if ((!$salary || $salary <= 0) && $essRequest->employee && $essRequest->employee->position) {
                        $salary = $essRequest->employee->position->base_salary ?? 0;
                        $netPay = $salary;
                        $source = 'Position Base Salary';
                    }

                    // Ensure net_pay is set
                    if ((!$netPay || $netPay <= 0) && $salary > 0) {
                        $netPay = $salary;
                    }
                @endphp
                
                @if($salary > 0 || $netPay > 0)
                    <div class="aj-section">
                        <div class="section-title">
                            <i class="bi bi-calculator"></i> Payroll Information
                        </div>
                        <div class="info-box">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Gross Salary</div>
                                    <div class="info-value" style="color: var(--c-green); font-weight: 600; font-size: 1.1rem;">
                                        ₱{{ number_format($salary, 2) }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Net Pay</div>
                                    <div class="info-value" style="color: var(--c-teal); font-weight: 600; font-size: 1.1rem;">
                                        ₱{{ number_format($netPay, 2) }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Source</div>
                                    <div class="info-value">{{ $source }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Approval Information --}}
                @if($essRequest->status !== 'pending')
                    <div class="aj-section">
                        <div class="section-title">
                            <i class="bi bi-check-circle"></i> Approval Information
                        </div>
                        <div class="info-box">
                            <div class="info-stack">
                                <div class="info-item">
                                    <div class="info-label">Reviewed By</div>
                                    <div class="info-value">
                                        @if($essRequest->approvedBy)
                                            {{ $essRequest->approvedBy->name ?? $essRequest->approvedBy->username }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Review Date</div>
                                    <div class="info-value">{{ $essRequest->approved_date?->format('M d, Y H:i') ?? 'N/A' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Notes</div>
                                    <div class="desc-box">{{ $essRequest->approval_notes ?? 'No notes provided' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- ══════════════════ RIGHT COLUMN (Sidebar) ══════════════════ --}}
        <div class="aj-sidebar">
            <div class="aj-card">
                <div class="aj-card-body">

                    {{-- Actions --}}
                    @if($essRequest->status === 'pending')
                        <div class="sidebar-label">Actions</div>
                        <form method="POST" action="{{ route('hr4.ess_requests.approve', $essRequest->id) }}">
                            @csrf
                            <button type="submit" class="btn-approve">
                                <i class="bi bi-check-circle"></i> Approve Request
                            </button>
                        </form>
                        <button type="button" onclick="openRejectModal()" class="btn-reject">
                            <i class="bi bi-x-circle"></i> Reject Request
                        </button>

                        <div style="margin: 1.5rem 0; border-top: 1px solid var(--c-line);"></div>
                    @endif

                    {{-- Timeline --}}
                    <div class="sidebar-label">Timeline</div>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot-wrap">
                                <div class="timeline-dot dot-blue"></div>
                                @if($essRequest->status !== 'pending')
                                    <div class="timeline-line"></div>
                                @endif
                            </div>
                            <div>
                                <div class="timeline-label">Request Submitted</div>
                                <div class="timeline-date">{{ $essRequest->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>

                        @if($essRequest->status !== 'pending')
                            <div class="timeline-item" style="margin-top:.5rem;">
                                <div class="timeline-dot-wrap">
                                    <div class="timeline-dot {{ $essRequest->status === 'approved' ? 'dot-green' : 'dot-red' }}"></div>
                                </div>
                                <div>
                                    <div class="timeline-label">
                                        {{ $essRequest->status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </div>
                                    <div class="timeline-date">{{ $essRequest->approved_date?->format('M d, Y H:i') ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div style="margin: 1.5rem 0; border-top: 1px solid var(--c-line);"></div>

                    {{-- Additional Info --}}
                    <div class="sidebar-label">Additional Info</div>
                    <div>
                        <div class="meta-row">
                            <span>Last Updated</span>
                            <span>{{ $essRequest->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="meta-row">
                            <span>Request ID</span>
                            <span>#{{ $essRequest->id }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Reject Modal ── --}}
<div id="rejectModal" class="aj-modal-overlay">
    <div class="aj-modal">
        <h2><em>Reject</em> Request</h2>
        <form method="POST" action="{{ route('hr4.ess_requests.reject', $essRequest->id) }}">
            @csrf
            <label>Rejection Reason</label>
            <textarea name="reason" rows="4" placeholder="Enter reason for rejection..." required></textarea>
            <div class="modal-actions">
                <button type="button" onclick="closeRejectModal()" class="btn-modal-cancel">Cancel</button>
                <button type="submit" class="btn-modal-reject">
                    <i class="bi bi-x-circle"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal()  { document.getElementById('rejectModal').classList.add('open'); }
    function closeRejectModal() { document.getElementById('rejectModal').classList.remove('open'); }
</script>

@endsection