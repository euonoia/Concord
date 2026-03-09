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
            CREATE TABLE IF NOT EXISTS wards_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                description text NULL,
                capacity int NOT NULL DEFAULT 0,
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
        Schema::dropIfExists('wards_core1');
    }
};
