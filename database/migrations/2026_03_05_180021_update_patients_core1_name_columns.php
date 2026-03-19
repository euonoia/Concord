<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disabled for safe payroll migration
        // Add new columns idompotent for TiDB
        // DB::statement('ALTER TABLE patients_core1 ADD COLUMN IF NOT EXISTS first_name VARCHAR(255) NULL;');
        // DB::statement('ALTER TABLE patients_core1 ADD COLUMN IF NOT EXISTS middle_name VARCHAR(255) NULL;');
        // DB::statement('ALTER TABLE patients_core1 ADD COLUMN IF NOT EXISTS last_name VARCHAR(255) NULL;');

        // Migrate existing names safely
        // $records = DB::table('patients_core1')->whereNotNull('name')->get();
        // foreach ($records as $record) {
        //     $parts = explode(' ', $record->name, 2);
        //     $firstName = $parts[0];
        //     $lastName = count($parts) > 1 ? $parts[1] : '';
        //     DB::table('patients_core1')->where('id', $record->id)->update([
        //         'first_name' => $firstName,
        //         'last_name' => $lastName,
        //         'middle_name' => null
        //     ]);
        // }

        // Drop original name column
        // DB::statement('ALTER TABLE patients_core1 DROP COLUMN IF EXISTS name;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE patients_core1 ADD COLUMN IF NOT EXISTS name VARCHAR(255) NULL;');
        
        // Try restoring the name column
        $records = DB::table('patients_core1')->get();
        foreach ($records as $record) {
            $name = trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? ''));
            DB::table('patients_core1')->where('id', $record->id)->update(['name' => $name ?: 'Unknown']);
        }

        DB::statement('ALTER TABLE patients_core1 DROP COLUMN IF EXISTS first_name;');
        DB::statement('ALTER TABLE patients_core1 DROP COLUMN IF EXISTS middle_name;');
        DB::statement('ALTER TABLE patients_core1 DROP COLUMN IF EXISTS last_name;');
    }
};
