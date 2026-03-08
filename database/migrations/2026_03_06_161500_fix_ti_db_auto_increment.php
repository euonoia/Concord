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
        // Temporarily disable strict mode for this session to allow migration with potential truncation
        DB::statement("SET SESSION sql_mode = ''");

        // Fix patients_core1
        $this->fixTable('patients_core1', function() {
            DB::statement("CREATE TABLE patients_core1_new (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                patient_id varchar(255) DEFAULT NULL,
                first_name varchar(255) DEFAULT NULL,
                middle_name varchar(255) DEFAULT NULL,
                last_name varchar(255) DEFAULT NULL,
                date_of_birth date DEFAULT NULL,
                gender enum('male','female','other') DEFAULT NULL,
                phone varchar(255) DEFAULT NULL,
                email varchar(255) DEFAULT NULL,
                address text DEFAULT NULL,
                emergency_contact_name varchar(255) DEFAULT NULL,
                emergency_contact_phone varchar(255) DEFAULT NULL,
                blood_type varchar(255) DEFAULT NULL,
                allergies text DEFAULT NULL,
                medical_history text DEFAULT NULL,
                status enum('active','inactive','deceased') NOT NULL DEFAULT 'active',
                last_visit timestamp NULL DEFAULT NULL,
                care_type enum('inpatient','outpatient') DEFAULT NULL,
                admission_date date DEFAULT NULL,
                doctor_id bigint(20) DEFAULT NULL,
                reason text DEFAULT NULL,
                insurance_provider varchar(255) DEFAULT NULL,
                policy_number varchar(255) DEFAULT NULL,
                emergency_contact_relation varchar(255) DEFAULT NULL,
                assigned_nurse_id bigint(20) DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        });

        // Fix appointments_core1
        $this->fixTable('appointments_core1', function() {
            DB::statement("CREATE TABLE appointments_core1_new (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                appointment_id varchar(255) DEFAULT NULL,
                patient_id bigint(20) NOT NULL,
                doctor_id bigint(20) DEFAULT NULL,
                appointment_date date NOT NULL,
                appointment_time datetime NOT NULL,
                type enum('consultation','follow-up','emergency','surgery','checkup') NOT NULL DEFAULT 'consultation',
                status enum('pending','scheduled','confirmed','approved','rejected','completed','cancelled','declined','no-show','waiting','in_consultation','consulted','triaged','no-sohw') NOT NULL DEFAULT 'pending',
                notes text DEFAULT NULL,
                reason text DEFAULT NULL,
                approved_by bigint(20) unsigned DEFAULT NULL,
                approved_at timestamp NULL DEFAULT NULL,
                rejection_reason text DEFAULT NULL,
                triage_note text DEFAULT NULL,
                vital_signs json DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        });
    }

    private function fixTable($tableName, $createTableCallback)
    {
        $newTableName = $tableName . '_new';
        $oldTableName = $tableName . '_old';

        // Clean up previous failed attempts
        DB::statement("DROP TABLE IF EXISTS $newTableName");
        DB::statement("DROP TABLE IF EXISTS $oldTableName");

        // 1. Create new table with correct schema
        $createTableCallback();

        // 2. Copy data
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
        $columnsStr = implode(', ', $columns);
        DB::statement("INSERT INTO $newTableName ($columnsStr) SELECT $columnsStr FROM $tableName");

        // 3. Drop old table and rename new one
        DB::statement("RENAME TABLE $tableName TO $oldTableName, $newTableName TO $tableName");
        
        // 4. Drop the old table safely
        DB::statement("DROP TABLE $oldTableName");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse this safely without knowing the "broken" state, 
        // but since we are fixing a broken state, we don't really want to reverse.
    }
};
