@extends('admin.hr2.layouts.app')

@section('content')

<div class="hr2_enroll_table_wrapper">
    <header class="hr2_enroll_table_page_header">
        <h1>Enroll Employee</h1>
    </header>

    <div class="hr2_enroll_table_card">
        <div class="hr2_enroll_table_controls">
            <div style="position: relative; width: 100%;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 12px; color: var(--neutral-400);"></i>
                <input type="text" id="hr2_emp_search" class="hr2_enroll_table_search_input" placeholder="Find employee by name, ID, or specialization...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="hr2_enroll_table_main" id="hr2_main_employee_table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Department & Specialization</th>
                        <th class="text-center">Portal Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $emp)
                    <tr class="hr2_enroll_table_row">
                        <td>
                            <span class="hr2_enroll_table_emp_id">{{ $emp->employee_id }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--primary-base);">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                            <small class="text-light">Active Personnel</small>
                        </td>
                        <td>
                            <div style="font-size: 14px; margin-bottom: 4px; color: var(--neutral-700);">
                                <i class="fas fa-hospital-user mr-1 text-accent"></i> {{ $emp->department_name ?? 'General Staff' }}
                            </div>
                            <span class="hr2_enroll_table_spec_badge">
                                {{ $emp->specialization }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('hr2.learning.show_enroll', $emp->id) }}" class="hr2_enroll_table_btn_action shadow-sm">
                                <i class="fas fa-sync-alt"></i> Select Modules
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Modular Search for Employee Table
    $("#hr2_emp_search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#hr2_main_employee_table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
@endsection