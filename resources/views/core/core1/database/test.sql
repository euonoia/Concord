
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE IF NOT EXISTS `appointments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appointment_no` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `patients_core1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(255) DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `status` enum('active','inactive','deceased') NOT NULL DEFAULT 'active',
  `last_visit` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('patient','staff','admin') DEFAULT NULL,
  `role_slug` enum('sys_super_admin','core_admin','core_employee','hr_admin','hr_employee','logistics_admin','logistics_employee','finance_admin','finance_employee','patient','patient_guardian') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified_at` datetime DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` varchar(50) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `is_on_duty` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `appointments_core1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appointment_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` datetime NOT NULL,
  `type` enum('consultation','follow-up','emergency','surgery','checkup') NOT NULL DEFAULT 'consultation',
  `status` enum('scheduled','confirmed','completed','cancelled','no-show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `bills_core1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(255) NOT NULL,
  `patient_id` bigint(20) NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `items` json DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `medical_records_core1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `record_type` enum('diagnosis','treatment','prescription','lab_result','xray','surgery','other') NOT NULL DEFAULT 'diagnosis',
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `record_date` datetime NOT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE IF NOT EXISTS `waiting_lists_core1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `status` enum('pending','notified','converted','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;








































COMMIT;



ALTER TABLE appointments_core1
MODIFY status ENUM('pending','scheduled','confirmed','completed','cancelled','declined','no-show') 
NOT NULL DEFAULT 'pending';

ALTER TABLE `patients_core1`
ADD COLUMN IF NOT EXISTS `care_type` enum('inpatient','outpatient') DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `admission_date` date NULL,
ADD COLUMN IF NOT EXISTS `doctor_id` bigint(20) NULL,
ADD COLUMN IF NOT EXISTS `reason` text NULL;

ALTER TABLE `patients_core1`
ADD COLUMN IF NOT EXISTS `insurance_provider` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `policy_number` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `emergency_contact_relation` varchar(255) DEFAULT NULL;

ALTER TABLE patients_core1
ADD COLUMN IF NOT EXISTS assigned_nurse_id BIGINT NULL;





ALTER TABLE appointments_core1
MODIFY status ENUM(
    'pending',
    'scheduled',
    'confirmed',
    'completed',
    'cancelled',
    'declined',
    'no-sohw',
    'waiting',
    'in_consultation',
    'consulted',
    'triaged'
) NOT NULL DEFAULT 'pending';

ALTER TABLE medical_records_core1
MODIFY record_type VARCHAR(50);





ALTER TABLE `appointments`
MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS `cancellation_reason` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `cancelled_at` timestamp NULL DEFAULT NULL;


ALTER TABLE `patients_core1`
ADD COLUMN IF NOT EXISTS `insurance_provider` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `policy_number` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `emergency_contact_relation` varchar(255) DEFAULT NULL;
