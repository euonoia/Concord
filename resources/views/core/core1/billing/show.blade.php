@extends('layouts.core1.layouts.app')

@section('title', 'Bill Details')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Bill Details</h1>
                <p class="text-gray-600 mt-1">View billing information</p>
            </div>
            <a href="{{ route('billing.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 max-w-3xl">
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bill Number</label>
                    <p class="text-gray-900 font-semibold">{{ $bill->bill_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                        $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 
                        ($bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                    }}">
                        {{ $bill->status }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                    <p class="text-gray-900 font-semibold">{{ $bill->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $bill->patient->patient_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bill Date</label>
                    <p class="text-gray-900">{{ $bill->bill_date->format('M d, Y') }}</p>
                </div>
                @if($bill->due_date)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <p class="text-gray-900">{{ $bill->due_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($bill->paid_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paid At</label>
                    <p class="text-gray-900">{{ $bill->paid_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
                @if($bill->payment_method)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <p class="text-gray-900">{{ $bill->payment_method }}</p>
                </div>
                @endif
            </div>

            @if($bill->items && count($bill->items) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Items</label>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bill->items as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item['description'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($item['amount'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="pt-6 border-t border-gray-200">
                <div class="flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">${{ number_format($bill->subtotal, 2) }}</span>
                        </div>
                        @if($bill->tax > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax:</span>
                            <span class="text-gray-900">${{ number_format($bill->tax, 2) }}</span>
                        </div>
                        @endif
                        @if($bill->discount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount:</span>
                            <span class="text-green-600">-${{ number_format($bill->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                            <span class="text-lg font-semibold text-gray-900">${{ number_format($bill->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

