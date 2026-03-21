@extends('admin._logistics2.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Lab Documents</h2>

<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f3f4f6;">
            <th style="padding:10px; border:1px solid #ddd;">Order ID</th>
            <th style="padding:10px; border:1px solid #ddd;">Encounter</th>
            <th style="padding:10px; border:1px solid #ddd;">Patient Name</th>
            <th style="padding:10px; border:1px solid #ddd;">Test Name</th>
            <th style="padding:10px; border:1px solid #ddd;">Priority</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
            <th style="padding:10px; border:1px solid #ddd;">Created</th>
            <th style="padding:10px; border:1px solid #ddd;">Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($labOrders as $order)
        <tr>
            <td style="padding:10px; border:1px solid #ddd;">{{ $order->id }}</td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $order->encounter_id }}
            </td>

            <!-- PATIENT NAME -->
            <td style="padding:10px; border:1px solid #ddd; font-weight:600;">
                {{ $order->first_name }} {{ $order->last_name }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $order->test_name }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <span style="color:{{ $order->priority == 'Urgent' ? 'red' : 'green' }}; font-weight:600;">
                    {{ $order->priority }}
                </span>
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $order->status }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $order->created_at }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <a href="{{ route('admin.logistics2.document.result', $order->id) }}" 
                   style="padding:6px 12px; background:#2563eb; color:white; border-radius:5px; text-decoration:none;">
                   View Result
                </a>
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="8" style="padding:15px; text-align:center; color:gray;">
                No lab orders found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection