@extends('admin._logistics1.layouts.app')

@section('content')

<style>
    .page-header { margin-bottom: 1.75rem; }
    .page-header h4 { font-size: 1.3rem; font-weight: 700; color: #1e293b; letter-spacing: -0.3px; }
    .page-header p  { font-size: 0.82rem; color: #94a3b8; margin: 0; }

    /* Section cards */
    .section-card { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.04); margin-bottom: 1.25rem; }
    .section-card-header { padding: 0.85rem 1.1rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px; }
    .section-card-header .sc-title { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
    .section-card-header .sc-icon  { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }
    .sc-icon-warning { background: #fef9c3; color: #a16207; }
    .sc-icon-success { background: #dcfce7; color: #16a34a; }
    .sc-icon-info    { background: #dbeafe; color: #1d4ed8; }

    /* Tables */
    .data-table { margin: 0; font-size: 0.82rem; }
    .data-table thead tr { background: #1e293b; }
    .data-table thead th { color: #94a3b8; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; padding: 0.75rem 1rem; border: none; white-space: nowrap; }
    .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.12s; }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table tbody td { padding: 0.75rem 1rem; color: #334155; border: none; vertical-align: middle; }

    .code-pill { background: #f1f5f9; color: #475569; border-radius: 5px; padding: 2px 8px; font-size: 0.73rem; font-family: monospace; font-weight: 600; }
    .row-title { font-weight: 600; color: #1e293b; }
    .row-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    /* Inline repair form */
    .repair-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .repair-form .form-select,
    .repair-form .form-control { border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.8rem; color: #1e293b; padding: 0.35rem 0.65rem; box-shadow: none; }
    .repair-form .form-select:focus,
    .repair-form .form-control:focus { border-color: #94a3b8; box-shadow: none; }
    .repair-form .input-group-text { background: #f8fafc; border: 1px solid #e2e8f0; border-right: none; border-radius: 7px 0 0 7px; font-size: 0.8rem; color: #64748b; }
    .repair-form .cost-input { border-radius: 0 7px 7px 0 !important; border-left: none !important; }

    .btn-release { background: #dcfce7; color: #16a34a; border: none; border-radius: 7px; font-size: 0.78rem; font-weight: 700; padding: 0.38rem 0.9rem; cursor: pointer; white-space: nowrap; transition: background 0.15s; }
    .btn-release:hover { background: #bbf7d0; }

    /* Financial ledger */
    .amount-cell { font-weight: 700; color: #b91c1c; font-size: 0.82rem; }

    /* History timeline */
    .history-list { list-style: none; padding: 0; margin: 0; }
    .history-item { padding: 0.8rem 1.1rem; border-bottom: 1px solid #f1f5f9; display: flex; gap: 10px; align-items: flex-start; }
    .history-item:last-child { border-bottom: none; }
    .history-dot { width: 8px; height: 8px; border-radius: 50%; background: #1e293b; margin-top: 5px; flex-shrink: 0; }
    .history-action { font-size: 0.82rem; font-weight: 700; color: #1e293b; }
    .history-detail { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }
    .history-time   { font-size: 0.72rem; color: #cbd5e1; white-space: nowrap; margin-left: auto; padding-left: 8px; }

    .empty-state { padding: 2.5rem 1rem; text-align: center; color: #94a3b8; }
    .empty-state i { font-size: 1.8rem; display: block; margin-bottom: 0.4rem; opacity: 0.4; }
    .empty-state p { font-size: 0.82rem; margin: 0; }
</style>

<div class="page-header">
    <h4><i class="bi bi-tools me-2"></i>Fleet Maintenance</h4>
    <p>Manage vehicle repairs, financials and maintenance history</p>
</div>

<div class="row g-4">

    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">

        {{-- Pending Maintenance --}}
        <div class="section-card">
            <div class="section-card-header">
                <div class="sc-icon sc-icon-warning"><i class="bi bi-wrench"></i></div>
                <span class="sc-title">Pending Maintenance</span>
            </div>
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Repair Details</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenanceFleet as $v)
                    <tr>
                        <td>
                            <div class="row-title">{{ $v->plate_number }}</div>
                            <div class="row-sub">{{ $v->vehicle_type ?? '—' }}</div>
                        </td>
                        <td>
                            <form id="form-{{ $v->id }}" action="{{ route('admin.logistics1.maintenance.repair') }}" method="POST">
                                @csrf
                                <input type="hidden" name="vehicle_id" value="{{ $v->id }}">
                                <div class="repair-form">
                                    <select name="repair_type" class="form-select" style="width:160px;" required>
                                        <option value="" disabled selected>Select Repair...</option>
                                        <option value="Oil Change">Oil Change</option>
                                        <option value="Engine Repair">Engine Repair</option>
                                        <option value="Tire Swap">Tire Swap</option>
                                        <option value="Electrical">Electrical</option>
                                    </select>
                                    <div class="input-group" style="width:130px;">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" name="cost" class="form-control cost-input" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td style="white-space:nowrap;">
                            <button type="submit" form="form-{{ $v->id }}" class="btn-release">
                                <i class="bi bi-check-lg me-1"></i> Release Vehicle
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state">
                                <i class="bi bi-check-circle"></i>
                                <p>All vehicles are operational.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Financial Ledger --}}
        <div class="section-card">
            <div class="section-card-header">
                <div class="sc-icon sc-icon-success"><i class="bi bi-cash-stack"></i></div>
                <span class="sc-title">Recent Financial Impact</span>
            </div>
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plate</th>
                        <th>Repair Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financials as $fin)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fin->transaction_date)->format('M d, Y') }}</td>
                        <td><span class="code-pill">{{ $fin->vehicle_plate }}</span></td>
                        <td>{{ $fin->repair_type }}</td>
                        <td><span class="amount-cell">−₱{{ number_format($fin->amount, 2) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="bi bi-receipt"></i>
                                <p>No financial records yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- RIGHT COLUMN — Maintenance History --}}
    <div class="col-lg-4">
        <div class="section-card" style="height:fit-content;">
            <div class="section-card-header">
                <div class="sc-icon sc-icon-info"><i class="bi bi-clock-history"></i></div>
                <span class="sc-title">Maintenance History</span>
            </div>
            <ul class="history-list">
                @forelse($repairLogs as $log)
                <li class="history-item">
                    <div class="history-dot"></div>
                    <div style="flex:1; min-width:0;">
                        <div class="history-action">{{ $log->action }}</div>
                        <div class="history-detail">{{ $log->details }}</div>
                    </div>
                    <div class="history-time">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                </li>
                @empty
                <li class="history-item">
                    <div class="empty-state w-100">
                        <i class="bi bi-clock-history"></i>
                        <p>No maintenance history yet.</p>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>
    </div>

</div>

@endsection