@extends('admin.hr4.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold mb-6 text-slate-800">Create Payroll Entry</h1>

    <form action="{{ route('hr4.payroll.store') }}" method="POST" id="payrollForm">
        @csrf

        <!-- Employee Selection -->
        <div class="mb-6">
            <label for="employee_id" class="block font-medium mb-2 text-slate-700">
                Employee <span class="text-red-500">*</span>
            </label>
            <select name="employee_id" id="employee_id" class="w-full border rounded px-3 py-2 bg-white" required>
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->employee_id }}">{{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }} (ID: {{ $employee->employee_id }})</option>
                @endforeach
            </select>
        </div>

        <!-- Position (Read-only) -->
        <div class="mb-6">
            <label for="position_id" class="block font-medium mb-2 text-slate-700">Position</label>
            <input type="text" id="position_display" class="w-full border rounded px-3 py-2 bg-slate-100 text-slate-600" readonly placeholder="Auto-filled when employee selected">
            <input type="hidden" name="position_id" id="position_id">
        </div>

        <!-- Salary (Read-only) -->
        <div class="mb-6">
            <label for="salary" class="block font-medium mb-2 text-slate-700">Monthly Salary</label>
            <input type="text" id="salary_display" class="w-full border rounded px-3 py-2 bg-slate-100 text-slate-600" readonly placeholder="Auto-filled when employee selected">
            <input type="hidden" name="salary" id="salary">
            <p id="salary-status" class="text-xs text-slate-500 mt-1"></p>
        </div>

        <!-- Attendance Summary -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-slate-800 mb-4">Attendance Summary</h3>
            <div class="grid grid-cols-4 gap-4">
                <div class="bg-white p-3 rounded border border-slate-200">
                    <p class="text-xs text-slate-500 font-medium">Days Worked</p>
                    <p id="total-days" class="text-2xl font-bold text-slate-800">0</p>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <p class="text-xs text-slate-500 font-medium">Hours Worked</p>
                    <p id="total-hours" class="text-2xl font-bold text-slate-800">0<span class="text-sm">h</span></p>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <p class="text-xs text-slate-500 font-medium">Overtime Hours</p>
                    <p id="overtime-hours" class="text-2xl font-bold text-orange-600">0</p>
                </div>
                <div class="bg-white p-3 rounded border border-slate-200">
                    <p class="text-xs text-slate-500 font-medium">Night Diff Hours</p>
                    <p id="night-diff-hours" class="text-2xl font-bold text-purple-600">0</p>
                </div>
            </div>
            <div id="attendanceLogs" class="mt-4 text-sm text-slate-600">
                <p class="text-slate-500 text-sm">Select an employee to view attendance details.</p>
            </div>
        </div>

        <!-- Hidden inputs for hours -->
        <input type="hidden" name="worked_hours" id="worked_hours_hidden" value="0">
        <input type="hidden" name="overtime_hours" id="overtime_hours_hidden" value="0">
        <input type="hidden" name="night_diff_hours" id="night_diff_hours_hidden" value="0">

        <!-- Deduction Type -->
        <div class="mb-6">
            <label for="deduction_type" class="block font-medium mb-2 text-slate-700">Deduction Type</label>
            <select name="deduction_type" id="deduction_type" class="w-full border rounded px-3 py-2">
                <option value="">Select Deduction Type (Optional)</option>
                <option value="tax">Tax Withholding</option>
                <option value="sss">SSS Contribution</option>
                <option value="philhealth">PhilHealth Premium</option>
                <option value="pagibig">PAG-IBIG Contribution</option>
                <option value="insurance">Insurance</option>
                <option value="loan">Loan</option>
                <option value="other">Other</option>
            </select>
            <p id="deduction-info" class="text-xs text-slate-500 mt-1"></p>
        </div>

        <!-- Deductions -->
        <div class="mb-6">
            <label for="deductions" class="block font-medium mb-2 text-slate-700">Deductions Amount</label>
            <input type="number" name="deductions" id="deductions" class="w-full border rounded px-3 py-2" step="0.01" min="0" placeholder="0.00">
        </div>

        <!-- Pay Date -->
        <div class="mb-6">
            <label for="pay_date" class="block font-medium mb-2 text-slate-700">Pay Date <span class="text-red-500">*</span></label>
            <input type="date" name="pay_date" id="pay_date" class="w-full border rounded px-3 py-2" required value="{{ date('Y-m-d') }}">
        </div>

        <!-- Payment Summary -->
        <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <h3 class="font-semibold text-slate-800 mb-3">Payment Summary</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Base Salary:</span>
                    <span id="summary-salary" class="font-semibold text-slate-800">0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Deductions:</span>
                    <span id="summary-deductions" class="font-semibold text-slate-800">0.00</span>
                </div>
                <div class="border-t border-green-200 pt-2 flex justify-between">
                    <span class="text-slate-700 font-semibold">Net Pay:</span>
                    <span id="summary-net" class="font-bold text-green-600 text-lg">0.00</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-3">
            <button type="submit" id="submitBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition disabled:bg-slate-400 disabled:cursor-not-allowed" disabled>
                Create Payroll Entry
            </button>
            <a href="{{ route('hr4.payroll.index') }}" class="flex-1 bg-slate-300 hover:bg-slate-400 text-slate-800 font-semibold py-3 rounded-lg text-center transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('payrollForm');
        const employeeSelect = document.getElementById('employee_id');
        const positionHidden = document.getElementById('position_id');
        const positionDisplay = document.getElementById('position_display');
        const salaryHidden = document.getElementById('salary');
        const salaryDisplay = document.getElementById('salary_display');
        const salaryStatus = document.getElementById('salary-status');
        const deductionType = document.getElementById('deduction_type');
        const deductionsInput = document.getElementById('deductions');
        const deductionInfo = document.getElementById('deduction-info');
        const attendanceLogs = document.getElementById('attendanceLogs');
        const submitBtn = document.getElementById('submitBtn');
        
        let salaryLoaded = false;

        const deductionDescriptions = {
            'tax': 'Withholding tax based on salary',
            'sss': 'Social Security System contribution (₱1,125/month avg)',
            'philhealth': 'PhilHealth premium contribution (₱200-300/month)',
            'pagibig': 'PAG-IBIG housing contribution (₱100/month)',
            'insurance': 'Employee insurance deduction',
            'loan': 'Employee loan installment',
            'other': 'Other deductions'
        };

        deductionType.addEventListener('change', function() {
            deductionInfo.textContent = deductionDescriptions[this.value] || '';
        });

        function updateSummary() {
            const salary = parseFloat(salaryHidden.value) || 0;
            const deductions = parseFloat(deductionsInput.value) || 0;
            const net = salary - deductions;

            document.getElementById('summary-salary').textContent = salary.toFixed(2);
            document.getElementById('summary-deductions').textContent = deductions.toFixed(2);
            document.getElementById('summary-net').textContent = net.toFixed(2);
        }

        salaryHidden.addEventListener('change', updateSummary);
        deductionsInput.addEventListener('input', updateSummary);

        // Validate before submission
        form.addEventListener('submit', function(e) {
            const salary = parseFloat(salaryHidden.value) || 0;
            
            if (!salaryLoaded || salary === 0) {
                e.preventDefault();
                alert('⚠️ Wait for salary to load!\n\n1. Select employee\n2. Wait for salary to appear\n3. Then submit');
                return false;
            }
            return true;
        });

        employeeSelect.addEventListener('change', function() {
            const empId = this.value;
            salaryLoaded = false;
            submitBtn.disabled = true;
            
            if (!empId) {
                positionDisplay.value = '';
                salaryDisplay.value = '';
                salaryStatus.textContent = '';
                salaryHidden.value = '';
                document.getElementById('total-days').textContent = '0';
                document.getElementById('total-hours').textContent = '0';
                document.getElementById('overtime-hours').textContent = '0';
                document.getElementById('night-diff-hours').textContent = '0';
                attendanceLogs.innerHTML = '<p class="text-slate-500 text-sm">Select an employee to view attendance details.</p>';
                deductionType.value = '';
                deductionsInput.value = '';
                deductionInfo.textContent = '';
                updateSummary();
                return;
            }

            salaryStatus.textContent = '⏳ Loading...';
            salaryStatus.className = 'text-xs text-slate-500 mt-1';

            fetch(`/admin/hr4/payroll/get-employee-position/${empId}`)
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    if (data.position_id) {
                        positionHidden.value = data.position_id;
                        positionDisplay.value = data.position_title || 'N/A';
                    }
                    
                    if (data.salary && data.salary > 0) {
                        salaryHidden.value = data.salary;
                        const salary = parseFloat(data.salary);
                        const formatted = salary.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        salaryDisplay.value = '₱ ' + formatted;
                        salaryStatus.textContent = '✓ Loaded';
                        salaryStatus.className = 'text-xs text-green-600 mt-1 font-semibold';
                        updateSummary();
                        salaryLoaded = true;
                        submitBtn.disabled = false;
                    } else {
                        salaryDisplay.value = '₱ 0.00';
                        salaryStatus.textContent = '✗ No salary data';
                        salaryStatus.className = 'text-xs text-red-600 mt-1 font-semibold';
                        salaryHidden.value = 0;
                        submitBtn.disabled = true;
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    salaryStatus.textContent = '✗ Error loading data';
                    salaryStatus.className = 'text-xs text-red-600 mt-1 font-semibold';
                    submitBtn.disabled = true;
                });

            fetch(`/admin/hr4/payroll/get-attendance/${empId}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('total-days').textContent = data.total_days || 0;
                    document.getElementById('total-hours').textContent = data.total_hours || 0;
                    document.getElementById('overtime-hours').textContent = (data.overtime_hours || 0).toFixed(2);
                    document.getElementById('night-diff-hours').textContent = (data.night_diff_hours || 0).toFixed(2);

                    document.getElementById('worked_hours_hidden').value = data.worked_hours || 0;
                    document.getElementById('overtime_hours_hidden').value = data.overtime_hours || 0;
                    document.getElementById('night_diff_hours_hidden').value = data.night_diff_hours || 0;

                    if (data.attendances && data.attendances.length > 0) {
                        let html = '<div class="space-y-2">';
                        data.attendances.forEach(att => {
                            html += `<div class="text-xs text-slate-600 pb-2 border-b border-slate-200">
                                <span class="font-medium">${att.date}</span>: ${att.clock_in} - ${att.clock_out}
                                <span class="text-slate-500">(${att.hours}h)</span>
                            </div>`;
                        });
                        html += '</div>';
                        attendanceLogs.innerHTML = html;
                    } else {
                        attendanceLogs.innerHTML = '<p class="text-slate-500 text-sm">No attendance records found for this month.</p>';
                    }
                })
                .catch(err => console.error('Error fetching attendance:', err));
        });
    });
</script>
@endsection

