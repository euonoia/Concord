@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container" style="padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h2 style="font-weight: 700; color: #333; margin-bottom: 25px;">
        <i class="fas fa-seedling" style="color: #28a745; margin-right: 10px;"></i>Succession Planning
    </h2>

    {{-- Feedback Alerts --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Add Position Section --}}
    <div style="background: #ffffff; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eef0f2;">
        <form method="POST" action="{{ route('succession.position.store') }}">
            @csrf
            <h3 style="font-size: 1.2rem; margin-bottom: 20px; color: #444;"><i class="fas fa-plus-circle"></i> Add New Critical Position</h3>
            <div style="display: flex; gap: 20px; align-items: flex-end;">
                <div style="flex: 2;">
                    <label style="font-weight: 600; font-size: 0.9rem;">Position Title:</label>
                    <input type="text" name="position_title" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;" placeholder="e.g. Chief Operations Officer" required>
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: 600; font-size: 0.9rem;">Criticality:</label>
                    <select name="criticality" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <button type="submit" style="background: #007bff; color: white; padding: 10px 25px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s;">
                    <i class="fas fa-save"></i> Add Position
                </button>
            </div>
        </form>
    </div>

    <h3 style="font-size: 1.3rem; margin-bottom: 15px;"><i class="fas fa-list"></i> Succession Positions</h3>
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <thead style="background: #f1f3f5;">
            <tr>
                <th style="padding: 15px; text-align: left;">Position Title</th>
                <th style="padding: 15px; text-align: left;">Branch ID</th>
                <th style="padding: 15px; text-align: left;">Criticality</th>
                <th style="padding: 15px; text-align: center;">Bench Strength</th>
                <th style="padding: 15px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $p)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;"><strong>{{ $p->position_title }}</strong></td>
                    <td style="padding: 15px;"><span class="badge" style="background: #e9ecef; padding: 5px 10px; border-radius: 4px; font-family: monospace;">{{ $p->branch_id }}</span></td>
                    <td style="padding: 15px;">
                        <span style="font-weight: bold; color: {{ $p->criticality == 'high' ? '#dc3545' : ($p->criticality == 'medium' ? '#fd7e14' : '#28a745') }}">
                            <i class="fas fa-circle" style="font-size: 0.7rem; margin-right: 5px;"></i> {{ ucfirst($p->criticality) }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">{{ $p->candidates_count }} Candidates</td>
                    <td style="padding: 15px; text-align: center;">
                        <form method="POST" action="{{ route('succession.position.destroy', $p->id) }}" onsubmit="return confirm('Archive this position?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-archive"></i> Archive
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding: 30px; color: #888;">No positions found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <hr style="border: 0; height: 1px; background: #dee2e6; margin-bottom: 40px;">

    {{-- Add Candidate Section --}}
    <div style="background: #f0f7ff; padding: 30px; border-radius: 15px; border: 1px solid #cde4ff; margin-bottom: 40px;">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf
            <h3 style="font-size: 1.2rem; margin-bottom: 25px; color: #0056b3;"><i class="fas fa-user-tie"></i> Strategic Candidate Assessment</h3>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: 600;">Target Position:</label>
                    <select name="position_id" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="">-- Select Position --</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->branch_id }}">{{ $p->position_title }} ({{ $p->branch_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight: 600;">Select Employee:</label>
                    <select name="employee_id" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-weight: 600;">Readiness Level:</label>
                    <select name="readiness" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="Ready Now">Ready Now</option>
                        <option value="1-2 Years">1-2 Years</option>
                        <option value="3+ Years">3+ Years</option>
                        <option value="Emergency">Emergency Only</option>
                    </select>
                </div>
            </div>

            {{-- 9-Box Grid Data Inputs --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="font-weight: 600;">Performance Score (1-10):</label>
                    <input type="number" name="perf_score" min="1" max="10" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Potential Score (1-10):</label>
                    <input type="number" name="pot_score" min="1" max="10" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Retention Risk:</label>
                    <select name="retention_risk" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                        <option value="Low">Low Risk</option>
                        <option value="Medium" selected>Medium Risk</option>
                        <option value="High">High Risk</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                <div>
                    <label style="font-weight: 600;">Target Transition Date:</label>
                    <input type="date" name="effective_at" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" required>
                </div>
                <div>
                    <label style="font-weight: 600;">Succession Development Focus:</label>
                    <input type="text" name="development_plan" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #b8daff; margin-top: 5px;" placeholder="Identify specific training or mentorship required...">
                </div>
            </div>

            <button type="submit" style="margin-top: 25px; background: #28a745; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.3s; box-shadow: 0 4px 10px rgba(40,167,69,0.2);">
                <i class="fas fa-user-check"></i> Finalize Candidate Selection
            </button>
        </form>
    </div>

    <h3 style="font-size: 1.3rem; margin-bottom: 15px;"><i class="fas fa-users-cog"></i> Active Succession Pipeline</h3>
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <thead style="background: #f1f3f5;">
            <tr>
                <th style="padding: 15px; text-align: left;">Target Position</th>
                <th style="padding: 15px; text-align: left;">Employee Name</th>
                <th style="padding: 15px; text-align: center;">Readiness</th>
                <th style="padding: 15px; text-align: center;">Matrix (Perf/Pot)</th>
                <th style="padding: 15px; text-align: center;">Date</th>
                <th style="padding: 15px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidates as $c)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">{{ $c->position->position_title ?? 'Unknown' }}</td>
                    <td style="padding: 15px;"><strong>{{ $c->employee->first_name }} {{ $c->employee->last_name }}</strong></td>
                    <td style="padding: 15px; text-align: center;">
                        <span class="badge" style="background: {{ $c->readiness == 'Ready Now' ? '#d4edda' : '#fff3cd' }}; color: {{ $c->readiness == 'Ready Now' ? '#155724' : '#856404' }}; padding: 5px 10px; border-radius: 20px; font-weight: bold; font-size: 0.8rem;">
                            {{ $c->readiness }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #f8f9fa; padding: 5px 10px; border-radius: 5px; font-weight: bold; color: #6c757d;">
                            {{ $c->performance_score }} / {{ $c->potential_score }}
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center; color: #666;">{{ \Carbon\Carbon::parse($c->effective_at)->format('M d, Y') }}</td>
                    <td style="padding: 15px; text-align: center;">
                        <form method="POST" action="{{ route('succession.candidate.destroy', $c->id) }}" onsubmit="return confirm('Remove this candidate?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding: 30px; color: #888;">No candidates currently in the pipeline.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection