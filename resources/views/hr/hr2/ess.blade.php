@extends('layouts.dashboard.app')

@section('content')
<div class="container" style="padding: 20px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

        {{-- Left Column: New Request / Claim Form --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; color: #333;">New Request / Claim</h3>

            <form id="requestForm" action="{{ route('user.ess.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Request Type Selector --}}
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Request Type</label>
                <select name="type" id="typeSelect" class="form-control" onchange="toggleUI()" required
                        style="width:100%; margin-bottom: 15px; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="Profile Update">Profile Update</option>
                    <option value="Leave">Request Leave</option>
                    <option value="Document Request">Document Request</option>
                    <option value="Claim">Claim / Reimbursement</option>
                </select>

                {{-- Leave UI --}}
                <div id="leaveUI" style="display: none; border-top: 2px solid #eee; padding-top: 15px; margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: bold;">Select Shift to Miss:</label>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 10px; max-height: 200px; overflow-y: auto; padding-right: 5px; margin-bottom: 15px;">
                        @forelse($allShifts as $shift)
                            @if($shift->is_active)
                            <label style="border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center;">
                                <input type="radio" name="shift_id" value="{{ $shift->id }}" style="margin-right: 10px;">
                                <div>
                                    <strong>{{ $shift->day_of_week }}</strong> - <span style="color: #007bff;">{{ $shift->shift_name }}</span><br>
                                    <small style="color: #666;">Time: {{ $shift->start_time }} - {{ $shift->end_time }}</small>
                                </div>
                            </label>
                            @endif
                        @empty
                            <p style="font-size: 0.85em; color: #999; text-align: center;">No active shifts assigned.</p>
                        @endforelse
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-weight: bold;">Start Date</label>
                            <input type="date" name="leave_date" class="form-control" style="width:100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                        <div>
                            <label style="font-weight: bold;">End Date</label>
                            <input type="date" name="end_date" class="form-control" style="width:100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    </div>
                </div>

                {{-- Claim UI --}}
                <div id="claimUI" style="display: none; border-top: 2px solid #eee; padding-top: 15px; margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block; margin-bottom: 5px;">Claim Type</label>
                    <select name="claim_type" class="form-control" style="width:100%; margin-bottom: 15px; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                        <option value="Travel">Travel</option>
                        <option value="Training">Training</option>
                        <option value="Medical Supplies">Medical Supplies</option>
                        <option value="Certification Fees">Certification Fees</option>
                        <option value="Conference">Conference</option>
                        <option value="Equipment Purchase">Equipment Purchase</option>
                    </select>

                    <label style="font-weight: bold; display:block; margin-bottom: 5px;">Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control" style="width:100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 15px;">

                    <label style="font-weight: bold; display:block; margin-bottom: 5px;">Upload Receipt (PNG/JPG)</label>
                    <input type="file" name="receipt" accept="image/png,image/jpeg" class="form-control" style="margin-bottom: 15px;">
                </div>

                {{-- Reason / Details --}}
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Reason / Details</label>
                <textarea name="details" rows="4" class="form-control" style="width:100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ddd;" required placeholder="Type your reason here..."></textarea>

                <button type="submit" style="width: 100%; background: #1B3C53; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    Submit Request
                </button>
            </form>
        </div>

        {{-- Right Column: Unified History --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; color: #333;">Request & Claim History</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #eee; color: #666; font-size: 0.9em;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Type & Details</th>
                            <th style="padding: 10px;">Amount / Schedule</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $h)
                        @php
                            $badgeStyles = match($h->status) {
                                'approved' => ['bg' => '#d4edda', 'text' => '#155724'],
                                'rejected' => ['bg' => '#f8d7da', 'text' => '#721c24'],
                                'closed'   => ['bg' => '#e9ecef', 'text' => '#6c757d'],
                                default    => ['bg' => '#fff3cd', 'text' => '#856404'],
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; vertical-align: top;">#{{ $h->ess_id ?? $h->claim_id }}</td>
                            <td style="padding: 10px;">
                                <strong>{{ strtoupper($h->type) }}</strong><br>
                                <small style="color: #777;">{{ Str::limit($h->details, 40) }}</small>
                                @if(isset($h->claim_type))
                                    <div style="margin-top: 5px; font-size: 0.85em; color: #555;">Claim Type: {{ $h->claim_type }}</div>
                                @endif
                            </td>
                            <td style="padding: 10px; font-size: 0.85em;">
                                @if(isset($h->amount))
                                    <div><strong>Amount:</strong> ₱{{ number_format($h->amount,2) }}</div>
                                @endif
                                @if(isset($h->shift_id))
                                    <div><strong>Shift ID:</strong> {{ $h->shift_id }}</div>
                                @endif
                                @if(isset($h->leave_date))
                                    <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($h->leave_date)->format('M d, Y') }}</div>
                                @endif
                                @if(isset($h->end_date))
                                    <div><strong>End:</strong> {{ \Carbon\Carbon::parse($h->end_date)->format('M d, Y') }}</div>
                                @endif
                                @if(isset($h->receipt_path))
                                    <div><a href="{{ asset('storage/'.$h->receipt_path) }}" target="_blank" style="color:#007bff;">View Receipt</a></div>
                                @endif
                            </td>
                            <td style="padding: 10px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.75em; font-weight: bold; text-transform: uppercase; background: {{ $badgeStyles['bg'] }}; color: {{ $badgeStyles['text'] }};">
                                    {{ $h->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #999;">No requests or claims yet.</td>
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
    const leaveUI = document.getElementById('leaveUI');
    const claimUI = document.getElementById('claimUI');
    const shiftInputs = document.querySelectorAll('input[name="shift_id"]');
    const form = document.getElementById('requestForm');

    if (type === 'Leave') {
        leaveUI.style.display = 'block';
        claimUI.style.display = 'none';
        shiftInputs.forEach(input => input.required = true);
        form.action = "{{ route('user.ess.store') }}";
    } else if (type === 'Claim') {
        leaveUI.style.display = 'none';
        claimUI.style.display = 'block';
        shiftInputs.forEach(input => input.required = false);
        form.action = "{{ route('user.claims.store') }}";
    } else {
        leaveUI.style.display = 'none';
        claimUI.style.display = 'none';
        shiftInputs.forEach(input => input.required = false);
        form.action = "{{ route('user.ess.store') }}";
    }
}
</script>
@endsection