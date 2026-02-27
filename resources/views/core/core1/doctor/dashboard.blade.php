@extends('layouts.core1.layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @include('core.core1.doctor.overview')
@endsection

