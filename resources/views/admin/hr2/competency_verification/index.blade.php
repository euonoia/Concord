@extends('layouts.dashboard.app')

@section('content')

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Competency Verification</h2>

    <a href="{{ route('competencies.index') }}" class="btn btn-secondary">
        ← Back to Competency Framework
    </a>
</div>

@if(session('success'))
<div style="color:green">{{ session('success') }}</div>
@endif

<table border="1" width="100%" cellpadding="10">

<thead>
<tr>
<th>Employee</th>
<th>Employee ID</th>
<th>Department</th>
<th>Specialization</th>
<th>Competency</th>
<th>Status</th>
<th>Completed At</th>
<th>Verified By</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($completions as $c)

<tr>

<td>{{ $c->first_name }} {{ $c->last_name }}</td>

<td>{{ $c->employee_id }}</td>

<td>{{ $c->department_id }}</td>

<td>{{ $c->specialization }}</td>

<td>{{ $c->competency_code }}</td>

<td>
@if($c->verified_by)
<span style="color:green;font-weight:bold">Verified</span>
@else
<span style="color:orange">Pending</span>
@endif
</td>

<td>{{ $c->completed_at }}</td>

<td>{{ $c->verified_by ?? '—' }}</td>

<td>

@if(!$c->verified_by)

<form method="POST" action="{{ route('admin.hr2.competency.verify',$c->id) }}">

@csrf

<textarea
name="verification_notes"
placeholder="Verification notes"
style="width:200px"
></textarea>

<br><br>

<button type="submit" class="btn btn-success">
Verify
</button>

</form>

@else

—

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection