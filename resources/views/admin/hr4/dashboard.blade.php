@extends('admin.hr4.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Welcome to HR4 Dashboard</h1>
        <p class="text-lg text-gray-600">Hello, {{ Auth::user()->username }}! Here's your HR4 summary overview.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="bi bi-briefcase text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Available Jobs</h3>
                    <p class="text-gray-600">Manage job vacancies</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('hr4.job_postings.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">View Jobs →</a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="bi bi-cash-stack text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Direct Compensation</h3>
                    <p class="text-gray-600">Employee salary management</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('hr4.direct_compensation.index') }}" class="text-green-600 hover:text-green-800 font-medium">Manage Compensation →</a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="bi bi-diagram-3 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Core Human Capital</h3>
                    <p class="text-gray-600">Human resources core functions</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('hr4.core') }}" class="text-purple-600 hover:text-purple-800 font-medium">Access Core →</a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('hr4.job_postings.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-200">
                <i class="bi bi-plus-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Add New Available Job</h3>
                    <p class="text-gray-600 text-sm">Create job vacancies for HR1</p>
                </div>
            </a>
            <a href="{{ route('hr4.direct_compensation.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition duration-200">
                <i class="bi bi-calculator text-green-600 text-2xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Generate Compensation</h3>
                    <p class="text-gray-600 text-sm">Calculate employee salaries</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection