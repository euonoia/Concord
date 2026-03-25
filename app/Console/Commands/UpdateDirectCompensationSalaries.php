<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\Employee;

class UpdateDirectCompensationSalaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-direct-compensation-salaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update DirectCompensation records with 0 salary to use position base_salary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating DirectCompensation records with 0 or NULL salary to ₱30,000...');
        
        // Update all records with 0 or NULL base_salary to default 30,000
        $updated = \DB::update('
            UPDATE direct_compensations_hr4
            SET base_salary = 30000
            WHERE base_salary = 0 OR base_salary IS NULL
        ');
        
        $this->info("✓ Updated $updated DirectCompensation records to ₱30,000 base salary.");
        
        // Also update employees without positions to have a default position ID
        // First, find if there's a default position or create mapping
        $defaultPositionId = \DB::table('department_position_titles_hr2')
            ->where('base_salary', 30000)
            ->value('id');
        
        if ($defaultPositionId) {
            $empUpdated = \DB::update('
                UPDATE employees
                SET position_id = ?
                WHERE position_id IS NULL
            ', [$defaultPositionId]);
            
            $this->info("✓ Assigned $empUpdated employees without positions to default position (ID: $defaultPositionId).");
        } else {
            $this->warn("⚠ No position found with ₱30,000 base salary. Employees without positions remain unassigned.");
            $this->info("Remaining employees without positions: " . \DB::table('employees')->whereNull('position_id')->count());
        }
    }
}
