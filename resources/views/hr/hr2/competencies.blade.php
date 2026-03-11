@extends('layouts.dashboard.app')

@section('content')

<div class="container">
<h2>My Competencies</h2>

@if(session('success'))
<div style="color:green">{{ session('success') }}</div>
@endif

@if(!empty($error))
<div style="color:red">{{ $error }}</div>
@endif

@if($competencies->isEmpty())
<p>No competencies available.</p>
@else

<table border="1" width="100%" cellpadding="10">
<thead>
<tr>
<th>Code</th>
<th>Name</th>
<th>Description</th>
<th>Group</th>
<th>Status</th>
<th>Score</th>
<th>Validator</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($competencies as $comp)

@php
$isCompleted = in_array($comp->competency_code, $completed);
$isEnrolled = in_array($comp->competency_code, $enrolled);

$scoreRecord = $scores[$comp->competency_code] ?? null;
$scoreValue = $scoreRecord ? $scoreRecord->total_score : '-';

$evaluator = '-';
if($scoreRecord && $scoreRecord->evaluated_by) {
$evaluatorEmployee = \App\Models\Employee::where('employee_id', $scoreRecord->evaluated_by)->first();
if($evaluatorEmployee) {
$evaluator = $evaluatorEmployee->first_name.' '.$evaluatorEmployee->last_name;
}
}
@endphp

<tr>

<td>{{ $comp->competency_code }}</td>
<td>{{ $comp->name }}</td>
<td>{{ $comp->description }}</td>
<td>{{ $comp->competency_group }}</td>

<td>
@if($isCompleted)
<span style="color:green;font-weight:bold">Completed</span>
@elseif($isEnrolled)
<span style="color:blue">Enrolled</span>
@else
<span style="color:orange">Not Enrolled</span>
@endif
</td>

<td>{{ $scoreValue }}</td>
<td>{{ $evaluator }}</td>

<td>

@if(!$isEnrolled)

<form method="POST" action="{{ route('user.competency.enroll',$comp->competency_code) }}">
@csrf
<button type="submit">Enroll</button>
</form>

@elseif($isEnrolled && !$isCompleted)

<form method="POST" action="{{ route('user.competency.complete',$comp->competency_code) }}">
@csrf
<button type="submit">Complete</button>
</form>

@else
—
@endif

</td>

</tr>

@endforeach

</tbody>
</table>

@endif

</div>

@endsection