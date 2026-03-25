@extends('layouts.dashboard.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .container { max-width: 1200px; margin: auto; }
    
    /* Card Enhancements */
    .custom-card {
        background: #fff;
        padding: 28px;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s;
    }

    /* Form Styling */
    label { font-weight: 600; color: #4a5568; margin-bottom: 8px; display: block; font-size: 0.9rem; }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 20px;
        transition: border-color 0.2s;
    }
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

    /* Button Styling */
    .btn-submit {
        background: #2563eb;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #1d4ed8; }

    /* Status Badges */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    /* Dynamic Info Boxes */
    .info-box {
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>

<div class="container" style="padding: 40px 20px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; border-left: 5px solid #10b981; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center;">
            <i class="fas fa-check-circle" style="margin-right: 10px;"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1.8fr; gap: 30px;">

        {{-- Left Column: Form --}}
        <div class="custom-card">
            <h3 style="margin-top: 0; margin-bottom: 25px; color: #1e293b; font-weight: 700;">
                <i class="fas fa-paper-plane" style="color: #3b82f6; margin-right: 8px;"></i> New Request
            </h3>

            <form id="requestForm" action="{{ route('user.ess.store') }}" method="POST">
                @csrf

                <label>Request Type</label>
                <select name="type" id="typeSelect" class="form-control" onchange="toggleUI()" required>
                    <option value="Leave">Request Leave</option>
                    <option value="Payroll">Request Payroll</option>
                    <option value="Request Shift">Request Shift</option> 
                    <option value="Claim">Claim / Reimbursement</option>
                </select>

                {{-- Payroll Request UI --}}
              <div id="payrollRequestUI" class="info-box" style="display:none; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <i class="fas fa-info-circle fa-lg"></i>
                    <div>
                        @if($latestPayroll)
                            <strong>Payroll Found</strong><br>
                            <small>₱{{ number_format($latestPayroll->salary, 2) }}</small>
                        @else
                            <strong>No data found.</strong>
                        @endif
                    </div>
                </div>

                {{-- Request Shift UI --}}
                <div id="shiftRequestUI" class="info-box" style="display:none; background: #fdf2f8; border: 1px solid #fbcfe8; color: #9d174d;">
                    <i class="fas fa-calendar-check fa-lg"></i>
                    <div>
                @if($activeShifts->count())                            
                <strong>Active Shift Detected</strong><br>
                            <small>Ready for submission.</small>
                        @else
                            <strong>No active shifts.</strong>
                        @endif
                    </div>
                </div>

                {{-- Leave UI --}}
                <div id="leaveUI" style="display:none;">
                    <label>Shift to Miss</label>
                    <select name="shift_id" class="form-control">
                        @foreach($approvedShifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->day_of_week }} - {{ $shift->shift_name }}</option>
                        @endforeach
                    </select>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label>Start Date</label>
                            <input type="date" name="leave_date" class="form-control">
                        </div>
                        <div>
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Claim UI --}}
                <div id="claimUI" style="display:none;">
                    <label>Category</label>
                    <select name="claim_type" class="form-control">
                        <option value="Travel">🚗 Travel</option>
                        <option value="Training">📚 Training</option>
                        <option value="Medical">🏥 Medical Supplies</option>
                        <option value="Conference">🎤 Conference</option>
                    </select>
                    <label>Amount (PHP)</label>
                    <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00">
                </div>

                <label>Reason / Details</label>
                <textarea name="details" rows="3" class="form-control" required placeholder="Brief explanation..."></textarea>

                <button type="submit" class="btn-submit">
                    Send Request <i class="fas fa-chevron-right" style="font-size: 0.8rem; margin-left: 5px;"></i>
                </button>
            </form>
        </div>

        {{-- Right Column: History --}}
        <div class="custom-card">
            <h3 style="margin-top: 0; margin-bottom: 25px; color: #1e293b; font-weight: 700;">History</h3>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 12px; color: #64748b; font-size: 0.85rem;">DETAILS</th>
                            <th style="padding: 12px; color: #64748b; font-size: 0.85rem;">SCHEDULE/AMOUNT</th>
                            <th style="padding: 12px; color: #64748b; font-size: 0.85rem; text-align: center;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $h)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px 12px;">
                                <span style="font-weight: 600; color: #334155;">{{ $h->type ?? 'Payroll' }}</span><br>
                                <small style="color: #94a3b8;">{{ $h->details ?? 'No details' }}</small>
                            </td>
                            <td style="padding: 15px 12px;">
                                <div style="font-size: 0.9rem;">
                                    @if(isset($h->leave_date)) <span style="display:block"><i class="far fa-calendar-alt"></i> {{ $h->leave_date }}</span> @endif
                                    @if(isset($h->amount) || isset($h->salary)) 
                                        <strong style="color: #059669;">₱{{ number_format($h->amount ?? $h->salary, 2) }}</strong> 
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 15px 12px; text-align: center;">
                                <span class="badge status-{{ strtolower($h->status) }}">
                                    {{ $h->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 40px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-folder-open fa-2x" style="display:block; margin-bottom: 10px;"></i>
                                No requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
function toggleUI() {
    const type = document.getElementById('typeSelect').value;
    const sections = ['leaveUI', 'shiftRequestUI', 'claimUI', 'payrollRequestUI'];
    
    sections.forEach(id => {
        document.getElementById(id).style.display = 'none';
    });

    if (type === 'Leave') document.getElementById('leaveUI').style.display = 'block';
    if (type === 'Request Shift') document.getElementById('shiftRequestUI').style.display = 'flex';
    if (type === 'Claim') document.getElementById('claimUI').style.display = 'block';
    if (type === 'Payroll') document.getElementById('payrollRequestUI').style.display = 'flex';
}
// Run once on load to set initial state
window.onload = toggleUI;
</script>
@endsection