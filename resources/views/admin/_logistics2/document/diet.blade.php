@extends('admin._logistics2.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Diet Documents</h2>

<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f3f4f6;">
            <th style="padding:10px; border:1px solid #ddd;">Order ID</th>
            <th style="padding:10px; border:1px solid #ddd;">Encounter</th>
            <th style="padding:10px; border:1px solid #ddd;">Patient Name</th>
            <th style="padding:10px; border:1px solid #ddd;">Diet Type</th>
            <th style="padding:10px; border:1px solid #ddd;">Instructions</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
            <th style="padding:10px; border:1px solid #ddd;">Created</th>
        </tr>
    </thead>

    <tbody>
        @forelse($dietOrders as $diet)
        <tr>
            <td style="padding:10px; border:1px solid #ddd;">{{ $diet->id }}</td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $diet->encounter_id }}
            </td>

            <!-- PATIENT NAME (from patients_core1 join) -->
            <td style="padding:10px; border:1px solid #ddd; font-weight:600;">
                {{ $diet->first_name }} {{ $diet->last_name }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <span style="color:#2563eb; font-weight:bold;">
                    {{ $diet->diet_type }}
                </span>
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $diet->instructions ?? 'No instructions' }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <span style="color:green; font-weight:600;">
                    {{ $diet->status }}
                </span>
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $diet->created_at }}
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="7" style="padding:15px; text-align:center; color:gray;">
                No diet orders found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection