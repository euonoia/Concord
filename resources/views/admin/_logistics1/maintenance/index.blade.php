@extends('admin._logistics1.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800"><i class="bi bi-tools"></i> Fleet Maintenance</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Maintenance</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Vehicle Info</th>
                                <th>Assignment</th>
                                <th class="text-end pe-3">Action</th>
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
                                    <form id="form-{{ $v->id }}" action="{{ route('admin.logistics1.maintenance.repair') }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="vehicle_id" value="{{ $v->id }}">
                                        <select name="repair_type" class="form-select form-select-sm" required style="width: 160px;">
                                            <option value="" disabled selected>Select Repair...</option>
                                            <option value="Oil Change">Oil Change</option>
                                            <option value="Engine Repair">Engine Repair</option>
                                            <option value="Tire Swap">Tire Swap</option>
                                            <option value="Electrical">Electrical</option>
                                        </select>
                                        <div class="input-group input-group-sm" style="width: 130px;">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="cost" class="form-control" placeholder="0.00" step="0.01">
                                        </div>
                                    </form>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="submit" form="form-{{ $v->id }}" class="btn btn-sm btn-success">
                                        Release Vehicle
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-5 text-muted">All vehicles are operational.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">Recent Financial Impact</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0 small">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3">Date</th>
                                <th>Plate</th>
                                <th>Repair</th>
                                <th class="text-end pe-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financials as $fin)
                            <tr>
                                <td class="ps-3 text-muted">{{ $fin->transaction_date }}</td>
                                <td>{{ $fin->vehicle_plate }}</td>
                                <td>{{ $fin->repair_type }}</td>
                                <td class="text-end pe-3 fw-bold text-danger">-${{ number_format($fin->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 font-weight-bold">Maintenance History</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        @foreach($repairLogs as $log)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">{{ $log->action }}</span>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted">{{ $log->details }}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection