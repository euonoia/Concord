@extends('layouts.app')
@section('title', 'Logistics Subsystem')
@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md border-l-8 border-orange-500">
        <h1 class="text-2xl font-bold text-orange-800">Logistics & Inventory</h1>
        <p class="text-gray-600 mt-2">Welcome, {{ Auth::user()->username }}. Manage hospital supplies and medicine stock.</p>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-orange-50 rounded border border-orange-100">
                <h3 class="font-bold">Low Stock Items</h3>
                <p class="text-3xl font-extrabold text-red-600">12</p>
            </div>
            <div class="p-4 bg-orange-50 rounded border border-orange-100">
                <h3 class="font-bold">Pending Orders</h3>
                <p class="text-3xl font-extrabold text-orange-600">5</p>
            </div>
        </div>
    </div>
@endsection