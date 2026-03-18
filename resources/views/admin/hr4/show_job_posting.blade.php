@extends('admin.hr4.layouts.app')

@section('title', 'View Job Posting - HR4 Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Job Posting Details</h2>
            <a href="{{ route('hr4.job_postings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to Jobs
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Job Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Job Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $jobPosting->title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $jobPosting->department_name ?? $jobPosting->department }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $jobPosting->status == 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($jobPosting->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary Range</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $jobPosting->salary_range ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Positions Available</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $jobPosting->positions_available }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Posted By</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $jobPosting->poster->username ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Posted At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $jobPosting->posted_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Job Description</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPosting->description }}</p>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Requirements</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $jobPosting->requirements }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <a href="{{ route('hr4.job_postings.edit', $jobPosting) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                <i class="bi bi-pencil mr-2"></i>
                Edit Job
            </a>
            <form method="POST" action="{{ route('hr4.job_postings.destroy', $jobPosting) }}" class="inline" onsubmit="return confirm('Are you sure you want to archive this job posting?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    <i class="bi bi-archive mr-2"></i>
                    Archive Job
                </button>
            </form>
        </div>
    </div>
</div>
@endsection