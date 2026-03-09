<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS admissions_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                bed_id bigint SIGNED NOT NULL,
                admission_date datetime NOT NULL,
                discharge_date datetime NULL,
                status varchar(50) NOT NULL DEFAULT 'Admitted',
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions_core1');
    }
};
