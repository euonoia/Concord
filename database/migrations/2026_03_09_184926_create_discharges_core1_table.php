<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS discharges_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                discharge_summary text NOT NULL,
                final_diagnosis text NOT NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_discharge_encounter FOREIGN KEY (encounter_id) REFERENCES encounters_core1(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharges_core1');
    }
};
