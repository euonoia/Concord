@extends('layouts.app')
@section('title', 'Financials')
@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md border-l-8 border-emerald-500">
        <h1 class="text-2xl font-bold text-emerald-800">Financial Control Center</h1>
        <p class="text-gray-600 mt-2">Authenticated: {{ Auth::user()->email }}</p>
        <div class="mt-4 p-4 bg-emerald-50 text-emerald-700 rounded">
            <strong>System Note:</strong> You are accessing the billing and insurance module.
        </div>
    </div>
@endsection