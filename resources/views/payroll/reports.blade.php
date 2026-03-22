@extends('admin.hr4.layouts.app')

@section('title', 'Payroll Reports - HR4')

@section('content')
<div style="padding: 20px; font-family: Arial, sans-serif; background: #f7fafc; min-height: 100vh;">
    <h1 style="color: #1c3d5a; margin-bottom: 1rem;">Payroll Reports</h1>

    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 1rem;">
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
            <div style="background:#edf2f7; padding:12px; border-radius:6px;">Total Employees: <strong>{{ $employeeCount ?? 0 }}</strong></div>
            <div style="background:#edf2f7; padding:12px; border-radius:6px;">Total Salary: <strong>₱{{ number_format($totalSalary ?? 0, 2) }}</strong></div>
            <div style="background:#edf2f7; padding:12px; border-radius:6px;">Total Deductions: <strong>₱{{ number_format($totalDeductions ?? 0, 2) }}</strong></div>
            <div style="background:#edf2f7; padding:12px; border-radius:6px;">Total Net Pay: <strong>₱{{ number_format($totalNetPay ?? 0, 2) }}</strong></div>
        </div>
    </div>

    <h2 style="color:#1c3d5a; margin-bottom: 0.5rem;">Department Breakdown</h2>
    <table style="width:100%; border-collapse: collapse; background:#fff; border:1px solid #e2e8f0; margin-bottom:1rem;">
        <thead>
            <tr style="background:#e2e8f0; color:#1a365d;">
                <th style="padding:8px; border:1px solid #cbd5e0;">Department</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Employees</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Total Salary</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Total Deductions</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Total Net Pay</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departmentBreakdown ?? collect() as $dept)
                <tr>
                    <td style="padding:8px; border:1px solid #cbd5e0;">{{ $dept->department ?? 'N/A' }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">{{ $dept->employees ?? 0 }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($dept->total_salary ?? 0, 2) }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($dept->total_deductions ?? 0, 2) }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($dept->total_net_pay ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:8px; border:1px solid #cbd5e0; text-align:center;">No payroll data available for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2 style="color:#1c3d5a; margin-bottom: 0.5rem;">Recent Payroll Entries</h2>
    <table style="width:100%; border-collapse: collapse; background:#fff; border:1px solid #e2e8f0;">
        <thead>
            <tr style="background:#e2e8f0; color:#1a365d;">
                <th style="padding:8px; border:1px solid #cbd5e0;">Employee</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Salary</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Deductions</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Net Pay</th>
                <th style="padding:8px; border:1px solid #cbd5e0;">Pay Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls->take(20) ?? collect() as $payroll)
                <tr>
                    <td style="padding:8px; border:1px solid #cbd5e0;">{{ optional($payroll->employee)->full_name ?? 'N/A' }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($payroll->salary ?? 0, 2) }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($payroll->deductions ?? 0, 2) }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">₱{{ number_format($payroll->net_pay ?? 0, 2) }}</td>
                    <td style="padding:8px; border:1px solid #cbd5e0;">{{ optional($payroll->pay_date)->format('Y-m-d') ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:8px; border:1px solid #cbd5e0; text-align:center;">No recent payroll records.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
