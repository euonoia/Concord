@extends('layouts.dashboard.app')

@section('content')

<div class="container">
<h2>My Competencies</h2>

@if(session('success'))
<div style="color:green">{{ session('success') }}</div>
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
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($competencies as $comp)

@php
$isCompleted = in_array($comp->competency_code, $completed);
@endphp

<tr>
<td>{{ $comp->competency_code }}</td>
<td>{{ $comp->name }}</td>
<td>{{ $comp->description }}</td>
<td>{{ $comp->competency_group }}</td>

<td>
@if($isCompleted)
<span style="color:green;font-weight:bold">Completed</span>
@else
<span style="color:orange">Not Completed</span>
@endif
</td>

<td>
@if(!$isCompleted)
<form method="POST" action="{{ route('user.competency.complete',$comp->competency_code) }}">
@csrf
<button type="submit">Complete Competency</button>
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