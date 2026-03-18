<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
use App\Models\admin\Hr\hr3\Shift;

class SyncCompensationWithHR1 extends Command
{
    protected $signature = 'compensation:sync-hr1 {month?}';
    protected $description = 'Sync compensation records with latest HR1 training data for all employees';

    public function handle()
    {
        $month = $this->argument('month') ?? date('Y-m');
        $employees = Employee::all();
        $updated = 0;

        foreach ($employees as $emp) {
            $position = DepartmentPositionTitle::find($emp->position_id);
            $base_salary = $position->base_salary ?? 0;
            $shift_allowance = Shift::calculateMonthlyShiftAllowance($emp->employee_id, $month);
            $bonus = 0;

            DirectCompensation::updateOrCreate(
                ['employee_id' => $emp->employee_id, 'month' => $month],
                [
                    'base_salary' => $base_salary,
                    'shift_allowance' => $shift_allowance,
                    'bonus' => $bonus,
                ]
            );
            $updated++;
        }
        $this->info("Compensation sync complete for {$month}. Updated: {$updated} records.");
    }
}
