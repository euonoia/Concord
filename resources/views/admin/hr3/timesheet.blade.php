@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 20px;">HR3: Attendance Timesheet</h2>

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left; font-family: sans-serif;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="padding: 10px;">Emp ID</th>
                <th style="padding: 10px;">Full Name</th>
                <th style="padding: 10px;">Department</th>
                <th style="padding: 10px;">Specialization & Position</th> <th style="padding: 10px;">Clock In</th>
                <th style="padding: 10px;">Clock Out</th>
                <th style="padding: 10px;">Status</th>
                <th style="padding: 10px;">Device ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">{{ $log->employee_id }}</td> 
                    <td style="padding: 10px;">
                        <strong>{{ $log->employee->first_name ?? 'N/A' }} {{ $log->employee->last_name ?? '' }}</strong>
                    </td>
                    <td style="padding: 10px;">{{ $log->department->department_name ?? $log->department_id }}</td> 
                    <td style="padding: 10px;">
                        <div style="font-weight: 600; color: #374151;">{{ $log->specialization ?? 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: #6366f1; font-style: italic;">
                            {{ $log->position_title ?? 'No Title' }}
                        </div>
                    </td>
                    <td style="padding: 10px;">{{ $log->clock_in ? $log->clock_in->format('M d, Y h:i A') : '---' }}</td>
                    <td style="padding: 10px;">
                        @if($log->clock_out)
                            {{ $log->clock_out->format('M d, Y h:i A') }}
                        @else
                            <span style="color: #28a745; font-weight: bold;">[ ON DUTY ]</span>
                        @endif
                    </td>
                    <td style="padding: 10px;">
                        <span style="color: {{ $log->status == 'on-time' ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                            {{ strtoupper($log->status) }}
                        </span>
                    </td>
                    <td style="padding: 10px;">
                        <code title="{{ $log->device_fingerprint }}" style="font-size: 11px; color: #666;">
                            {{ Str::limit($log->device_fingerprint, 10) }}
                        </code>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 20px; color: #666;">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection