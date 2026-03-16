@extends('admin._logistics1.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800"><i class="bi bi-tools"></i> Maintenance & Repairs</h2>
        <span class="badge bg-dark px-3 py-2">
            Total Maintenance Logs: {{ count($repairLogs) }}
        </span>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vehicles Awaiting Repair</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Plate / Type</th>
                                    <th>Status</th>
                                    <th>Action & Cost Assignment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maintenanceFleet as $v)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold">{{ $v->plate_number }}</div>
                                        <small class="text-muted">{{ $v->vehicle_type }}</small>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-danger" style="font-size: 0.75rem;">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Maintenance
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.logistics1.maintenance.repair') }}" method="POST" class="d-flex gap-2 align-items-center">
                                            @csrf
                                            <input type="hidden" name="vehicle_id" value="{{ $v->id }}">
                                            
                                            <select name="repair_type" class="form-select form-select-sm" required style="width: 160px;">
                                                <option value="" disabled selected>Select Repair...</option>
                                                <option value="Engine Service">Engine Service</option>
                                                <option value="Tire Replacement">Tire Replacement</option>
                                                <option value="Brake Check">Brake Check</option>
                                                <option value="General Cleaning">General Cleaning</option>
                                                <option value="Body Repair">Body Repair</option>
                                            </select>

                                            <div class="input-group input-group-sm" style="width: 130px;">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="cost" class="form-control" placeholder="0.00" step="0.01" min="0">
                                            </div>

                                            <button type="submit" class="btn btn-sm btn-success px-3">
                                                <i class="bi bi-check-lg"></i> Complete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-check2-circle h1 d-block"></i>
                                        No vehicles currently require maintenance.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Recent Audit Logs</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($repairLogs as $log)
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-light text-primary border">{{ $log->action }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                                </small>
                            </div>
                            <div class="small text-dark mb-1">{{ $log->details }}</div>
                            <div class="fw-bold text-success" style="font-size: 0.85rem;">
                                Cost: ${{ number_format($log->cost, 2) }}
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4 text-muted small">
                            No repair history found.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection