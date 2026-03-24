@extends('admin._logistics2.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Warehouse Purchase Orders </h2>

<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f1f1f1;">
            <th>ID</th>
            <th>PO Number</th>
            <th>Drug</th>
            <th>Quantity</th>
            <th>Supplier</th>
            <th>Delivery Date</th>
            <th>Status</th>
            <th>Vehicle</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @foreach($purchaseOrders as $po)
        <tr style="border-bottom:1px solid #ddd;">
            <td>{{ $po->id }}</td>
            <td>{{ $po->po_number }}</td>
            <td>{{ $po->drug_name }}</td>
            <td>{{ $po->requested_quantity }}</td>
            <td>{{ $po->selected_supplier }}</td>
            <td>{{ $po->expected_delivery_date }}</td>

            <td>
                <span style="padding:5px 10px; background:#eee; border-radius:5px;">
                    {{ strtoupper($po->status) }}
                </span>
            </td>

            <td>{{ $po->model_name ?? 'Not Assigned' }}</td>

            <td>
                @if($po->status == 'pending')
                <form method="POST" action="{{ route('admin.logistics2.purchase.assign', $po->id) }}">
                    @csrf

                    <select name="model_name" required>
                        <option value="">Select Vehicle</option>

                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->model_name }}">
                                {{ $vehicle->model_name }} ({{ $vehicle->plate_number }})
                            </option>
                        @endforeach

                    </select>

                    <button type="submit" style="background:green;color:white;border:none;padding:6px 12px;border-radius:5px;">
                        Approve & Assign
                    </button>
                </form>
                @else
                    <span style="color:green;">Assigned</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection