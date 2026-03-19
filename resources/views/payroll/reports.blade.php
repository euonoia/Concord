@extends('admin.hr4.layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Payroll Reports</h1>
        <a href="{{ route('hr4.payroll.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow transition">Back to Payroll List</a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Filters</h2>
        <form method="GET" action="{{ route('hr4.payroll.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select name="employee_id" id="employee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">Filter</button>
                <a href="{{ route('hr4.payroll.reports') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow transition">Clear</a>
            </div>
        </form>
    </div>

    <!-- Summary -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">₱{{ number_format($totalSalary, 2) }}</p>
                <p class="text-sm text-gray-600">Total Salary</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">₱{{ number_format($totalDeductions, 2) }}</p>
                <p class="text-sm text-gray-600">Total Deductions</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($totalNetPay, 2) }}</p>
                <p class="text-sm text-gray-600">Total Net Pay</p>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Payroll Records</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Employee</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Salary</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Deductions</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Net Pay</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Pay Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="px-4 py-2">{{ $payroll->id }}</td>
                            <td class="px-4 py-2">{{ $payroll->employee->first_name ?? 'N/A' }} {{ $payroll->employee->last_name ?? '' }}</td>
                            <td class="px-4 py-2">₱{{ number_format($payroll->salary, 2) }}</td>
                            <td class="px-4 py-2">₱{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="px-4 py-2 font-semibold text-green-700">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="px-4 py-2">{{ $payroll->pay_date->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">No payroll records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection