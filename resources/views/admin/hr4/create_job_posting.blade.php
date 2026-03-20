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
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
            @csrf

            <div>
                <label for="dept_code" class="block text-sm font-medium text-gray-700 mb-2">Department Code</label>
                <select name="dept_code" id="dept_code" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Select Department Code</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->dept_code }}" {{ old('dept_code') == $dept->dept_code ? 'selected' : '' }}>
                            {{ $dept->dept_code }} - {{ $dept->specialization_name }}
                        </option>
                    @endforeach
                </select>
                @error('dept_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="specialization_name" class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                <select name="specialization_name" id="specialization_name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Select Position First</option>
                    @foreach($specializations as $spec)
                        <option value="{{ $spec->specialization_name }}" {{ old('specialization_name') == $spec->specialization_name ? 'selected' : '' }}>
                            {{ $spec->specialization_name }}
                        </option>
                    @endforeach
                </select>
                @error('specialization_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <select name="position_id" id="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Select Position</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" data-salary="{{ $pos->base_salary }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                            {{ $pos->position_title }}
                        </option>
                    @endforeach
                </select>
                @error('position_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <script>
            document.getElementById('position_id').addEventListener('change', function() {
                const positionId = this.value;
                const specSelect = document.getElementById('specialization_name');
                
                if (!positionId) {
                    specSelect.innerHTML = '<option value="">Select Position First</option>';
                    return;
                }

                specSelect.innerHTML = '<option value="">Loading specializations...</option>';

                fetch(`/admin/hr4/job-postings/${positionId}/specializations`)
                    .then(res => res.json())
                    .then(data => {
                        specSelect.innerHTML = '<option value="">Select Specialization</option>';
                        if (data.length === 0) {
                            specSelect.innerHTML += '<option value="" disabled>No specializations available</option>';
                        } else {
                            data.forEach(spec => {
                                const opt = document.createElement('option');
                                opt.value = spec.specialization_name;
                                opt.textContent = spec.specialization_name;
                                specSelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error loading specializations:', err);
                        specSelect.innerHTML = '<option value="">Error loading specializations</option>';
                    });
            });

            document.getElementById('specialization_name').addEventListener('change', function() {
                const specName = this.value;
                const positionId = document.getElementById('position_id').value;
                const competencySelect = document.getElementById('competency_code');

                if (!specName || !positionId) {
                    competencySelect.innerHTML = '<option value="">Select Specialization & Position First</option>';
                    competencySelect.disabled = true;
                    return;
                }

                competencySelect.innerHTML = '<option value="">Loading competencies...</option>';
                competencySelect.disabled = false;
                
                fetch(`/admin/hr4/job-postings/competencies?specialization=${encodeURIComponent(specName)}&position_id=${positionId}`)
                    .then(res => res.json())
                    .then(data => {
                        competencySelect.innerHTML = '<option value="">Select Competency</option>';
                        if (data.length === 0) {
                            competencySelect.innerHTML += '<option value="" disabled>No competencies available</option>';
                        } else {
                            data.forEach(comp => {
                                const opt = document.createElement('option');
                                opt.value = comp.competency_code;
                                opt.textContent = comp.competency_code + ' - ' + comp.description;
                                competencySelect.appendChild(opt);
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error loading competencies:', err);
                        competencySelect.innerHTML = '<option value="">Error loading competencies</option>';
                    });
            });
            </script>

            <div>
                <label for="salary_range" class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                <input type="text" name="salary_range" id="salary_range" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('salary_range') }}" placeholder="e.g., $50,000 - $70,000">
                @error('salary_range')
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
                <label for="competency_code" class="block text-sm font-medium text-gray-700 mb-2">Requirements (Competency)</label>
                <select name="competency_code" id="competency_code" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                    <option value="">Select Specialization & Position First</option>
                </select>
                @error('competency_code')
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