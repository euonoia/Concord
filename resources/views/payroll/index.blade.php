@extends('admin.hr4.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Payroll List</h1>
        <div class="flex gap-2">
            <a href="{{ route('hr4.payroll.reports') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition">Reports</a>
            <a href="{{ route('hr4.payroll.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">Add Payroll</a>
        </div>
    </div>
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <div class="overflow-x-auto rounded shadow">
        <table class="min-w-full bg-white border border-slate-200">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Employee</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Salary</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Deductions</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Net Pay</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Pay Date</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                    <tr class="border-b hover:bg-slate-50">
                        <td class="px-4 py-2">{{ $payroll->id }}</td>
                        <td class="px-4 py-2">{{ $payroll->employee->name ?? ($payroll->employee->first_name ?? 'N/A') }}</td>
                        <td class="px-4 py-2">₱{{ number_format($payroll->salary, 2) }}</td>
                        <td class="px-4 py-2">₱{{ number_format($payroll->deductions, 2) }}</td>
                        <td class="px-4 py-2 font-semibold text-green-700">₱{{ number_format($payroll->net_pay, 2) }}</td>
                        <td class="px-4 py-2">{{ $payroll->pay_date }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            @if($payroll->employee)
                                <!-- Employee profile link removed: no route defined -->
                                <a href="{{ route('hr4.direct_compensation.index', ['employee_id' => $payroll->employee->id]) }}" class="text-green-600 hover:underline text-xs">View Compensation</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-slate-400">No payroll records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
