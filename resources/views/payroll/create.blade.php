@extends('admin.hr4.layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&display=swap');

    :root {
        --c-bg:          #eef3f7;
        --c-surface:     #ffffff;
        --c-border:      #d4e3ee;
        --c-teal:        #0a7c6e;
        --c-teal-light:  #e4f4f1;
        --c-teal-mid:    #b8e0da;
        --c-blue:        #1a5f8a;
        --c-blue-light:  #e8f2f9;
        --c-green:       #1a7a52;
        --c-green-light: #e4f5ed;
        --c-green-mid:   #b2ddc8;
        --c-amber:       #b45309;
        --c-amber-light: #fef3c7;
        --c-purple:      #6d28d9;
        --c-purple-light:#ede9fe;
        --c-red:         #be123c;
        --c-red-light:   #fce7ef;
        --c-text:        #1b2b3a;
        --c-muted:       #5c798e;
        --c-line:        #dde8f0;
        --shadow-sm:     0 1px 4px rgba(10,50,80,.07);
        --shadow-md:     0 4px 20px rgba(10,50,80,.10);
    }

    .prc * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    .prc {
        background: var(--c-bg);
        min-height: 100vh;
        padding: 2.5rem 2rem;
    }

    /* ── Header ── */
    .prc-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1.5px solid var(--c-line);
        animation: fadeDown .45s ease both;
    }

    .prc-header h1 {
        font-family: 'Instrument Serif', serif;
        font-size: 2rem;
        color: var(--c-text);
        margin: 0;
        line-height: 1.1;
    }

    .prc-header h1 em { color: var(--c-teal); font-style: italic; }

    /* ── Layout ── */
    .prc-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1.5rem;
        align-items: start;
        animation: fadeUp .5s .1s ease both;
    }

    @media (max-width: 900px) {
        .prc-layout { grid-template-columns: 1fr; }
    }

    /* ── Card ── */
    .prc-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
    }

    .prc-card + .prc-card { margin-top: 1.25rem; }

    .card-section-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: 1.25rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid var(--c-line);
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .card-section-title i { color: var(--c-teal); font-size: .85rem; }

    /* ── Form fields ── */
    .form-group { margin-bottom: 1.25rem; }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
        display: block;
        font-size: .8rem;
        font-weight: 600;
        color: var(--c-text);
        margin-bottom: .45rem;
        letter-spacing: .01em;
    }

    .form-label .req { color: var(--c-red); margin-left: .2rem; }

    .form-control {
        width: 100%;
        padding: .6rem .9rem;
        border: 1.5px solid var(--c-border);
        border-radius: 9px;
        font-size: .85rem;
        color: var(--c-text);
        background: var(--c-surface);
        outline: none;
        font-family: 'DM Sans', sans-serif;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .form-control:focus {
        border-color: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(10,124,110,.1);
    }

    .form-control:read-only,
    .form-control[readonly] {
        background: var(--c-bg);
        color: var(--c-muted);
        cursor: not-allowed;
    }

    .form-hint {
        font-size: .75rem;
        color: var(--c-muted);
        margin-top: .35rem;
    }

    .form-hint.success { color: var(--c-green); font-weight: 600; }
    .form-hint.error   { color: var(--c-red);   font-weight: 600; }
    .form-hint.loading { color: var(--c-amber);  font-weight: 600; }

    /* ── Attendance grid ── */
    .attendance-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .75rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 600px) {
        .attendance-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .att-stat {
        background: var(--c-bg);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: .85rem .75rem;
        text-align: center;
        transition: transform .2s ease;
    }

    .att-stat:hover { transform: translateY(-2px); }

    .att-stat-label {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--c-muted);
        margin-bottom: .4rem;
    }

    .att-stat-value {
        font-family: 'Instrument Serif', serif;
        font-size: 1.7rem;
        line-height: 1;
        color: var(--c-text);
    }

    .att-stat-value.amber  { color: var(--c-amber); }
    .att-stat-value.purple { color: var(--c-purple); }

    .att-stat-value sup {
        font-family: 'DM Sans', sans-serif;
        font-size: .65rem;
        font-weight: 600;
        color: var(--c-muted);
        vertical-align: super;
    }

    /* ── Attendance log ── */
    .att-log {
        max-height: 160px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .att-log-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .45rem 0;
        border-bottom: 1px solid var(--c-line);
        font-size: .78rem;
        color: var(--c-text);
    }

    .att-log-item:last-child { border-bottom: none; }
    .att-log-empty { font-size: .8rem; color: var(--c-muted); text-align: center; padding: .75rem 0; }

    /* ── Sticky sidebar ── */
    .prc-sidebar { position: sticky; top: 1.5rem; }

    /* ── Summary card ── */
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .6rem 0;
        font-size: .85rem;
        border-bottom: 1px solid var(--c-line);
        color: var(--c-text);
    }

    .summary-row:last-child { border-bottom: none; }
    .summary-row .label { color: var(--c-muted); }

    .summary-row.total {
        padding-top: .85rem;
        margin-top: .1rem;
        border-top: 1.5px solid var(--c-line);
        border-bottom: none;
    }

    .summary-row.total .label {
        font-weight: 700;
        color: var(--c-text);
        font-size: .9rem;
    }

    .net-pay-display {
        font-family: 'Instrument Serif', serif;
        font-size: 1.6rem;
        color: var(--c-green);
        line-height: 1;
    }

    /* ── Buttons ── */
    .btn-group { display: flex; gap: .75rem; margin-top: 1.25rem; }

    .btn {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        font-size: .85rem;
        font-weight: 600;
        padding: .7rem 1.2rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, opacity .2s ease;
        font-family: 'DM Sans', sans-serif;
    }

    .btn:hover:not(:disabled) { transform: translateY(-2px); text-decoration: none; }
    .btn:disabled { opacity: .45; cursor: not-allowed; }

    .btn-teal {
        background: var(--c-teal);
        color: #fff;
        box-shadow: 0 2px 8px rgba(10,124,110,.25);
    }

    .btn-teal:hover:not(:disabled) {
        background: #0b9483;
        box-shadow: 0 4px 14px rgba(10,124,110,.35);
        color: #fff;
    }

    .btn-cancel {
        background: var(--c-bg);
        color: var(--c-muted);
        border: 1.5px solid var(--c-border);
    }

    .btn-cancel:hover { background: #dce6ed; color: var(--c-text); }

    /* ── Deduction info ── */
    .deduction-info-box {
        display: none;
        margin-top: .5rem;
        padding: .55rem .85rem;
        background: var(--c-blue-light);
        border: 1px solid #c0d9ee;
        border-radius: 8px;
        font-size: .78rem;
        color: var(--c-blue);
    }

    .deduction-info-box.show { display: block; }

    /* Salary Breakdown */
    .salary-breakdown {
        display: flex;
        flex-direction: column;
        gap: .5rem;
    }
    .salary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .5rem .75rem;
        background: var(--c-gray-light);
        border-radius: 6px;
        font-size: .9rem;
    }
    .salary-row.total {
        background: var(--c-teal-light);
        font-weight: 600;
        margin-top: .25rem;
    }
    .salary-row .label { color: var(--c-text); }
    .salary-row.total .label { color: var(--c-teal); }

    /* ── Animations ── */
    @keyframes fadeUp   { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    @keyframes fadeDown { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
</style>

<div class="prc">

    <div class="prc-header">
        <h1>Create <em>Payroll Entry</em></h1>
    </div>

    <form action="{{ route('hr4.payroll.store') }}" method="POST" id="payrollForm">
        @csrf

        <div class="prc-layout">

            {{-- ── LEFT COLUMN ── --}}
            <div>

                {{-- Employee Info --}}
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-person-badge"></i> Employee Information
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="employee_id">Employee <span class="req">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}">
                                    {{ $employee->first_name ?? '' }} {{ $employee->last_name ?? '' }} (ID: {{ $employee->employee_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" id="position_display" class="form-control" readonly placeholder="Auto-filled when employee selected">
                        <input type="hidden" name="position_id" id="position_id">
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Monthly Salary</label>
                        <input type="text" id="salary_display" class="form-control" readonly placeholder="Auto-filled when employee selected">
                        <input type="hidden" name="salary" id="salary">
                        <p id="salary-status" class="form-hint"></p>
                    </div>
                </div>

                {{-- Attendance --}}
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-calendar-check"></i> Attendance Summary
                    </div>

                    <div class="attendance-grid">
                        <div class="att-stat">
                            <div class="att-stat-label">Days Worked</div>
                            <div class="att-stat-value" id="total-days">0</div>
                        </div>
                        <div class="att-stat">
                            <div class="att-stat-label">Hours Worked</div>
                            <div class="att-stat-value" id="total-hours">0<sup>h</sup></div>
                        </div>
                        <div class="att-stat">
                            <div class="att-stat-label">Overtime</div>
                            <div class="att-stat-value amber" id="overtime-hours">0</div>
                        </div>
                        <div class="att-stat">
                            <div class="att-stat-label">Night Diff</div>
                            <div class="att-stat-value purple" id="night-diff-hours">0</div>
                        </div>
                    </div>

                    <div class="att-log" id="attendanceLogs">
                        <p class="att-log-empty">Select an employee to view attendance details.</p>
                    </div>

                    <input type="hidden" name="worked_hours"    id="worked_hours_hidden"    value="0">
                    <input type="hidden" name="overtime_hours"  id="overtime_hours_hidden"  value="0">
                    <input type="hidden" name="night_diff_hours" id="night_diff_hours_hidden" value="0">
                </div>

                {{-- Salary Computation --}}
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-calculator"></i> Salary Computation
                    </div>

                    <div class="salary-breakdown">
                        <div class="salary-row">
                            <span class="label">Base Salary</span>
                            <span id="base-salary">₱0.00</span>
                        </div>
                        <div class="salary-row">
                            <span class="label">Shift Allowance</span>
                            <span id="shift-allowance">₱0.00</span>
                        </div>
                        <div class="salary-row">
                            <span class="label">Overtime Pay</span>
                            <span id="overtime-pay">₱0.00</span>
                        </div>
                        <div class="salary-row">
                            <span class="label">Night Diff Pay</span>
                            <span id="night-diff-pay">₱0.00</span>
                        </div>
                        <div class="salary-row">
                            <span class="label">Bonus</span>
                            <span id="bonus">₱0.00</span>
                        </div>
                        <div class="salary-row">
                            <span class="label">Training Reward</span>
                            <span id="training-reward">₱0.00</span>
                        </div>
                        <div class="salary-row total">
                            <span class="label">Total Compensation</span>
                            <span id="total-compensation">₱0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Deductions --}}
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-dash-circle"></i> Deductions
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="deduction_type">Deduction Type</label>
                        <select name="deduction_type" id="deduction_type" class="form-control">
                            <option value="">Select Deduction Type (Optional)</option>
                            <option value="tax">Tax Withholding</option>
                            <option value="sss">SSS Contribution</option>
                            <option value="philhealth">PhilHealth Premium</option>
                            <option value="pagibig">PAG-IBIG Contribution</option>
                            <option value="insurance">Insurance</option>
                            <option value="loan">Loan</option>
                            <option value="other">Other</option>
                        </select>
                        <div id="deduction-info" class="deduction-info-box"></div>
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="deductions">Deduction Amount</label>
                        <input type="number" name="deductions" id="deductions" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                {{-- Pay Date --}}
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-calendar-date"></i> Pay Date
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="pay_date">Pay Date <span class="req">*</span></label>
                        <input type="date" name="pay_date" id="pay_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN (sticky sidebar) ── --}}
            <div class="prc-sidebar">
                <div class="prc-card">
                    <div class="card-section-title">
                        <i class="bi bi-receipt"></i> Payment Summary
                    </div>

                    <div class="summary-row">
                        <span class="label">Base Salary</span>
                        <span id="summary-salary" style="font-weight:600">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Deductions</span>
                        <span id="summary-deductions" style="color:var(--c-red); font-weight:600">−₱0.00</span>
                    </div>
                    <div class="summary-row total">
                        <span class="label">Net Pay</span>
                        <span class="net-pay-display" id="summary-net">₱0.00</span>
                    </div>

                    <div class="btn-group">
                        <button type="submit" id="submitBtn" class="btn btn-teal" disabled>
                            <i class="bi bi-check-circle"></i> Create Entry
                        </button>
                        <a href="{{ route('hr4.payroll.index') }}" class="btn btn-cancel">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form           = document.getElementById('payrollForm');
    const employeeSelect = document.getElementById('employee_id');
    const positionHidden = document.getElementById('position_id');
    const positionDisplay= document.getElementById('position_display');
    const salaryHidden   = document.getElementById('salary');
    const salaryDisplay  = document.getElementById('salary_display');
    const salaryStatus   = document.getElementById('salary-status');
    const deductionType  = document.getElementById('deduction_type');
    const deductionsInput= document.getElementById('deductions');
    const deductionInfo  = document.getElementById('deduction-info');
    const attendanceLogs = document.getElementById('attendanceLogs');
    const submitBtn      = document.getElementById('submitBtn');

    let salaryLoaded = false;

    const deductionDescriptions = {
        tax:        'Withholding tax based on salary',
        sss:        'Social Security System contribution (₱1,125/month avg)',
        philhealth: 'PhilHealth premium contribution (₱200–300/month)',
        pagibig:    'PAG-IBIG housing contribution (₱100/month)',
        insurance:  'Employee insurance deduction',
        loan:       'Employee loan installment',
        other:      'Other deductions'
    };

    deductionType.addEventListener('change', function () {
        const desc = deductionDescriptions[this.value];
        deductionInfo.textContent = desc || '';
        deductionInfo.classList.toggle('show', !!desc);
    });

    function fmt(val) {
        return '₱' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateSummary() {
        const salary     = parseFloat(salaryHidden.value) || 0;
        const deductions = parseFloat(deductionsInput.value) || 0;
        const net        = salary - deductions;
        document.getElementById('summary-salary').textContent     = fmt(salary);
        document.getElementById('summary-deductions').textContent = '−' + fmt(deductions);
        document.getElementById('summary-net').textContent        = fmt(net);
    }

    salaryHidden.addEventListener('change', updateSummary);
    deductionsInput.addEventListener('input', updateSummary);

    form.addEventListener('submit', function (e) {
        const salary = parseFloat(salaryHidden.value) || 0;
        if (!salaryLoaded || salary === 0) {
            e.preventDefault();
            alert('⚠️ Please select an employee and wait for the salary to load before submitting.');
        }
    });

    function resetForm() {
        positionDisplay.value = '';
        positionHidden.value  = '';
        salaryDisplay.value   = '';
        salaryHidden.value    = '';
        salaryStatus.textContent = '';
        salaryStatus.className   = 'form-hint';
        document.getElementById('total-days').textContent          = '0';
        document.getElementById('total-hours').innerHTML           = '0<sup>h</sup>';
        document.getElementById('overtime-hours').textContent      = '0';
        document.getElementById('night-diff-hours').textContent    = '0';
        document.getElementById('base-salary').textContent         = '₱0.00';
        document.getElementById('shift-allowance').textContent     = '₱0.00';
        document.getElementById('overtime-pay').textContent        = '₱0.00';
        document.getElementById('night-diff-pay').textContent      = '₱0.00';
        document.getElementById('bonus').textContent               = '₱0.00';
        document.getElementById('training-reward').textContent     = '₱0.00';
        document.getElementById('total-compensation').textContent  = '₱0.00';
        document.getElementById('worked_hours_hidden').value       = '0';
        document.getElementById('overtime_hours_hidden').value     = '0';
        document.getElementById('night_diff_hours_hidden').value   = '0';
        attendanceLogs.innerHTML = '<p class="att-log-empty">Select an employee to view attendance details.</p>';
        deductionType.value      = '';
        deductionsInput.value    = '';
        deductionInfo.textContent= '';
        deductionInfo.classList.remove('show');
        salaryLoaded  = false;
        submitBtn.disabled = true;
        updateSummary();
    }

    employeeSelect.addEventListener('change', function () {
        const empId = this.value;
        resetForm();
        if (!empId) return;

        salaryStatus.textContent = '⏳ Loading salary…';
        salaryStatus.className   = 'form-hint loading';

        fetch(`/admin/hr4/payroll/get-employee-position/${empId}`)
            .then(res => { if (!res.ok) throw new Error(`HTTP ${res.status}`); return res.json(); })
            .then(data => {
                if (data.position_id) {
                    positionHidden.value  = data.position_id;
                    positionDisplay.value = data.position_title || 'N/A';
                }
                if (data.salary && data.salary > 0) {
                    salaryHidden.value   = data.salary;
                    salaryDisplay.value  = '₱ ' + parseFloat(data.salary).toLocaleString('en-PH', { minimumFractionDigits: 2 });
                    salaryStatus.textContent = '✓ Salary loaded';
                    salaryStatus.className   = 'form-hint success';
                    salaryLoaded  = true;
                    submitBtn.disabled = false;
                } else {
                    salaryDisplay.value  = '₱ 0.00';
                    salaryStatus.textContent = '✗ No salary data found';
                    salaryStatus.className   = 'form-hint error';
                    salaryHidden.value  = 0;
                    submitBtn.disabled  = true;
                }
                updateSummary();
            })
            .catch(() => {
                salaryStatus.textContent = '✗ Error loading data';
                salaryStatus.className   = 'form-hint error';
                submitBtn.disabled = true;
            });

        fetch(`/admin/hr4/payroll/get-attendance/${empId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('total-days').textContent       = data.total_days || 0;
                document.getElementById('total-hours').innerHTML        = (data.total_hours || 0) + '<sup>h</sup>';
                document.getElementById('overtime-hours').textContent   = parseFloat(data.overtime_hours || 0).toFixed(2);
                document.getElementById('night-diff-hours').textContent = parseFloat(data.night_diff_hours || 0).toFixed(2);

                // Update salary breakdown
                document.getElementById('base-salary').textContent = '₱' + (data.base_salary ? parseFloat(data.base_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('shift-allowance').textContent = '₱' + (data.shift_allowance ? parseFloat(data.shift_allowance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('overtime-pay').textContent = '₱' + (data.overtime_pay ? parseFloat(data.overtime_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('night-diff-pay').textContent = '₱' + (data.night_diff_pay ? parseFloat(data.night_diff_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('bonus').textContent = '₱' + (data.bonus ? parseFloat(data.bonus).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('training-reward').textContent = '₱' + (data.training_reward ? parseFloat(data.training_reward).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                document.getElementById('total-compensation').textContent = '₱' + (data.total_compensation ? parseFloat(data.total_compensation).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');

                // Update salary display and hidden field
                salaryDisplay.value = '₱' + (data.total_compensation ? parseFloat(data.total_compensation).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
                salaryHidden.value = data.total_compensation || 0;

                // Update payment summary
                document.getElementById('summary-salary').textContent = '₱' + (data.total_compensation ? parseFloat(data.total_compensation).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');

                document.getElementById('worked_hours_hidden').value    = data.worked_hours || 0;
                document.getElementById('overtime_hours_hidden').value  = data.overtime_hours || 0;
                document.getElementById('night_diff_hours_hidden').value= data.night_diff_hours || 0;

                if (data.attendances && data.attendances.length > 0) {
                    attendanceLogs.innerHTML = data.attendances.map(att => `
                        <div class="att-log-item">
                            <span><strong>${att.date}</strong> &nbsp; ${att.clock_in} – ${att.clock_out}</span>
                            <span style="color:var(--c-muted)">${att.hours}h</span>
                        </div>`).join('');
                } else {
                    attendanceLogs.innerHTML = '<p class="att-log-empty">No attendance records found for this month.</p>';
                }
            })
            .catch(err => console.error('Attendance fetch error:', err));
    });
});
</script>

@endsection