{{-- Add New Critical Position --}}
<div style="background: #ffffff; padding: 25px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #eef0f2;">
    <form method="POST" action="{{ route('succession.position.store') }}">
        @csrf
        <h3 style="font-size: 1.2rem; margin-bottom: 20px; color: #444;"><i class="fas fa-plus-circle"></i> Add New Critical Position</h3>
        
        <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 0.8fr auto; gap: 15px; align-items: flex-end;">
            <div>
                <label style="font-weight: 600; font-size: 0.9rem;">Position Title:</label>
                <input type="text" name="position_title" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;" placeholder="e.g. Senior Pathologist" required>
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem;">Department:</label>
                <select id="dept_select" name="department_id" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;" required>
                    <option value="">-- Select Dept --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem;">Specialization:</label>
                <select id="spec_select" name="specialization" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;">
                    <option value="">-- Choose Dept First --</option>
                </select>
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem;">Criticality:</label>
                <select name="criticality" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-top: 5px;" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s;">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </form>
</div>

{{-- Succession Positions Table --}}
<h3 style="font-size: 1.3rem; margin-bottom: 15px;"><i class="fas fa-list"></i> Succession Positions</h3>
<table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 40px;">
    <thead style="background: #f1f3f5;">
        <tr>
            <th style="padding: 15px; text-align: left;">Position Title</th>
            <th style="padding: 15px; text-align: left;">Dept / Specialization</th>
            <th style="padding: 15px; text-align: left;">Criticality</th>
            <th style="padding: 15px; text-align: center;">Bench Strength</th>
            <th style="padding: 15px; text-align: center;">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($positions as $p)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px;">
                    <strong>{{ $p->position_title }}</strong><br>
                    <small style="color: #888;">ID: {{ $p->position_id }}</small>
                </td>
                <td style="padding: 15px;">
                    {{ $p->department_name ?? 'N/A' }}<br>
                    <small style="color: #28a745;">{{ $p->specialization ?? 'General' }}</small>
                </td>
                <td style="padding: 15px;">
                    <span style="font-weight: bold; color: {{ $p->criticality == 'high' ? '#dc3545' : ($p->criticality == 'medium' ? '#fd7e14' : '#28a745') }}">
                        {{ ucfirst($p->criticality) }}
                    </span>
                </td>
                <td style="padding: 15px; text-align: center;">{{ $p->candidates_count }} Candidates</td>
                <td style="padding: 15px; text-align: center;">
                    <form method="POST" action="{{ route('succession.position.destroy', $p->id) }}" onsubmit="return confirm('Archive this position?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer;">
                            <i class="fas fa-archive"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center; padding: 30px; color: #888;">No positions found.</td></tr>
        @endforelse
    </tbody>
</table>