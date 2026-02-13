@extends('layouts.app')
@section('title', 'Clinical Core')
@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md border-l-8 border-blue-500">
        <h1 class="text-2xl font-bold text-blue-800">Medical Core System</h1>
        <p class="text-gray-600 mt-2">Status: <strong>Active Duty</strong></p>
        <hr class="my-4">
        <div class="grid grid-cols-2 gap-4">
            <button class="bg-blue-600 text-white p-4 rounded shadow">View Patient Records</button>
            <button class="bg-slate-200 text-slate-700 p-4 rounded shadow">Schedule Surgery</button>
        </div>
    </div>
@endsection