@extends('admin.hr3.layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Employee Attendance Details</h1>

        <div class="mb-6 p-4 bg-gray-100 rounded">
            <h2 class="text-lg font-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
            <p><strong>Department:</strong> {{ $employee->department->department_name ?? 'N/A' }}</p>
            <p><strong>Position:</strong> {{ $employee->position->title ?? 'N/A' }}</p>
            <p><strong>Total Days Worked:</strong> {{ $totalDays }}</p>
            <p><strong>Total Hours Worked:</strong> {{ $totalHours }}</p>
        </div>

        <div class="mb-4">
            <a href="{{ route('timesheet.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Back to Timesheet</a>
        </div>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">Date</th>
                    <th class="py-2 px-4 border">Clock In</th>
                    <th class="py-2 px-4 border">Clock Out</th>
                    <th class="py-2 px-4 border">Hours</th>
                    <th class="py-2 px-4 border">Status</th>
                    <th class="py-2 px-4 border">Device</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t">
                        <td class="py-2 px-4 border">{{ $log->clock_in ? $log->clock_in->format('Y-m-d') : 'N/A' }}</td>
                        <td class="py-2 px-4 border">{{ $log->clock_in ? $log->clock_in->format('H:i') : '---' }}</td>
                        <td class="py-2 px-4 border">
                            @if($log->clock_out)
                                {{ $log->clock_out->format('H:i') }}
                            @else
                                <span class="text-green-600 font-bold">[ON DUTY]</span>
                            @endif
                        </td>
                        <td class="py-2 px-4 border">
                            @if($log->clock_in && $log->clock_out)
                                {{ $log->clock_in->diffInHours($log->clock_out) }}h
                            @else
                                ---
                            @endif
                        </td>
                        <td class="py-2 px-4 border">
                            <span class="font-bold {{ $log->status == 'on-time' ? 'text-green-600' : 'text-red-600' }}">
                                {{ strtoupper($log->status) }}
                            </span>
                        </td>
                        <td class="py-2 px-4 border">
                            <code class="text-xs">{{ Str::limit($log->device_fingerprint, 15) }}</code>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection