<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\core1\Bed;
use App\Models\core1\Admission;
use Illuminate\Support\Facades\DB;

class SyncBedStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core1:sync-beds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile bed statuses based on active admissions (Admitted or Doctor Approved)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Bed Status Reconciliation...');

        DB::transaction(function () {
            // 1. Get all beds currently marked as Occupied
            $occupiedBeds = Bed::where('status', 'Occupied')->get();
            $fixedCount = 0;

            foreach ($occupiedBeds as $bed) {
                // Check if there is an active admission for this bed
                $activeAdmission = Admission::where('bed_id', $bed->id)
                    ->whereIn('status', ['Admitted', 'Doctor Approved'])
                    ->first();

                /** @var Bed $bed */
                if (!$activeAdmission) {
                    $this->warn("Bed {$bed->bed_number} (ID: {$bed->id}) is Occupied but has NO active admission. Resetting to Available.");
                    $bed->update(['status' => 'Available']);
                    $fixedCount++;
                }
            }

            // 2. Ensuring beds with active admissions are marked as Occupied
            $activeAdmissions = Admission::whereIn('status', ['Admitted', 'Doctor Approved'])->get();
            $ensuredCount = 0;

            foreach ($activeAdmissions as $admission) {
                /** @var Bed|null $associatedBed */
                $associatedBed = $admission->bed;
                if ($associatedBed && $associatedBed->status !== 'Occupied') {
                    $this->warn("Admission ID: {$admission->id} has status '{$admission->status}' but Bed {$associatedBed->bed_number} is '{$associatedBed->status}'. Setting to Occupied.");
                    $associatedBed->update(['status' => 'Occupied']);
                    $ensuredCount++;
                }
            }

            $this->info("Reconciliation Complete.");
            $this->line("- Fixed (Occupied -> Available): $fixedCount");
            $this->line("- Ensured (Available -> Occupied): $ensuredCount");
        });
    }
}
