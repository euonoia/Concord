@extends('layouts.dashboard.app')

@section('content')

<div class="container">
    <h2>My Competencies</h2>

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
            </tr>
        </thead>

        <tbody>
            @foreach($competencies as $comp)
            <tr>
                <td>{{ $comp->competency_code }}</td>
                <td>{{ $comp->name }}</td>
                <td>{{ $comp->description }}</td>
                <td>{{ $comp->competency_group }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @endif
</div>

@endsection