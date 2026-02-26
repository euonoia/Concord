
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `core1_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients_core1`
--

CREATE TABLE `patients_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_core1`
--

CREATE TABLE `users_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','head_nurse','nurse','patient','receptionist','billing') NOT NULL DEFAULT 'patient',
  `employee_id` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments_core1`
--

CREATE TABLE `appointments_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` varchar(255) DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` datetime NOT NULL,
  `type` enum('consultation','follow-up','emergency','surgery','checkup') NOT NULL DEFAULT 'consultation',
  `status` enum('scheduled','confirmed','completed','cancelled','no-show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills_core1`
--

CREATE TABLE `bills_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bill_number` varchar(255) NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_records_core1`
--

CREATE TABLE `medical_records_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `record_type` enum('diagnosis','treatment','prescription','lab_result','xray','surgery','other') NOT NULL DEFAULT 'diagnosis',
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `record_date` datetime NOT NULL,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waiting_lists_core1`
--

CREATE TABLE `waiting_lists_core1` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `status` enum('pending','notified','converted','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_appointment_no_unique` (`appointment_no`);

--
-- Indexes for table `patients_core1`
--
ALTER TABLE `patients_core1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_core1_patient_id_unique` (`patient_id`),
  ADD UNIQUE KEY `patients_core1_email_unique` (`email`);

--
-- Indexes for table `users_core1`
--
ALTER TABLE `users_core1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_core1_email_unique` (`email`),
  ADD UNIQUE KEY `users_core1_employee_id_unique` (`employee_id`);

--
-- Indexes for table `appointments_core1`
--
ALTER TABLE `appointments_core1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_core1_appointment_id_unique` (`appointment_id`),
  ADD KEY `appointments_core1_patient_id_foreign` (`patient_id`),
  ADD KEY `appointments_core1_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `bills_core1`
--
ALTER TABLE `bills_core1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bills_core1_bill_number_unique` (`bill_number`),
  ADD KEY `bills_core1_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `medical_records_core1`
--
ALTER TABLE `medical_records_core1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medical_records_core1_patient_id_foreign` (`patient_id`),
  ADD KEY `medical_records_core1_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `waiting_lists_core1`
--
ALTER TABLE `waiting_lists_core1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `waiting_lists_core1_patient_id_foreign` (`patient_id`),
  ADD KEY `waiting_lists_core1_doctor_id_foreign` (`doctor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients_core1`
--
ALTER TABLE `patients_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_core1`
--
ALTER TABLE `users_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments_core1`
--
ALTER TABLE `appointments_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bills_core1`
--
ALTER TABLE `bills_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_records_core1`
--
ALTER TABLE `medical_records_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waiting_lists_core1`
--
ALTER TABLE `waiting_lists_core1`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments_core1`
--
ALTER TABLE `appointments_core1`
  ADD CONSTRAINT `appointments_core1_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients_core1` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_core1_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users_core1` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bills_core1`
--
ALTER TABLE `bills_core1`
  ADD CONSTRAINT `bills_core1_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients_core1` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records_core1`
--
ALTER TABLE `medical_records_core1`
  ADD CONSTRAINT `medical_records_core1_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients_core1` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_core1_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users_core1` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `waiting_lists_core1`
--
ALTER TABLE `waiting_lists_core1`
  ADD CONSTRAINT `waiting_lists_core1_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients_core1` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `waiting_lists_core1_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users_core1` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

ALTER TABLE appointments_core1
MODIFY status ENUM('pending','scheduled','confirmed','completed','cancelled','declined','no-show') 
NOT NULL DEFAULT 'pending';

ALTER TABLE `patients_core1`
ADD COLUMN `care_type` enum('inpatient','outpatient') DEFAULT NULL AFTER `status`;

ALTER TABLE `patients_core1`
ADD COLUMN `admission_date` date NULL,
ADD COLUMN `doctor_id` bigint(20) UNSIGNED NULL,
ADD COLUMN `reason` text NULL;

ALTER TABLE `appointments_core1`
MODIFY `type` ENUM('consultation','follow-up','check-up','emergency') NOT NULL;

ALTER TABLE patients_core1 
MODIFY status ENUM(
    'active',
    'inactive',
    'deceased',
    'scheduled',
    'waiting',
    'in consultation',
    'consulted'
);

ALTER TABLE patients_core1
ADD COLUMN assigned_nurse_id BIGINT UNSIGNED NULL AFTER care_type;

ALTER TABLE users_core1 
MODIFY role ENUM('admin','doctor','head_nurse','nurse','patient','receptionist','billing') NOT NULL;

ALTER TABLE appointments_core1
ADD COLUMN triage_note VARCHAR(255) NULL AFTER notes,
ADD COLUMN vital_signs VARCHAR(255) NULL AFTER triage_note;

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

-- 
-- Additional modifications from recent migrations
--

ALTER TABLE `appointments`
ADD COLUMN `date_of_birth` date NOT NULL AFTER `phone`,
ADD COLUMN `gender` varchar(255) NOT NULL AFTER `date_of_birth`,
ADD COLUMN `address_street` varchar(255) NOT NULL AFTER `gender`,
ADD COLUMN `address_city` varchar(255) NOT NULL AFTER `address_street`,
ADD COLUMN `address_zip` varchar(255) NOT NULL AFTER `address_city`,
ADD COLUMN `reason_for_visit` text NOT NULL AFTER `appointment_time`,
ADD COLUMN `insurance_provider` varchar(255) DEFAULT NULL AFTER `reason_for_visit`,
ADD COLUMN `policy_number` varchar(255) DEFAULT NULL AFTER `insurance_provider`,
ADD COLUMN `medical_history_summary` text DEFAULT NULL AFTER `policy_number`;

ALTER TABLE `appointments`
MODIFY status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
ADD COLUMN `cancellation_reason` text DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `cancelled_at` timestamp NULL DEFAULT NULL AFTER `cancellation_reason`;

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `patients_core1`
ADD COLUMN `insurance_provider` varchar(255) DEFAULT NULL AFTER `medical_history`,
ADD COLUMN `policy_number` varchar(255) DEFAULT NULL AFTER `insurance_provider`,
ADD COLUMN `emergency_contact_relation` varchar(255) DEFAULT NULL AFTER `emergency_contact_phone`;
