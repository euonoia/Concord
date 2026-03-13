@extends('admin.hr1.layouts.app')

@section('content')
<div class="dashboard_container">
    <h2>Welcome, {{ Auth::user()->username }}</h2>
    <p>Here’s your HR1 summary overview:</p>

    <div class="dashboard_grid">
        <div class="dashboard_card">
            <h3>Department Summary</h3>
            <p class="text-sm text-gray-600">Quick totals of assigned staff and available slots.</p>

            @if(isset($departmentSummaries) && count($departmentSummaries) > 0)
            <div style="margin-top:12px; overflow:auto">
                <table style="width:100%; border-collapse:collapse; font-size:0.95rem">
                    <thead>
                        <tr style="background:#f7f7fb">
                            <th style="padding:8px; border:1px solid #eee; text-align:left">Department</th>
                            <th style="padding:8px; border:1px solid #eee; text-align:right">Assigned</th>
                            <th style="padding:8px; border:1px solid #eee; text-align:right">Max</th>
                            <th style="padding:8px; border:1px solid #eee; text-align:right">Available</th>
                            <th style="padding:8px; border:1px solid #eee; text-align:right">Avail. Specs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentSummaries as $row)
                        <tr>
                            <td style="padding:8px; border:1px solid #eee">{{ $row['department']->name }}</td>
                            <td style="padding:8px; border:1px solid #eee; text-align:right">{{ $row['assigned'] }}</td>
                            <td style="padding:8px; border:1px solid #eee; text-align:right">{{ $row['max'] }}</td>
                            <td style="padding:8px; border:1px solid #eee; text-align:right">{{ $row['available'] }}</td>
                            <td style="padding:8px; border:1px solid #eee; text-align:right">{{ $row['available_specializations'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <p class="text-muted">No department summaries available.</p>
            @endif
        </div>
    </div>
</div>
@endsection