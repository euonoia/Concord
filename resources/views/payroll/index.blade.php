@extends('admin.hr4.layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:          #eef3f7;
        --c-surface:     #ffffff;
        --c-border:      #d4e3ee;
        --c-teal:        #0a7c6e;
        --c-teal-light:  #e4f4f1;
        --c-teal-mid:    #b8e0da;
        --c-blue:        #1a5f8a;
        --c-blue-light:  #e8f2f9;
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-green-mid:   #b2ddc8;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
        --shadow-hover:  0 10px 36px rgba(10,124,110,.13);
    }

    .pr * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .pr {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .pr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .pr-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        line-height: 1.1;
    }

    .pr-header h1 em { color: var(--c-teal); font-style: italic; }

    .pr-actions { display: flex; gap: .6rem; flex-wrap: wrap; }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .83rem;
        font-weight: 600;
        padding: .55rem 1.15rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .btn:hover { transform: translateY(-2px); text-decoration: none; }

    .btn-teal {
        background: var(--c-teal);
        color: #fff;
        box-shadow: 0 2px 8px rgba(10,124,110,.25);
    }
    .btn-teal:hover { background: #0b9483; box-shadow: 0 4px 14px rgba(10,124,110,.35); color:#fff; }

    .btn-outline {
        background: var(--c-surface);
        color: var(--c-teal);
        border: 1.5px solid var(--c-teal-mid);
    }
    .btn-outline:hover { background: var(--c-teal-light); color: var(--c-teal); }

    /* ── Alert ── */
    .pr-alert {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .9rem 1.2rem;
        background: var(--c-green-light);
        border: 1px solid var(--c-green-mid);
        border-left: 4px solid var(--c-teal);
        border-radius: 10px;
        color: var(--c-teal);
        font-size: .88rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        animation: fadeUp .4s ease both;
    }

    /* ── Card ── */
    .pr-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        animation: fadeUp .5s .1s ease both;
    }

    /* ── Table ── */
    .pr-table { width: 100%; border-collapse: collapse; font-size: .83rem; }

    .pr-table thead th {
        background: var(--c-bg);
        color: var(--c-muted);
        font-size: .71rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding: .8rem 1.1rem;
        text-align: left;
        border-bottom: 1.5px solid var(--c-line);
        white-space: nowrap;
    }

    .pr-table tbody tr {
        border-bottom: 1px solid var(--c-line);
        transition: background .18s ease;
    }

    .pr-table tbody tr:hover { background: var(--c-teal-light); }
    .pr-table tbody tr:last-child { border-bottom: none; }

    .pr-table tbody td {
        padding: .85rem 1.1rem;
        color: var(--c-text);
        vertical-align: middle;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .25rem .7rem;
        border-radius: 99px;
        font-size: .71rem;
        font-weight: 600;
        letter-spacing: .02em;
    }

    .badge-blue  { background: var(--c-blue-light);  color: var(--c-blue); }
    .badge-green { background: var(--c-green-light); color: var(--c-green); }

    /* ── Net pay highlight ── */
    .net-pay {
        font-family: 'Instrument Serif', serif;
        font-size: 1rem;
        color: var(--c-green);
        font-weight: 400;
    }

    /* ── Action link ── */
    .action-link {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        padding: .3rem .65rem;
        border-radius: 6px;
        background: var(--c-teal-light);
        color: var(--c-teal);
        border: 1px solid var(--c-teal-mid);
        transition: background .18s ease, filter .18s ease;
    }

    .action-link:hover { filter: brightness(.92); text-decoration: none; }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 3.5rem 1rem;
        color: var(--c-muted);
    }

    .empty-state i {
        font-size: 2.8rem;
        display: block;
        margin-bottom: .75rem;
        opacity: .25;
        color: var(--c-teal);
    }

    .empty-state h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--c-text);
        margin: 0 0 .3rem;
    }

    .empty-state p { font-size: .83rem; margin: 0; }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="pr">

    {{-- Header --}}
    <div class="pr-header">
        <h1><em>Payroll</em> List</h1>
        <div class="pr-actions">
            <a href="{{ route('hr4.payroll.reports') }}" class="btn btn-outline">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="pr-alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(isset($budgetRequests) && $budgetRequests->count())
    <div class="pr-card" style="margin-bottom:1rem;">
        <div style="padding:.85rem 1rem; font-size:.9rem; background:#f3f6f9; border:1px solid #d4e3ee; border-radius:10px;">
            <strong>Latest Budget Allocation Requests</strong>
        </div>
        <div style="overflow-x:auto;">
            <table class="pr-table" style="margin:0;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetRequests as $request)
                    <tr>
                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $request->month }}</td>
                        <td>₱{{ number_format($request->total_compensation, 2) }}</td>
                        <td>{{ ucfirst($request->status) }}</td>
                        <td>{{ optional($request->user)->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="pr-card">
        <div style="overflow-x:auto">
            <table class="pr-table">
                <thead>

            <a href="{{ route('hr4.payroll.create') }}" class="btn btn-teal">
                <i class="bi bi-plus-circle"></i> Add Payroll
            </a>
            <form method="POST" action="{{ route('hr4.payroll.request_budget_allocation') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline" title="Request budget allocation from finance">
                    <i class="bi bi-wallet2"></i> Request Budget Allocation
                </button>
            </form>

    {{-- Table card --}}
    <div class="pr-card">
        <div style="overflow-x:auto">
            <table class="pr-table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee</th>
                        <th>Salary</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Pay Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr>
                        <td>
                            <span class="badge badge-blue">{{ $payroll->employee->employee_id }}</span>
                        </td>
                        <td>
                            <strong>{{ $payroll->employee->name ?? ($payroll->employee->first_name ?? 'N/A') }}</strong>
                        </td>
                        <td>₱{{ number_format($payroll->salary, 2) }}</td>
                        <td>
                            <span style="color:#be123c">−₱{{ number_format($payroll->deductions, 2) }}</span>
                        </td>
                        <td>
                            <span class="net-pay">₱{{ number_format($payroll->net_pay, 2) }}</span>
                        </td>
                        <td>{{ $payroll->pay_date }}</td>
                        <td>
                            @if($payroll->employee)
                                <a href="{{ route('hr4.direct_compensation.index', ['employee_id' => $payroll->employee->id]) }}"
                                   class="action-link">
                                    <i class="bi bi-wallet2"></i> View Compensation
                                </a>
                            @else
                                <span style="color:var(--c-muted); font-size:.78rem">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-receipt"></i>
                                <h4>No payroll records found</h4>
                                <p>Start by adding a new payroll entry.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection