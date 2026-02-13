@extends('layouts.app')
@section('title', 'HR Subsystem')
@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md border-l-8 border-purple-500">
        <h1 class="text-2xl font-bold text-purple-800">HR & Payroll Subsystem</h1>
        <p class="text-gray-600 mt-2">Welcome, {{ Auth::user()->username }}. You have administrative access to employee records.</p>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-purple-50 rounded border border-purple-100">
                <h3 class="font-bold">Total Staff</h3>
                <p class="text-3xl font-extrabold">124</p>
            </div>
            <div class="p-4 bg-purple-50 rounded border border-purple-100">
                <h3 class="font-bold">Pending Leaves</h3>
                <p class="text-3xl font-extrabold">8</p>
            </div>
        </div>
    </div>
@endsection