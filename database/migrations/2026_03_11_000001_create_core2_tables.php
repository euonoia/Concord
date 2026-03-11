<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── PHARMACY ────────────────────────────────────────────────────────────

        DB::statement("
            CREATE TABLE IF NOT EXISTS `drug_inventory_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `drug_num` VARCHAR(50) DEFAULT NULL,
                `drug_name` VARCHAR(100) DEFAULT NULL,
                `quantity` INT DEFAULT NULL,
                `expiry_date` DATE DEFAULT NULL,
                `supplier` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `formula_management_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `formula_id` VARCHAR(50) DEFAULT NULL,
                `formula_name` VARCHAR(100) DEFAULT NULL,
                `ingredients_list` TEXT DEFAULT NULL,
                `drug_id` VARCHAR(50) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `prescriptions_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `prescription_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `doctor_id` BIGINT DEFAULT NULL,
                `date` DATE DEFAULT NULL,
                `drug_id` VARCHAR(50) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // ── MEDICAL PACKAGES ────────────────────────────────────────────────────

        DB::statement("
            CREATE TABLE IF NOT EXISTS `package_definition_pricing_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `package_id` VARCHAR(50) DEFAULT NULL,
                `package_name` VARCHAR(100) DEFAULT NULL,
                `price` DECIMAL(10,2) DEFAULT NULL,
                `includes_services` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `patient_enrollment_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `package_identifier` VARCHAR(50) DEFAULT NULL,
                `package_description` TEXT DEFAULT NULL,
                `price_list_node` VARCHAR(100) DEFAULT NULL,
                `included_services_state` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // ── LABORATORY ──────────────────────────────────────────────────────────

        DB::statement("
            CREATE TABLE IF NOT EXISTS `test_orders_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `order_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `test_id` VARCHAR(50) DEFAULT NULL,
                `date_ordered` DATE DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `sample_tracking_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `sample_id` VARCHAR(50) DEFAULT NULL,
                `test_order_id` VARCHAR(50) DEFAULT NULL,
                `status` VARCHAR(50) DEFAULT NULL,
                `lab_id` VARCHAR(50) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `result_validation_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `result_id` VARCHAR(50) DEFAULT NULL,
                `sample_id` VARCHAR(50) DEFAULT NULL,
                `test_result` TEXT DEFAULT NULL,
                `validated_by` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // ── SURGERY & DIET ──────────────────────────────────────────────────────

        DB::statement("
            CREATE TABLE IF NOT EXISTS `operating_room_booking_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `operating_booking_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `booking_date` DATE DEFAULT NULL,
                `surgeon_id` BIGINT DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `nutritional_assessment_consultation_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `enrollment_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `package_id` VARCHAR(50) DEFAULT NULL,
                `enrollment_status` VARCHAR(50) DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `utilization_reporting_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `report_id` VARCHAR(50) DEFAULT NULL,
                `module_name` VARCHAR(100) DEFAULT NULL,
                `usage_metrics` VARCHAR(100) DEFAULT NULL,
                `reporting_period` DATE DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // ── BED & LINEN ─────────────────────────────────────────────────────────

        DB::statement("
            CREATE TABLE IF NOT EXISTS `room_assignments_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `assignment_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `room` VARCHAR(50) DEFAULT NULL,
                `date_assigned` DATE DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `bed_status_allocation_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `bed_id` VARCHAR(50) DEFAULT NULL,
                `room_id` VARCHAR(50) DEFAULT NULL,
                `status` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `patient_transfer_management_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `transfer_id` VARCHAR(50) DEFAULT NULL,
                `patient_id` BIGINT DEFAULT NULL,
                `from_location` VARCHAR(100) DEFAULT NULL,
                `to_location` VARCHAR(100) DEFAULT NULL,
                `transfer_date` DATE DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS `house_keeping_status_core2` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `house_keeping_id` VARCHAR(50) DEFAULT NULL,
                `room_id` VARCHAR(50) DEFAULT NULL,
                `bed_id` VARCHAR(50) DEFAULT NULL,
                `status` VARCHAR(50) DEFAULT NULL,
                `last_cleaned_date` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `drug_inventory_core2`');
        DB::statement('DROP TABLE IF EXISTS `formula_management_core2`');
        DB::statement('DROP TABLE IF EXISTS `prescriptions_core2`');
        DB::statement('DROP TABLE IF EXISTS `package_definition_pricing_core2`');
        DB::statement('DROP TABLE IF EXISTS `patient_enrollment_core2`');
        DB::statement('DROP TABLE IF EXISTS `test_orders_core2`');
        DB::statement('DROP TABLE IF EXISTS `sample_tracking_core2`');
        DB::statement('DROP TABLE IF EXISTS `result_validation_core2`');
        DB::statement('DROP TABLE IF EXISTS `operating_room_booking_core2`');
        DB::statement('DROP TABLE IF EXISTS `nutritional_assessment_consultation_core2`');
        DB::statement('DROP TABLE IF EXISTS `utilization_reporting_core2`');
        DB::statement('DROP TABLE IF EXISTS `room_assignments_core2`');
        DB::statement('DROP TABLE IF EXISTS `bed_status_allocation_core2`');
        DB::statement('DROP TABLE IF EXISTS `patient_transfer_management_core2`');
        DB::statement('DROP TABLE IF EXISTS `house_keeping_status_core2`');
    }
};
