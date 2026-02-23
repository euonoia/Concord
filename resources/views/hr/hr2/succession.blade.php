@extends('layouts.dashboard.app')

@section('title', 'Succession Planning')

@section('content')
<div class="container">
    <div class="header-box" style="margin-bottom: 20px;">
        <h2>Succession Roadmap</h2>
        <p>Positions where you have been identified as a potential successor.</p>
    </div>

    <div class="succession-list" style="display: flex; flex-direction: column; gap: 15px;">
        @forelse($nominations as $nomination)
            <div class="card" style="border-left: 5px solid #f39c12; padding: 20px; border-radius: 8px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="text-transform: uppercase; font-size: 0.75rem; color: #e67e22; font-weight: bold;">
                            Nominated for Position:
                        </span>
                        <h3 style="margin: 5px 0;">{{ $nomination->position_title }}</h3>
                        <p style="margin: 0; color: #666;">
                            Criticality Level: <strong>{{ $nomination->criticality }}</strong>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge" style="background: #ebf5fb; color: #2e86c1; padding: 5px 10px; border-radius: 4px; font-size: 0.85rem;">
                            Readiness: {{ $nomination->readiness_level ?? 'Under Review' }}
                        </span>
                    </div>
                </div>
                
                <div style="margin-top: 15px; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                    <small style="color: #777;">Development Plan:</small>
                    <p style="margin: 5px 0 0; font-size: 0.9rem;">
                        {{ $nomination->development_notes ?? 'Your training path for this role is currently being drafted by HR.' }}
                    </p>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 30px; text-align: center; color: #888;">
                <i class="fa fa-info-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <p>You are not currently listed as a successor for any critical positions.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection