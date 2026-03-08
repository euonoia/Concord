@extends('admin.hr4.layouts.app')

@section('title', 'Direct Compensation - HR4 Admin')

@section('content')
<div class="container px-4 py-6">
    <h2 class="text-2xl font-semibold mb-4">Direct & Indirect Compensation- {{ date('F Y', strtotime($month . '-01')) }}</h2>

    {{-- Generate Compensation Form --}}
    <form method="POST" action="{{ route('hr4.direct_compensation.generate') }}" class="mb-6 flex items-center gap-2">
        @csrf
        <input type="month" name="month" value="{{ $month }}" class="border rounded px-2 py-1">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
            Generate Compensation
        </button>
    </form>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 border border-green-300 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Compensation Table --}}
    <table class="min-w-full border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Employee ID</th>
                <th class="border px-2 py-1">Name</th>
                <th class="border px-2 py-1">Base Salary</th>
                <th class="border px-2 py-1">Shift Allowance</th>
                <th class="border px-2 py-1">Overtime</th>
                <th class="border px-2 py-1">Bonus</th>
                <th class="border px-2 py-1">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compensations as $comp)
                <tr>
                    <td class="border px-2 py-1">{{ $comp->employee_id }}</td>
                    <td class="border px-2 py-1">{{ $comp->employee->first_name }} {{ $comp->employee->last_name }}</td>
                    <td class="border px-2 py-1">{{ number_format($comp->base_salary,2) }}</td>
                    <td class="border px-2 py-1">{{ number_format($comp->shift_allowance,2) }}</td>
                    <td class="border px-2 py-1">{{ number_format($comp->overtime_pay,2) }}</td>
                    <td class="border px-2 py-1">{{ number_format($comp->bonus,2) }}</td>
                    <td class="border px-2 py-1">{{ number_format($comp->total_compensation,2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-2 py-1 text-center">No compensation records found for this month.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection