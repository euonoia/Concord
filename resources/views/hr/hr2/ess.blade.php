@extends('layouts.dashboard.app')

@section('content')
<div class="container" style="padding: 20px;">
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
        
        {{-- Left Column: New Request Form --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; color: #333;">New Request</h3>
            
            <form action="{{ route('user.ess.store') }}" method="POST">
                @csrf
                
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Request Type</label>
                <select name="type" id="typeSelect" class="form-control" onchange="toggleLeaveUI()" required style="width:100%; margin-bottom: 15px; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="Profile Update">Profile Update</option>
                    <option value="Leave">Request Leave</option>
                    <option value="Document Request">Document Request</option>
                </select>

                <div id="leaveUI" style="display: none; border-top: 2px solid #eee; padding-top: 15px; margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: bold;">Select Shift to Miss:</label>
                    <div style="display: grid; grid-template-columns: 1fr; gap: 10px; margin-bottom: 15px; max-height: 200px; overflow-y: auto; padding-right: 5px;">
                        @forelse($allShifts as $shift)
                            @if($shift->is_active) {{-- Only show active shifts --}}
                            <label style="border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; transition: 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                                <input type="radio" name="shift_id" value="{{ $shift->id }}" style="margin-right: 10px;" required>
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

                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Reason / Details</label>
                <textarea name="details" rows="4" class="form-control" style="width:100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ddd;" required placeholder="Type your reason here..."></textarea>

                <button type="submit" style="width: 100%; background: #1B3C53; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    Submit Request
                </button>
            </form>
        </div>

        {{-- Right Column: Request History (Admin-Matched) --}}
        <div class="card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; color: #333;">Request History</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #eee; color: #666; font-size: 0.9em;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Type & Details</th>
                            <th style="padding: 10px;">Schedule/Dates</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $r)
                        @php
                            $badgeStyles = match($r->status) {
                                'approved' => ['bg' => '#d4edda', 'text' => '#155724'],
                                'rejected' => ['bg' => '#f8d7da', 'text' => '#721c24'],
                                'closed'   => ['bg' => '#e9ecef', 'text' => '#6c757d'],
                                default    => ['bg' => '#fff3cd', 'text' => '#856404'], 
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; vertical-align: top;">#{{ $r->id }}</td>
                            <td style="padding: 10px;">
                                <strong>{{ strtoupper($r->type) }}</strong><br>
                                <small style="color: #777;">{{ Str::limit($r->details, 40) }}</small>
                            </td>
                            <td style="padding: 10px; font-size: 0.85em;">
                                @if($r->shift_id)
                                    <div><strong>Shift ID:</strong> {{ $r->shift_id }}</div>
                                @endif
                                @if($r->leave_date)
                                    <div><strong>Start:</strong> {{ \Carbon\Carbon::parse($r->leave_date)->format('M d, Y') }}</div>
                                @endif
                                @if($r->end_date)
                                    <div><strong>End:</strong> {{ \Carbon\Carbon::parse($r->end_date)->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td style="padding: 10px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.75em; font-weight: bold; text-transform: uppercase; background: {{ $badgeStyles['bg'] }}; color: {{ $badgeStyles['text'] }};">
                                    {{ $r->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #999;">You haven't submitted any requests yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLeaveUI() {
    const type = document.getElementById('typeSelect').value;
    const leaveUI = document.getElementById('leaveUI');
    const shiftInputs = document.querySelectorAll('input[name="shift_id"]');
    
    if (type === 'Leave') {
        leaveUI.style.display = 'block';
        shiftInputs.forEach(input => input.required = true);
    } else {
        leaveUI.style.display = 'none';
        shiftInputs.forEach(input => input.required = false);
    }
}
</script>
@endsection