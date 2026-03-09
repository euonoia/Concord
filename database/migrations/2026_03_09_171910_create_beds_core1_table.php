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
            CREATE TABLE IF NOT EXISTS beds_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                room_id bigint SIGNED NOT NULL,
                bed_number varchar(255) NOT NULL,
                status varchar(255) NOT NULL DEFAULT 'Available',
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
        Schema::dropIfExists('beds_core1');
    }
};
