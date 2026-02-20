@extends('admin.layouts.app')

@section('title', 'Succession Planning - HR2 Admin')

@section('content')
<div class="container">
    <h2>Succession Planning</h2>

    {{-- Feedback Alerts --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <form method="POST" action="{{ route('succession.position.store') }}">
            @csrf
            <h3>Add New Position</h3>
            <div style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex: 2;">
                    <label>Position Title:</label>
                    <input type="text" name="position_title" style="width: 100%;" placeholder="e.g. Senior Manager" required>
                </div>
                <div style="flex: 1;">
                    <label>Criticality:</label>
                    <select name="criticality" style="width: 100%;" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <button type="submit" style="background: #007bff; color: white; padding: 8px 15px; border: none; cursor: pointer;">Add Position</button>
            </div>
        </form>
    </div>

    <h3 class="section-title">Succession Positions</h3>
    <table border="1" style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
        <thead style="background: #eee;">
            <tr>
                <th>Position Title</th>
                <th>Branch ID</th>
                <th>Criticality</th>
                <th>Candidates</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $p)
                <tr>
                    <td><strong>{{ $p->position_title }}</strong></td>
                    <td><code>{{ $p->branch_id }}</code></td>
                    <td>
                        <span style="color: {{ $p->criticality == 'high' ? 'red' : ($p->criticality == 'medium' ? 'orange' : 'green') }}">
                            {{ ucfirst($p->criticality) }}
                        </span>
                    </td>
                    <td style="text-align: center;">{{ $p->candidates_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('succession.position.destroy', $p->id) }}" onsubmit="return confirm('Archive this position?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">Archive</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; padding: 15px;">No positions found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <hr>

    <div style="background: #f0f7ff; padding: 20px; border-radius: 8px; margin: 40px 0;">
        <form method="POST" action="{{ route('succession.candidate.store') }}">
            @csrf
            <h3>Add Candidate to Position</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label>Target Position:</label>
                    <select name="position_id" style="width: 100%;" required>
                        <option value="">-- Select Position --</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->branch_id }}">{{ $p->position_title }} ({{ $p->branch_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Select Employee:</label>
                    <select name="employee_id" style="width: 100%;" required>
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $e)
                            {{-- Adjusted to use standard 'id' based on previous controller fix --}}
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Readiness:</label>
                    <select name="readiness" style="width: 100%;" required>
                        <option value="ready">Ready</option>
                        <option value="not_ready">Not Ready</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                <div>
                    <label>Effective Date:</label>
                    <input type="date" name="effective_at" style="width: 100%;" required>
                </div>
                <div>
                    <label>Development Plan:</label>
                    <input type="text" name="development_plan" style="width: 100%;" placeholder="Required training or coaching steps...">
                </div>
            </div>

            <button type="submit" style="margin-top: 15px; background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Confirm Candidate
            </button>
        </form>
    </div>

    

[Image of succession planning matrix]


    <h3 class="section-title">Active Succession Candidates</h3>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #eee;">
            <tr>
                <th>Target Position</th>
                <th>Employee Name</th>
                <th>Readiness</th>
                <th>Effective Date</th>
                <th>Development Plan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($candidates as $c)
                <tr>
                    <td>{{ $c->position->position_title ?? 'Unknown' }}</td>
                    <td>{{ $c->employee->first_name }} {{ $c->employee->last_name }}</td>
                    <td>
                        <strong style="color: {{ $c->readiness == 'ready' ? 'green' : 'gray' }}">
                            {{ ucfirst($c->readiness) }}
                        </strong>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($c->effective_at)->format('M d, Y') }}</td>
                    <td>{{ $c->development_plan ?: 'No plan set' }}</td>
                    <td>
                        <form method="POST" action="{{ route('succession.candidate.destroy', $c->id) }}" onsubmit="return confirm('Archive this candidate?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding: 15px;">No candidates found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection