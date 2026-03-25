<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Please run SampleEmployeesSeeder first.');
            return;
        }

        // Generate payroll records for the last 6 months
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $payrollData = [];

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Skip if payroll already exists
                $existingPayroll = Payroll::where('employee_id', $employee->id)
                    ->whereYear('pay_date', $currentDate->year)
                    ->whereMonth('pay_date', $currentDate->month)
                    ->first();

                if (!$existingPayroll) {
                    // Generate realistic salary based on position (if available)
                    $baseSalary = 30000; // Default base salary
                    if ($employee->position && $employee->position->base_salary) {
                        $baseSalary = $employee->position->base_salary;
                    }

                    // Add some variation (±10%)
                    $salaryVariation = $baseSalary * (0.9 + (mt_rand(0, 20) / 100));
                    $salary = round($salaryVariation, 2);

                    // Calculate deductions (taxes, insurance, etc.) - roughly 15-25%
                    $deductionPercentage = 0.15 + (mt_rand(0, 10) / 100);
                    $deductions = round($salary * $deductionPercentage, 2);

                    $netPay = $salary - $deductions;

                    $payrollData[] = [
                        'employee_id' => $employee->id,
                        'salary' => $salary,
                        'deductions' => $deductions,
                        'net_pay' => $netPay,
                        'pay_date' => $currentDate->copy()->endOfMonth(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $currentDate->addMonth();
            }
        }

        // Insert payroll records in chunks to avoid memory issues
        foreach (array_chunk($payrollData, 100) as $chunk) {
            Payroll::insert($chunk);
        }

        $this->command->info('Payroll records seeded successfully for ' . count($employees) . ' employees.');
    }
}