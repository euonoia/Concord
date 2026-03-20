@extends('admin._logistics2.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Surgery Documents</h2>

<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:#f3f4f6;">
            <th style="padding:10px; border:1px solid #ddd;">Order ID</th>
            <th style="padding:10px; border:1px solid #ddd;">Encounter</th>
            <th style="padding:10px; border:1px solid #ddd;">Patient Name</th>
            <th style="padding:10px; border:1px solid #ddd;">Procedure</th>
            <th style="padding:10px; border:1px solid #ddd;">Priority</th>
            <th style="padding:10px; border:1px solid #ddd;">Proposed Date</th>
            <th style="padding:10px; border:1px solid #ddd;">Proposed Time</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($surgeryOrders as $surgery)
        <tr>
            <td style="padding:10px; border:1px solid #ddd;">{{ $surgery->id }}</td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $surgery->encounter_id }}
            </td>

            <!-- PATIENT NAME -->
            <td style="padding:10px; border:1px solid #ddd; font-weight:600;">
                {{ $surgery->first_name }} {{ $surgery->last_name }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <strong>{{ $surgery->procedure_name }}</strong>
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                <span style="color:{{ $surgery->priority == 'Urgent' ? 'red' : 'green' }}; font-weight:600;">
                    {{ $surgery->priority }}
                </span>
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $surgery->proposed_date }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $surgery->proposed_time }}
            </td>

            <td style="padding:10px; border:1px solid #ddd;">
                {{ $surgery->status }}
            </td>
        </tr>

        @empty
        <tr>
            <td colspan="8" style="padding:15px; text-align:center; color:gray;">
                No surgery orders found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection