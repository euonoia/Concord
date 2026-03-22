@extends('layouts.dashboard.app')

@section('content')
<div class="container" style="padding: 20px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">

        {{-- Left Column --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px;">
            <h3 style="margin-bottom: 20px;">New Request / Claim</h3>

            <form id="requestForm" action="{{ route('user.ess.store') }}" method="POST">
                @csrf

                <label>Request Type</label>
                <select name="type" id="typeSelect" class="form-control" onchange="toggleUI()" required style="margin-bottom:15px;">
                    <option value="Leave">Request Leave</option>
                    <option value="Payroll">Request Payroll</option>
                    <option value="Request Shift">Request Shift</option> 
                    <option value="Claim">Claim / Reimbursement</option>
                </select>

                {{-- Payroll Request UI --}}
                <div id="payrollRequestUI" style="display:none; margin-bottom:15px; padding:10px; border:1px dashed #28a745; border-radius:8px;">
                    @if(isset($employee->salary))
                        <strong>Found your payroll!</strong><br>
                        <small>Salary: ₱{{ number_format($employee->salary,2) }}</small><br>
                        <small>Click submit to request payroll.</small>
                    @else
                        <strong>No payroll data found.</strong>
                    @endif
                </div>

                {{-- Request Shift UI --}}
                <div id="shiftRequestUI" style="display:none; margin-bottom:15px; padding:10px; border:1px dashed #007bff; border-radius:8px;">
                    @if($allShifts->count())
                        @php $shift = $allShifts->first(); @endphp
                        <strong>Found your shift!</strong><br>
                        <small>Click submit to request this shift.</small>
                    @else
                        <strong>No active shift found.</strong>
                    @endif
                </div>

                {{-- Leave UI --}}
                <div id="leaveUI" style="display:none;">
                    <label>Select Shift to Miss:</label>
                    <select name="shift_id" class="form-control" style="margin-bottom:10px;">
                        @foreach($allShifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->day_of_week }} - {{ $shift->shift_name }}</option>
                        @endforeach
                    </select>
                    <label>Start Date</label>
                    <input type="date" name="leave_date" class="form-control">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>

                {{-- Claim UI --}}
                <div id="claimUI" style="display:none;">
                    <label>Claim Type</label>
                    <select name="claim_type" class="form-control" style="margin-bottom:10px;">
                        <option value="Travel">Travel</option>
                        <option value="Training">Training</option>
                        <option value="Medical Supplies">Medical Supplies</option>
                        <option value="Certification Fees">Certification Fees</option>
                        <option value="Conference">Conference</option>
                        <option value="Equipment Purchase">Equipment Purchase</option>
                    </select>
                    <label>Amount</label>
                    <input type="number" name="amount" step="0.01" class="form-control">
                </div>

                {{-- Reason / Details --}}
                <label>Reason / Details</label>
                <textarea name="details" rows="4" class="form-control" required></textarea>

                <button type="submit" class="btn btn-primary" style="margin-top:10px;">Submit Request</button>
            </form>
        </div>

        {{-- Right Column: History --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px;">
            <h3>Request & Claim History</h3>
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Type & Details</th><th>Amount / Schedule</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($history as $h)
                    <tr>
                        <td>{{ $h->ess_id ?? $h->claim_id ?? $h->id }}</td>
                        <td>{{ $h->type ?? 'Payroll' }}<br>{{ $h->details ?? '' }}</td>
                        <td>
                            @if(isset($h->shift_id)) Shift ID: {{ $h->shift_id }} @endif
                            @if(isset($h->leave_date)) Start: {{ $h->leave_date }} @endif
                            @if(isset($h->end_date)) End: {{ $h->end_date }} @endif
                            @if(isset($h->amount)) ₱{{ number_format($h->amount,2) }} @endif
                            @if(isset($h->salary)) ₱{{ number_format($h->salary,2) }} @endif
                        </td>
                        <td>{{ $h->status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4">No requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function toggleUI() {
    const type = document.getElementById('typeSelect').value;

    document.getElementById('leaveUI').style.display = type === 'Leave' ? 'block':'none';
    document.getElementById('shiftRequestUI').style.display = type === 'Request Shift' ? 'block':'none';
    document.getElementById('claimUI').style.display = type === 'Claim' ? 'block':'none';
    document.getElementById('payrollRequestUI').style.display = type === 'Payroll' ? 'block':'none';
}
</script>
@endsection