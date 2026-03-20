@extends('admin.hr2.layouts.app')

@section('content')

<div class="hr2_show_enroll_container">
    <a href="{{ route('hr2.learning.enroll') }}" class="btn btn-sm btn-outline-secondary mb-3 shadow-sm">
        <i class="fas fa-arrow-left"></i> Back to Employee List
    </a>

    <div class="hr2_show_enroll_card">
        <div class="hr2_show_enroll_header_profile">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 font-weight-bold text-white">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                    <p class="mb-0 opacity-75">ID: {{ $employee->employee_id }} | {{ $employee->department_name }}</p>
                </div>
                <span class="badge badge-light px-3 py-2" style="color: var(--primary-base)">Profile: {{ $employee->specialization }}</span>
            </div>
        </div>
    </div>

    <div class="hr2_show_enroll_card">
        <form action="{{ route('hr2.learning.assign') }}" method="POST" id="hr2_enroll_form">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">

            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 font-weight-bold text-dark">Available Training Modules</h5>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="hr2_selectAll">
                    <label class="custom-control-label font-weight-bold text-primary" for="hr2_selectAll">Select All New Modules</label>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table hr2_show_enroll_table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="100">Action</th>
                            <th>Module Details</th>
                            <th>Learning Materials</th>
                            <th class="text-center">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $module)
                        <tr class="{{ $module->is_enrolled ? 'hr2_show_enroll_row_enrolled' : '' }}">
                            <td class="text-center align-middle">
                                @if($module->is_enrolled)
                                    <span class="text-success font-weight-bold">
                                        <i class="fas fa-check-circle"></i> Ready
                                    </span>
                                @else
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="module_codes[]" 
                                               value="{{ $module->module_code }}" 
                                               class="custom-control-input hr2_module_check" 
                                               id="chk_{{ $module->id }}">
                                        <label class="custom-control-label" for="chk_{{ $module->id }}"></label>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge border text-primary mb-1">{{ $module->module_code }}</span>
                                <div class="font-weight-bold text-dark">{{ $module->module_name }}</div>
                                <small class="text-muted">{{ $module->description }}</small>
                            </td>
                            <td>
                                @foreach($module->materials as $mat)
                                    <small class="d-block text-info">
                                        <i class="fas {{ $mat->type == 'url' ? 'fa-video' : 'fa-file-pdf' }} mr-1"></i>
                                        {{ $mat->title }}
                                    </small>
                                @endforeach
                            </td>
                            <td class="text-center align-middle font-weight-bold">
                                {{ $module->duration_hours }}h
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center p-5">No modules found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white text-right p-4 border-0">
                <button type="submit" class="hr2_show_enroll_btn_sync shadow-sm">
                    <i class="fas fa-sync-alt mr-2"></i> Sync & Assign Modules
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Select All Logic using the unique class names
    $('#hr2_selectAll').on('change', function() {
        $('.hr2_module_check').prop('checked', $(this).prop('checked'));
    });

    $('.hr2_module_check').on('change', function() {
        if (!$(this).prop('checked')) {
            $('#hr2_selectAll').prop('checked', false);
        }
    });
});
</script>
@endsection