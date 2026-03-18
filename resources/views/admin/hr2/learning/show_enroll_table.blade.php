@extends('admin.hr2.layouts.app')

@section('content')

<div class="hr2_show_enroll_container">
    <div class="mb-4">
        <a href="{{ route('hr2.learning.enroll') }}" class="btn btn-sm btn-white shadow-sm border text-dark">
            <i class="fas fa-arrow-left mr-1"></i> Back to Employee Directory
        </a>
    </div>

    <div class="hr2_show_enroll_card">
        <div class="hr2_show_enroll_header_gradient">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="text-uppercase small font-weight-bold opacity-75">Syncing Learning Path For</span>
                    <h3 class="mb-0 font-weight-bold">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                    <div class="mt-2">
                        <span class="mr-3"><i class="far fa-id-badge mr-1"></i> {{ $employee->employee_id }}</span>
                        <span><i class="far fa-building mr-1"></i> {{ $employee->department_name }}</span>
                    </div>
                </div>
                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                    <div class="small font-weight-bold mb-1 opacity-75">TARGET SPECIALIZATION</div>
                    <span class="badge badge-light px-3 py-2" style="font-size: 0.9rem; color: #4e73df;">
                        {{ $employee->specialization }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="hr2_show_enroll_card bg-white">
        <form action="{{ route('hr2.learning.assign') }}" method="POST" id="hr2_enrollment_form">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">

            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-0">
                <div class="hr2_show_enroll_search_wrapper flex-grow-1 mr-3 mb-2 mb-md-0">
                    <i class="fas fa-search hr2_show_enroll_search_icon"></i>
                    <input type="text" id="hr2_module_search" class="hr2_show_enroll_search_input" placeholder="Filter by module name or code...">
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="custom-control custom-switch mr-3">
                        <input type="checkbox" class="custom-control-input" id="hr2_select_all">
                        <label class="custom-control-label font-weight-bold text-primary" for="hr2_select_all" style="cursor:pointer;">Select All Available</label>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 550px;">
                <table class="table hr2_show_enroll_table mb-0" id="hr2_main_table">
                    <thead>
                        <tr>
                            <th class="text-center" width="100">Status</th>
                            <th>Module Info</th>
                            <th>Attached Materials</th>
                            <th class="text-center">Hrs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $module)
                        <tr class="hr2_module_row {{ $module->is_enrolled ? 'hr2_show_enroll_row_enrolled' : '' }}">
                            <td class="text-center align-middle">
                                @if($module->is_enrolled)
                                    <div class="text-success small font-weight-bold">
                                        <i class="fas fa-check-double fa-lg mb-1"></i><br>READY
                                    </div>
                                @else
                                    <div class="custom-control custom-checkbox custom-checkbox-lg">
                                        <input type="checkbox" name="module_codes[]" 
                                               value="{{ $module->module_code }}" 
                                               class="custom-control-input hr2_module_check" 
                                               id="chk_{{ $module->id }}">
                                        <label class="custom-control-label" for="chk_{{ $module->id }}" style="cursor:pointer;"></label>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge border text-primary small mb-1">{{ $module->module_code }}</span>
                                <div class="font-weight-bold text-dark hr2_module_name">{{ $module->module_name }}</div>
                                <div class="text-muted small">
                                    {{ Str::limit($module->description ?? 'No specific description provided.', 75) }}
                                </div>
                            </td>
                            <td class="align-middle">
                                @forelse($module->materials as $mat)
                                    <span class="hr2_show_enroll_badge_material">
                                        <i class="fas {{ $mat->type == 'url' ? 'fa-link text-danger' : 'fa-file-pdf text-info' }} mr-1"></i>
                                        {{ Str::limit($mat->title, 20) }}
                                    </span>
                                @empty
                                    <span class="text-muted small italic">Core module only</span>
                                @endforelse
                            </td>
                            <td class="text-center align-middle font-weight-bold text-primary">
                                {{ $module->duration_hours }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-layer-group fa-3x text-light mb-3"></i>
                                <p class="text-muted">No modules defined for this specialization yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="hr2_show_enroll_footer">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div id="hr2_selection_count" class="text-muted mb-3 mb-md-0 font-italic small">
                        0 modules ready for deployment
                    </div>
                    <div class="d-flex">
                        <button type="submit" id="hr2_submit_btn" class="btn btn-success hr2_show_enroll_btn_sync shadow-sm" disabled>
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Deploy to User Portal
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Modular Search Logic
    $("#hr2_module_search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#hr2_main_table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Modular Selection Logic
    function refreshEnrollmentState() {
        const count = $('.hr2_module_check:checked').length;
        $('#hr2_selection_count').text(count + ' new modules selected for deployment');
        $('#hr2_submit_btn').prop('disabled', count === 0);
        
        // Add a subtle glow to the button if items are selected
        if(count > 0) {
            $('#hr2_submit_btn').css('box-shadow', '0 0 15px rgba(28, 200, 138, 0.4)');
        } else {
            $('#hr2_submit_btn').css('box-shadow', 'none');
        }
    }

    $('#hr2_select_all').on('change', function() {
        $('.hr2_module_check').prop('checked', $(this).prop('checked'));
        refreshEnrollmentState();
    });

    $('.hr2_module_check').on('change', function() {
        if (!$(this).prop('checked')) {
            $('#hr2_select_all').prop('checked', false);
        }
        refreshEnrollmentState();
    });

    // Loading State for Sync
    $('#hr2_enrollment_form').on('submit', function() {
        $('#hr2_submit_btn').html('<i class="fas fa-spinner fa-spin mr-2"></i> Deploying Path...').prop('disabled', true);
    });
});
</script>
@endsection