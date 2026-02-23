@extends('layouts.dashboard.app')

@section('title', 'Training Sessions')

@section('content')
<div class="container">
    <div class="header-box" style="margin-bottom: 20px;">
        <h2>Training Calendar</h2>
        <p>Sign up for upcoming technical and soft-skill training sessions.</p>
    </div>

    <div class="table-responsive" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #eee; text-align: left;">
                    <th style="padding: 10px;">Training Title</th>
                    <th style="padding: 10px;">Schedule</th>
                    <th style="padding: 10px;">Location</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">
                        <strong>{{ $session->title }}</strong><br>
                        <small style="color: #666;">{{ Str::limit($session->description, 50) }}</small>
                    </td>
                    <td style="padding: 15px;">
                        {{ \Carbon\Carbon::parse($session->start_datetime)->format('M d, Y') }}<br>
                        <small>{{ \Carbon\Carbon::parse($session->start_datetime)->format('h:i A') }}</small>
                    </td>
                    <td style="padding: 15px;">{{ $session->venue }}</td>
                    <td style="padding: 15px;">
                        @if($session->start_datetime < now())
                            <span style="color: #e74c3c;">Closed</span>
                        @else
                            <span style="color: #27ae60;">Open</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: right;">
                        @if($session->enrolls->isNotEmpty())
                            <button class="btn btn-sm btn-success" disabled>Enrolled</button>
                        @elseif($session->start_datetime < now())
                            <button class="btn btn-sm btn-secondary" disabled>Expired</button>
                        @else
                            <form action="{{ route('user.training.enroll', $session->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Join Session</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No training sessions scheduled.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection