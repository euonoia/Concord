@extends('admin.hr4.layouts.app')

@section('title', 'Add Available Job - HR4 Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-8">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Add Available Job</h2>
            <p class="text-gray-600">Fill in the details to add a new available job for HR1 to fetch.</p>
        </div>

        <form method="POST" action="{{ route('hr4.job_postings.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                <select name="title" id="title" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Job Title</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->position_title }}" {{ old('title') == $pos->position_title ? 'selected' : '' }}>
                            {{ $pos->position_title }}
                        </option>
                    @endforeach
                </select>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department" id="department" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}" {{ old('department') == $dept->department_id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describe the job responsibilities..." required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="List the job requirements..." required>{{ old('requirements') }}</textarea>
                @error('requirements')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="salary_range" class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                <input type="text" name="salary_range" id="salary_range" value="{{ old('salary_range') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., $50,000 - $70,000">
                @error('salary_range')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="positions_available" class="block text-sm font-medium text-gray-700 mb-2">Number of Positions Available</label>
                <input type="number" name="positions_available" id="positions_available" value="{{ old('positions_available', 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter number of available positions" required>
                @error('positions_available')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('hr4.job_postings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center">
                    <i class="bi bi-plus-circle mr-2"></i>
                    Add Available Job
                </button>
            </div>
        </form>
    </div>
</div>
@endsection