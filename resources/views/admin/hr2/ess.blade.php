@extends('admin.layouts.app')

@section('title', 'ESS Requests - HR2 Admin')

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Employee Self-Service Requests</h2>
        <span class="badge" style="padding: 5px 10px; background: #eee; border-radius: 4px;">Total: {{ $requests->count() }}</span>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left; font-family: sans-serif;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Employee</th>
                <th style="padding: 12px;">Request Details</th>
                <th style="padding: 12px;">Schedule/Dates</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Submitted</th>
                <th style="padding: 12px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
                @php
                    $statusColor = match($r->status) {
                        'approved' => '#28a745',
                        'rejected' => '#dc3545',
                        'closed'   => '#6c757d',
                        default    => '#ff8c00', 
                    };
                    
                    $isFinalized = in_array($r->status, ['approved', 'rejected', 'closed']);
                    $isClosed = ($r->status == 'closed');
                @endphp
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px;">#{{ $r->id }}</td>
                    <td style="padding: 12px;">
                        <strong>{{ $r->employee->first_name ?? 'N/A' }} {{ $r->employee->last_name ?? '' }}</strong><br>
                        <small style="color: #666;">ID: {{ $r->employee_id }}</small>
                    </td>
                    <td style="padding: 12px;">
                        <span style="background: #e9ecef; padding: 2px 6px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">
                            {{ strtoupper($r->type) }}
                        </span><br>
                        <p style="margin: 5px 0 0 0; font-size: 0.9em; color: #333;">{{ $r->details }}</p>
                    </td>
                    <td style="padding: 12px; font-size: 0.9em;">
                        @if($r->shift_id)
                            <div><strong>Shift ID:</strong> {{ $r->shift_id }}</div>
                        @endif
                        
                        @if($r->leave_date)
                            <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($r->leave_date)->format('M d, Y') }}</div>
                        @endif

                        @if($r->end_date)
                            <div><strong>End:</strong> {{ \Carbon\Carbon::parse($r->end_date)->format('M d, Y') }}</div>
                        @endif

                        @if(!$r->shift_id && !$r->leave_date)
                            <span style="color: #999;">N/A</span>
                        @endif
                    </td>
                    <td style="padding: 12px;">
                        <strong style="color: {{ $statusColor }}; text-transform: capitalize;">
                            {{ $r->status }}
                        </strong>
                    </td>
                    <td style="padding: 12px; white-space: nowrap;">
                        {{ \Carbon\Carbon::parse($r->created_at)->format('M d, Y') }}<br>
                        <small style="color: #999;">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</small>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <form method="POST" action="{{ route('ess.updateStatus', $r->id) }}" onsubmit="return confirm('Update status to ' + event.submitter.value + '?');">
                            @csrf
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <button type="submit" name="status" value="approved" 
                                    style="background: none; border: 1px solid #28a745; color: #28a745; padding: 5px 8px; border-radius: 4px; cursor: {{ $isFinalized ? 'not-allowed' : 'pointer' }}" 
                                    {{ $isFinalized ? 'disabled' : '' }}>
                                    Approve
                                </button>

                                <button type="submit" name="status" value="rejected" 
                                    style="background: none; border: 1px solid #dc3545; color: #dc3545; padding: 5px 8px; border-radius: 4px; cursor: {{ $isFinalized ? 'not-allowed' : 'pointer' }}" 
                                    {{ $isFinalized ? 'disabled' : '' }}>
                                    Reject
                                </button>

                                <button type="submit" name="status" value="closed" 
                                    style="background: none; border: 1px solid #6c757d; color: #6c757d; padding: 5px 8px; border-radius: 4px; cursor: {{ $isClosed ? 'not-allowed' : 'pointer' }}" 
                                    {{ $isClosed ? 'disabled' : '' }}>
                                    Close
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 30px; color: #999;">No employee requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection