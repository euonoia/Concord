@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 20px;">HR3: Attendance Timesheet</h2>

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #ececec;">
            <tr>
                <th>Emp ID</th>
                <th>Full Name</th>
                <th>Department</th>
                <th>Specialization</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Status</th>
                <th>Device ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->employee_id }}</td> 
                    <td>
                        <strong>{{ $log->employee->first_name ?? 'N/A' }} {{ $log->employee->last_name ?? '' }}</strong>
                    </td>
                    <td>{{ $log->department_id }}</td> 
                    <td><small>{{ $log->employee->specialization ?? 'N/A' }}</small></td>
                    <td>{{ $log->clock_in ? $log->clock_in->format('M d, Y h:i A') : '---' }}</td>
                    <td>
                        @if($log->clock_out)
                            {{ $log->clock_out->format('M d, Y h:i A') }}
                        @else
                            <span style="color: #28a745; font-weight: bold;">[ ON DUTY ]</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: {{ $log->status == 'on-time' ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                            {{ strtoupper($log->status) }}
                        </span>
                    </td>
                    <td>
                        {{-- Truncated for UI cleanlines, hover to see full ID --}}
                        <code title="{{ $log->device_fingerprint }}" style="font-size: 11px; color: #666;">
                            {{ Str::limit($log->device_fingerprint, 10) }}
                        </code>
                    </td>
                </tr>
            @empty
                <tr>
                    {{-- Updated colspan to 8 to match the header --}}
                    <td colspan="8" style="text-align:center; padding: 20px;">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection