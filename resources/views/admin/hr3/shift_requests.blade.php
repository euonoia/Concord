@extends('admin.hr3.layouts.app')

@section('content')

<div class="container" style="padding: 20px;">

    <h2 style="margin-bottom: 20px;">Shift Requests</h2>

    @if(session('success'))
        <div style="background:#d4edda; padding:15px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background:white; padding:20px; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,0.1);">

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #eee;">
                    <th style="padding:10px;">Employee</th>
                    <th style="padding:10px;">Shift</th>
                    <th style="padding:10px;">Day</th>
                    <th style="padding:10px;">Time</th>
                    <th style="padding:10px;">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($requests as $req)
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">
                            <strong>{{ $req->first_name }} {{ $req->last_name }}</strong><br>
                            <small>{{ $req->employee_id }} - {{ $req->specialization }}</small>
                        </td>

                        <td style="padding:10px;">{{ $req->shift_name }}</td>

                        <td style="padding:10px;">{{ $req->day_of_week }}</td>

                        <td style="padding:10px;">
                            {{ \Carbon\Carbon::parse($req->start_time)->format('h:i A') }} -
                            {{ \Carbon\Carbon::parse($req->end_time)->format('h:i A') }}
                        </td>

                        <td style="padding:10px;">
                            <form action="{{ route('admin.hr3.shift_requests.approve',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button style="background:#28a745; color:white; border:none; padding:6px 12px; border-radius:6px;">
                                    Approve
                                </button>
                            </form>

                            <form action="{{ route('admin.hr3.shift_requests.reject',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button style="background:#dc3545; color:white; border:none; padding:6px 12px; border-radius:6px;">
                                    Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:30px; color:#999;">
                            No pending shift requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection