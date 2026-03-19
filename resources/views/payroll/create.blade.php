@extends('admin.hr4.layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6 text-slate-800">Add Payroll</h1>
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('hr4.payroll.store') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label for="employee_id" class="block font-medium mb-1">Employee</label>
            <select name="employee_id" id="employee_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name ?? ($employee->first_name . ' ' . $employee->last_name) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="position_id" class="block font-medium mb-1">Position</label>
            <select name="position_id" id="position_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Position</option>
                @foreach(App\Models\admin\Hr\hr2\DepartmentPositionTitle::all() as $position)
                    <option value="{{ $position->id }}">{{ $position->position_title }} (₱{{ number_format($position->base_salary,2) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="salary" class="block font-medium mb-1">Salary</label>
            <input type="number" step="0.01" name="salary" id="salary" class="w-full border rounded px-3 py-2" required readonly>
            <span id="salary-info" class="text-xs text-slate-500"></span>
        </div>
        <div>
            <label class="block font-medium mb-1">Attendance Summary (Current Month)</label>
            <div id="attendance-summary" class="text-sm text-slate-600 mb-2">
                <span id="total-days">0</span> days, <span id="total-hours">0</span> hours worked.
            </div>
            <div id="attendance-logs" class="max-h-40 overflow-y-auto border rounded p-2 bg-slate-50">
                <p class="text-slate-500">Select an employee to view attendance logs.</p>
            </div>
        </div>
        <div>
            <label for="deductions" class="block font-medium mb-1">Deductions</label>
            <input type="number" step="0.01" name="deductions" id="deductions" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label for="pay_date" class="block font-medium mb-1">Pay Date</label>
            <input type="date" name="pay_date" id="pay_date" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow">Save Payroll</button>
            <a href="{{ route('hr4.payroll.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-2 rounded shadow">Back</a>
        </div>

    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employee_id');
        const salaryInput = document.getElementById('salary');
        const salaryInfo = document.getElementById('salary-info');
        const attendanceSummary = document.getElementById('attendance-summary');
        const totalDays = document.getElementById('total-days');
        const totalHours = document.getElementById('total-hours');
        const attendanceLogs = document.getElementById('attendance-logs');

        employeeSelect.addEventListener('change', function() {
            const empId = this.value;
            if (!empId) {
                salaryInput.value = '';
                salaryInfo.textContent = '';
                totalDays.textContent = '0';
                totalHours.textContent = '0';
                attendanceLogs.innerHTML = '<p class="text-slate-500">Select an employee to view attendance logs.</p>';
                document.getElementById('position_id').value = '';
                return;
            }

            // Fetch employee's position
            fetch(`/admin/hr4/payroll/get-employee-position/${empId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.position_id) {
                        document.getElementById('position_id').value = data.position_id;
                        // Trigger position change to update salary
                        document.getElementById('position_id').dispatchEvent(new Event('change'));
                    }
                });

            // Fetch attendance
            fetch(`{{ url('/admin/hr4/payroll/get-attendance') }}/${empId}`)
                .then(res => res.json())
                .then(data => {
                    totalDays.textContent = data.total_days;
                    totalHours.textContent = data.total_hours;
                    if (data.attendances.length > 0) {
                        let html = '<ul class="space-y-1">';
                        data.attendances.forEach(att => {
                            html += `<li class="text-xs">${att.date}: ${att.clock_in} - ${att.clock_out} (${att.hours}h)</li>`;
                        });
                        html += '</ul>';
                        attendanceLogs.innerHTML = html;
                    } else {
                        attendanceLogs.innerHTML = '<p class="text-slate-500">No attendance records found for this month.</p>';
                    }
                });
        });

        // When position changes, update salary
        document.getElementById('position_id').addEventListener('change', function() {
            const posId = this.value;
            if (!posId) {
                salaryInput.value = '';
                salaryInfo.textContent = '';
                return;
            }
            fetch(`/admin/hr4/payroll/get-position-salary/${posId}`)
                .then(res => res.json())
                .then(data => {
                    salaryInput.value = data.salary || '';
                    salaryInfo.textContent = data.info || '';
                });
        });
    });
    </script>
    </form>
</div>
@endsection
