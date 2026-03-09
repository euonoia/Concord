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
            CREATE TABLE IF NOT EXISTS triage_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                blood_pressure varchar(20) NULL,
                heart_rate int NULL,
                respiratory_rate int NULL,
                temperature decimal(4,1) NULL,
                spo2 int NULL,
                triage_level enum('1','2','3','4','5') NULL,
                notes text NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_triage_encounter FOREIGN KEY (encounter_id) REFERENCES encounters_core1(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS consultations_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                subjective text NULL,
                objective text NULL,
                assessment text NULL,
                plan text NULL,
                doctor_notes text NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_consultation_encounter FOREIGN KEY (encounter_id) REFERENCES encounters_core1(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS lab_orders_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                test_name varchar(255) NOT NULL,
                clinical_note text NULL,
                status varchar(50) NOT NULL DEFAULT 'ordered',
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_lab_order_encounter FOREIGN KEY (encounter_id) REFERENCES encounters_core1(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS prescriptions_core1 (
                id bigint SIGNED NOT NULL AUTO_INCREMENT,
                encounter_id bigint SIGNED NOT NULL,
                medication varchar(255) NOT NULL,
                dosage varchar(255) NOT NULL,
                instructions text NULL,
                duration varchar(100) NULL,
                created_at timestamp NULL,
                updated_at timestamp NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_prescription_encounter FOREIGN KEY (encounter_id) REFERENCES encounters_core1(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opd_clinical_tables_core1');
    }
};
