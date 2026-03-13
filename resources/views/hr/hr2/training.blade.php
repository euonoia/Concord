@extends('layouts.dashboard.app')

@section('title', 'My Training')

@section('content')
<div class="container" style="padding-top: 20px;">
    <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 4px solid #28a745;">
        <h2 style="margin: 0; color: #333;"><i class="bi bi-journal-check text-success"></i> My Assigned Training</h2>
        <p style="color: #666;">These are the training sessions assigned to you by the HR Department.</p>
    </div>

    <div style="margin-top: 20px; background: #fff; border-radius: 12px; border: 1px solid #eee; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #eee; text-align: left;">
                    <th style="padding: 15px;">Competency & Venue</th>
                    <th style="padding: 15px;">Schedule</th>
                    <th style="padding: 15px;">Trainer / Instructor</th>
                    <th style="padding: 15px; text-align: center;">Timeline</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr style="border-bottom: 1px solid #f4f4f4; transition: 0.3s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                    <td style="padding: 15px;">
                        <span style="font-weight: bold; font-size: 1.1em; color: #2c3e50;">{{ $session->competency_code }}</span><br>
                        <small style="color: #7f8c8d;"><i class="bi bi-geo-alt"></i> {{ $session->venue ?? 'Online/To be announced' }}</small>
                    </td>
                    <td style="padding: 15px;">
                        <div>{{ \Carbon\Carbon::parse($session->training_date)->format('D, M d, Y') }}</div>
                        <small style="color: #27ae60; font-weight: bold;">{{ \Carbon\Carbon::parse($session->training_time)->format('h:i A') }}</small>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 35px; height: 35px; background: #e8f5e9; color: #2e7d32; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <div style="font-weight: 500;">
                                    {{ $session->trainer->first_name ?? '' }} {{ $session->trainer->last_name ?? $session->trainer_id }}
                                </div>
                                <small style="color: #999;">Authorized Trainer</small>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        @php
                            $date = \Carbon\Carbon::parse($session->training_date);
                        @endphp

                        @if($date->isPast() && !$date->isToday())
                            <span style="padding: 5px 15px; border-radius: 15px; background: #f1f2f6; color: #a4b0be; font-size: 12px;">Concluded</span>
                        @elseif($date->isToday())
                            <span style="padding: 5px 15px; border-radius: 15px; background: #fff3cd; color: #856404; font-size: 12px; font-weight: bold; border: 1px solid #ffeeba;">Happening Today</span>
                        @else
                            <span style="padding: 5px 15px; border-radius: 15px; background: #e3f2fd; color: #0d47a1; font-size: 12px;">Waiting</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 50px; text-align: center; color: #ccc;">
                        <i class="bi bi-calendar-x" style="font-size: 3em; display: block; margin-bottom: 10px;"></i>
                        No training assigned yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection