@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800">Logistics Audit Trail</h2>
        <div>
            <span class="badge bg-success p-2">Total Maint. Cost: ${{ number_format($totalExpenses, 2) }}</span>
            <span class="badge bg-primary p-2">Total Repairs: {{ $repairCount }}</span>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">System-wide Activity Logs</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Timestamp</th>
                            <th>Vehicle (Plate)</th>
                            <th>Category</th>
                            <th>Action Taken</th>
                            <th>Details</th>
                            <th>Performed By</th>
                            <th class="text-end pe-4">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}</small><br>
                                <strong>{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $log->plate_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $log->category == 'Maintenance' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $log->category }}
                                </span>
                            </td>
                            <td><span class="fw-bold">{{ $log->action }}</span></td>
                            <td><small class="text-muted">{{ $log->details }}</small></td>
                            <td>{{ $log->performed_by }}</td>
                            <td class="text-end pe-4">
                                <span class="fw-bold text-success">${{ number_format($log->cost, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No audit logs available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection