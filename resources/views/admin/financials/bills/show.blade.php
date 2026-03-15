@extends('admin.financials.layouts.app')

@section('title', 'Bill Details')

@section('content')
<div class="container">
    <h2 class="mb-4">Bill Details: {{ $bill->bill_number }}</h2>

    <p><strong>Patient:</strong> {{ $bill->first_name ?? '' }} {{ $bill->last_name ?? '' }} (PID: {{ $bill->patient_id }})</p>
    <p><strong>Encounter ID:</strong> {{ $bill->encounter_id }}</p>
    <p><strong>Bill Date:</strong> {{ $bill->bill_date }}</p>
    <p><strong>Status:</strong> <span class="badge {{ $bill->status == 'paid' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($bill->status) }}</span></p>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->items as $item)
            <tr>
                <td>{{ $item['desc'] }}</td>
                <td>{{ number_format($item['price'], 2) }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ number_format($item['price'] * $item['qty'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Subtotal:</strong> {{ number_format($bill->subtotal, 2) }}</p>
    <p><strong>Tax:</strong> {{ number_format($bill->tax, 2) }}</p>
    <p><strong>Discount:</strong> {{ number_format($bill->discount, 2) }}</p>
    <p><strong>Total:</strong> {{ number_format($bill->total, 2) }}</p>

    @if($bill->status != 'paid')
    <form action="{{ route('financials.bills.pay', $bill->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control">
                <option value="cash">Cash</option>
                <option value="credit_card">Credit Card</option>
                <option value="online">Online Transfer</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mark as Paid</button>
    </form>
    @endif

    <a href="{{ route('financials.bills.index') }}" class="btn btn-secondary mt-3">Back to Bills</a>
</div>
@endsection