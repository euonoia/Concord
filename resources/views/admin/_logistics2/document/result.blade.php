@extends('admin._logistics2.layouts.app')

@section('content')

<h2 style="margin-bottom:20px;">Lab Result Documents</h2>

<div style="background:white; padding:20px; border-radius:8px;">

    <p><strong>Order ID:</strong> {{ $order->id }}</p>
    <p><strong>Test Name:</strong> {{ $order->test_name }}</p>

    <!-- PATIENT NAME -->
    <p><strong>Patient Name:</strong> {{ $order->first_name }} {{ $order->last_name }}</p>

    <p><strong>Encounter:</strong> {{ $order->encounter_id }}</p>

    <hr>

    <h3>Result Data</h3>

    @if(is_array($result) && count($result))
        @foreach($result as $key => $value)
            <p>
                <strong>{{ ucfirst(str_replace('_',' ',$key)) }}:</strong> {{ $value }}
            </p>
        @endforeach
    @else
        <p style="color:gray;">No result data available.</p>
    @endif

</div>

@endsection