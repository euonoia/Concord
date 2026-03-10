-- =====================================================
-- HR1 System Database Schema
-- Concord HR1 Module - Complete Database Structure
-- =====================================================

-- =====================================================
-- 1. USERS & AUTHENTICATION
-- =====================================================

-- Users Table (matches existing codebase structure)
CREATE TABLE IF NOT EXISTS `users_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NULL,
    `profile_picture` VARCHAR(255) NULL,
    `role` ENUM('admin', 'staff', 'candidate') NOT NULL DEFAULT 'candidate',
    `position` VARCHAR(255) NULL,
    `status` ENUM('Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected') NULL,
    `applied_date` DATE NULL,
    `score` INT NOT NULL DEFAULT 0,
    `skills` TEXT NULL,
    `notes` TEXT NULL,
    `contact_no` VARCHAR(20) NULL,
    `date_of_employment` DATE NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. APPLICANTS MANAGEMENT
-- =====================================================

-- Applicants Table
CREATE TABLE IF NOT EXISTS `applicants_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NULL,
    `position` VARCHAR(255) NULL,
    `contact_no` VARCHAR(20) NULL,
    `status` ENUM('applicant', 'candidate', 'probation', 'regular', 'rejected') NOT NULL DEFAULT 'applicant',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`),
    INDEX `idx_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. RECRUITMENT / JOB POSTINGS
-- =====================================================

-- Job Postings Table (matches existing codebase)
CREATE TABLE IF NOT EXISTS `job_postings_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `department` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `type` ENUM('Full-time', 'Part-time', 'Contract') NOT NULL DEFAULT 'Full-time',
    `status` ENUM('Open', 'Closed') NOT NULL DEFAULT 'Open',
    `posted_date` DATE NOT NULL,
    `description` TEXT NULL,
    `require_resume` TINYINT(1) NOT NULL DEFAULT 1,
    `attachment_path` VARCHAR(500) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applications Table (matches existing codebase)
CREATE TABLE IF NOT EXISTS `applications_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `job_posting_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected') NOT NULL DEFAULT 'Applicant',
    `applied_date` DATE NOT NULL,
    `interview_date` DATETIME NULL,
    `interview_location` VARCHAR(255) NULL,
    `interview_description` TEXT NULL,
    `documents` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_job_posting_id` (`job_posting_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Application Documents Table (Optional - for detailed document tracking)
CREATE TABLE IF NOT EXISTS `application_documents_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `application_id` BIGINT UNSIGNED NOT NULL,
    `document_type` ENUM('cv', 'resume', 'license', 'id', 'certificate', 'other') NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `mime_type` VARCHAR(100) NULL,
    `uploaded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_application_id` (`application_id`),
    FOREIGN KEY (`application_id`) REFERENCES `applications_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. ONBOARDING & TASKS
-- =====================================================

-- Task Sets Table (Collections of tasks/requirements)
CREATE TABLE IF NOT EXISTS `task_sets_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_created_by` (`created_by`),
    FOREIGN KEY (`created_by`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tasks Table (Individual requirements/tasks)
CREATE TABLE IF NOT EXISTS `tasks_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `task_set_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `category` VARCHAR(100) NULL,
    `department` VARCHAR(255) NULL,
    `is_required` BOOLEAN NOT NULL DEFAULT TRUE,
    `order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_task_set_id` (`task_set_id`),
    INDEX `idx_category` (`category`),
    FOREIGN KEY (`task_set_id`) REFERENCES `task_sets_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job Task Sets Assignment (Which task sets are required for which jobs)
CREATE TABLE IF NOT EXISTS `job_task_sets_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `job_posting_id` BIGINT UNSIGNED NOT NULL,
    `task_set_id` BIGINT UNSIGNED NOT NULL,
    `is_required` BOOLEAN NOT NULL DEFAULT TRUE,
    `assigned_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `assigned_by` BIGINT UNSIGNED NULL,
    INDEX `idx_job_posting_id` (`job_posting_id`),
    INDEX `idx_task_set_id` (`task_set_id`),
    UNIQUE KEY `unique_job_task_set` (`job_posting_id`, `task_set_id`),
    FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`task_set_id`) REFERENCES `task_sets_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`assigned_by`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applicant Task Completion Tracking
CREATE TABLE IF NOT EXISTS `applicant_tasks_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `task_id` BIGINT UNSIGNED NOT NULL,
    `job_posting_id` BIGINT UNSIGNED NULL,
    `completed` BOOLEAN NOT NULL DEFAULT FALSE,
    `completed_at` TIMESTAMP NULL,
    `submitted_document` VARCHAR(500) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_task_id` (`task_id`),
    INDEX `idx_job_posting_id` (`job_posting_id`),
    INDEX `idx_completed` (`completed`),
    UNIQUE KEY `unique_user_task` (`user_id`, `task_id`, `job_posting_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`task_id`) REFERENCES `tasks_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Onboarding Tasks Table (matches existing codebase)
CREATE TABLE IF NOT EXISTS `onboarding_tasks_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `department` VARCHAR(255) NOT NULL,
    `category` ENUM('Pre-onboarding', 'Orientation', 'IT Setup', 'Training') NOT NULL DEFAULT 'Pre-onboarding',
    `completed` TINYINT(1) NOT NULL DEFAULT 0,
    `assigned_to` ENUM('admin', 'staff', 'candidate') NOT NULL DEFAULT 'candidate',
    `user_id` BIGINT UNSIGNED NULL,
    `required_for_phase` INT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_category` (`category`),
    INDEX `idx_completed` (`completed`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. PERFORMANCE & ASSESSMENT
-- =====================================================

-- Question Sets / Forms Table
CREATE TABLE IF NOT EXISTS `question_sets_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` ENUM('assessment', 'evaluation', 'survey', 'interview') NOT NULL DEFAULT 'assessment',
    `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_type` (`type`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_created_by` (`created_by`),
    FOREIGN KEY (`created_by`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questions Table
CREATE TABLE IF NOT EXISTS `questions_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question_set_id` BIGINT UNSIGNED NOT NULL,
    `question_text` TEXT NOT NULL,
    `question_type` ENUM('text', 'multiple-choice', 'rating', 'yes-no', 'file-upload') NOT NULL DEFAULT 'text',
    `options` JSON NULL COMMENT 'For multiple-choice questions',
    `is_required` BOOLEAN NOT NULL DEFAULT TRUE,
    `order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_question_set_id` (`question_set_id`),
    INDEX `idx_question_type` (`question_type`),
    FOREIGN KEY (`question_set_id`) REFERENCES `question_sets_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applicant Answers / Responses
CREATE TABLE IF NOT EXISTS `applicant_responses_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `question_set_id` BIGINT UNSIGNED NOT NULL,
    `response_text` TEXT NULL,
    `response_value` VARCHAR(255) NULL,
    `response_file` VARCHAR(500) NULL,
    `submitted_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_question_id` (`question_id`),
    INDEX `idx_question_set_id` (`question_set_id`),
    UNIQUE KEY `unique_user_question` (`user_id`, `question_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `questions_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_set_id`) REFERENCES `question_sets_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. RECOGNITION & CULTURE
-- =====================================================

-- Recognitions Table (matches existing codebase structure)
CREATE TABLE IF NOT EXISTS `recognitions_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `from` VARCHAR(255) NOT NULL,
    `to` VARCHAR(255) NOT NULL,
    `reason` TEXT NOT NULL,
    `award_type` VARCHAR(255) NOT NULL,
    `date` DATE NOT NULL,
    `congratulations` INT NOT NULL DEFAULT 0,
    `boosts` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_date` (`date`),
    INDEX `idx_award_type` (`award_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Award Categories Table (matches existing codebase structure)
CREATE TABLE IF NOT EXISTS `award_categories_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. EVALUATION CRITERIA
-- =====================================================

-- Evaluation Criteria Table (matches existing codebase structure)
CREATE TABLE IF NOT EXISTS `evaluation_criteria_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `label` VARCHAR(255) NOT NULL,
    `section` ENUM('A', 'B', 'C') NOT NULL,
    `weight` INT NOT NULL DEFAULT 10,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Applicant Evaluations
CREATE TABLE IF NOT EXISTS `applicant_evaluations_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `criteria_id` BIGINT UNSIGNED NOT NULL,
    `score` DECIMAL(5,2) NULL COMMENT 'Score out of 100',
    `evaluator_id` BIGINT UNSIGNED NULL,
    `comments` TEXT NULL,
    `evaluated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_criteria_id` (`criteria_id`),
    INDEX `idx_evaluator_id` (`evaluator_id`),
    UNIQUE KEY `unique_user_criteria` (`user_id`, `criteria_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`evaluator_id`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Learning Modules Table (matches existing codebase)
CREATE TABLE IF NOT EXISTS `learning_modules_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Learning Modules Table (matches existing codebase)
CREATE TABLE IF NOT EXISTS `user_learning_modules_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `learning_module_id` BIGINT UNSIGNED NOT NULL,
    `completed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_learning_module_id` (`learning_module_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`learning_module_id`) REFERENCES `learning_modules_hr1`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. SYSTEM SETTINGS & CONFIGURATION
-- =====================================================

-- HR1 System Settings
CREATE TABLE IF NOT EXISTS `settings_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `type` ENUM('string', 'integer', 'boolean', 'json') NOT NULL DEFAULT 'string',
    `description` TEXT NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`key`),
    FOREIGN KEY (`updated_by`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. ACTIVITY LOGS (Optional but recommended)
-- =====================================================

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS `activity_logs_hr1` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `model_type` VARCHAR(255) NULL,
    `model_id` BIGINT UNSIGNED NULL,
    `description` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_model` (`model_type`, `model_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users_hr1`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. LARAVEL SESSIONS TABLE (Required for Laravel)
-- =====================================================

-- Sessions Table (Laravel Framework Requirement)
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. LARAVEL CACHE TABLE (Optional but recommended)
-- =====================================================

-- Cache Table (Laravel Framework Requirement)
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    INDEX `idx_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache Locks Table
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    INDEX `idx_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA / INITIAL RECORDS
-- =====================================================

-- Clear existing data (optional - comment out if you want to keep existing data)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `activity_logs_hr1`;
TRUNCATE TABLE `settings_hr1`;
TRUNCATE TABLE `applicant_evaluations_hr1`;
TRUNCATE TABLE `evaluation_criteria_hr1`;
TRUNCATE TABLE `award_categories_hr1`;
TRUNCATE TABLE `recognitions_hr1`;
TRUNCATE TABLE `applicant_responses_hr1`;
TRUNCATE TABLE `questions_hr1`;
TRUNCATE TABLE `question_sets_hr1`;
TRUNCATE TABLE `applicant_tasks_hr1`;
TRUNCATE TABLE `applicant_tasks_hr1`;
TRUNCATE TABLE `job_task_sets_hr1`;
TRUNCATE TABLE `user_learning_modules_hr1`;
TRUNCATE TABLE `learning_modules_hr1`;
TRUNCATE TABLE `onboarding_tasks_hr1`;
TRUNCATE TABLE `tasks_hr1`;
TRUNCATE TABLE `task_sets_hr1`;
TRUNCATE TABLE `application_documents_hr1`;
TRUNCATE TABLE `applications_hr1`;
TRUNCATE TABLE `job_postings_hr1`;
TRUNCATE TABLE `applicants_hr1`;
TRUNCATE TABLE `users_hr1`;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. USERS DATA
-- =====================================================

-- Password for all: 'password' (hashed with bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)

-- Admin Users
INSERT INTO `users_hr1` (`id`, `name`, `email`, `password`, `phone`, `role`, `position`, `status`, `contact_no`, `date_of_employment`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@concord.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0101', 'admin', 'System Administrator', NULL, '+1-555-0101', '2020-01-15', NOW(), NOW()),
(2, 'HR Manager', 'hr@concord.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0102', 'admin', 'HR Manager', NULL, '+1-555-0102', '2021-03-20', NOW(), NOW());

-- Staff Users
INSERT INTO `users_hr1` (`id`, `name`, `email`, `password`, `phone`, `role`, `position`, `status`, `contact_no`, `date_of_employment`, `created_at`, `updated_at`) VALUES
(3, 'John Smith', 'john.smith@concord.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0201', 'staff', 'HR Staff', NULL, '+1-555-0201', '2022-05-10', NOW(), NOW()),
(4, 'Sarah Johnson', 'sarah.johnson@concord.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0202', 'staff', 'Recruitment Specialist', NULL, '+1-555-0202', '2022-08-15', NOW(), NOW());

-- Candidate Users (These will also appear in applicants_hr1)
INSERT INTO `users_hr1` (`id`, `name`, `email`, `password`, `phone`, `role`, `position`, `status`, `applied_date`, `score`, `skills`, `contact_no`, `created_at`, `updated_at`) VALUES
(5, 'Michael Chen', 'michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1001', 'candidate', 'Registered Nurse', 'Applicant', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 85, 'Emergency Care, ACLS, BLS', '+1-555-1001', NOW(), NOW()),
(6, 'Emily Rodriguez', 'emily.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1002', 'candidate', 'Medical Technologist', 'Applicant', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 92, 'Laboratory Testing, Quality Control', '+1-555-1002', NOW(), NOW()),
(7, 'David Kim', 'david.kim@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1003', 'candidate', 'Physical Therapist', 'Candidate', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 88, 'Physical Therapy, Rehabilitation', '+1-555-1003', NOW(), NOW()),
(8, 'Jessica Martinez', 'jessica.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1004', 'candidate', 'Pharmacist', 'Candidate', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 95, 'Clinical Pharmacy, Medication Management', '+1-555-1004', NOW(), NOW()),
(9, 'Robert Taylor', 'robert.taylor@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1005', 'candidate', 'Radiology Technician', 'Probation', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 90, 'Radiology, Imaging, ARRT', '+1-555-1005', NOW(), NOW()),
(10, 'Amanda White', 'amanda.white@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1006', 'candidate', 'Laboratory Assistant', 'Rejected', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 65, 'Basic Lab Skills', '+1-555-1006', NOW(), NOW()),
(11, 'James Wilson', 'james.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1007', 'candidate', 'Respiratory Therapist', 'Applicant', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 82, 'Respiratory Care, Ventilator Management', '+1-555-1007', NOW(), NOW()),
(12, 'Lisa Anderson', 'lisa.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-1008', 'candidate', 'Nurse Practitioner', 'Applicant', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 89, 'Advanced Practice, Primary Care', '+1-555-1008', NOW(), NOW());

-- =====================================================
-- 2. APPLICANTS DATA (Separate table for admin management)
-- =====================================================

INSERT INTO `applicants_hr1` (`id`, `user_id`, `name`, `email`, `password`, `position`, `contact_no`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 5, 'Michael Chen', 'michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Registered Nurse', '+1-555-1001', 'applicant', 'Strong background in emergency care', NOW(), NOW()),
(2, 6, 'Emily Rodriguez', 'emily.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Medical Technologist', '+1-555-1002', 'applicant', 'Excellent technical skills', NOW(), NOW()),
(3, 7, 'David Kim', 'david.kim@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Physical Therapist', '+1-555-1003', 'candidate', 'Scheduled for interview next week', NOW(), NOW()),
(4, 8, 'Jessica Martinez', 'jessica.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pharmacist', '+1-555-1004', 'candidate', 'Offer extended, awaiting response', NOW(), NOW()),
(5, 9, 'Robert Taylor', 'robert.taylor@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Radiology Technician', '+1-555-1005', 'probation', 'Successfully onboarded', NOW(), NOW()),
(6, 10, 'Amanda White', 'amanda.white@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laboratory Assistant', '+1-555-1006', 'rejected', 'Did not meet requirements', NOW(), NOW()),
(7, 11, 'James Wilson', 'james.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Respiratory Therapist', '+1-555-1007', 'applicant', 'Recent graduate', NOW(), NOW()),
(8, 12, 'Lisa Anderson', 'lisa.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nurse Practitioner', '+1-555-1008', 'applicant', 'Advanced practice nurse', NOW(), NOW());

-- =====================================================
-- 3. JOB POSTINGS DATA
-- =====================================================

INSERT INTO `job_postings_hr1` (`id`, `title`, `department`, `location`, `type`, `status`, `posted_date`, `description`, `require_resume`, `attachment_path`, `created_at`, `updated_at`) VALUES
(1, 'Registered Nurse - Emergency Department', 'Emergency', 'Main Hospital', 'Full-time', 'Open', DATE_SUB(CURDATE(), INTERVAL 20 DAY), 'Seeking experienced RN for fast-paced emergency department. Must handle high-stress situations and work in a team environment. Requirements: BSN, 2+ years ER experience, ACLS certification.', 1, NULL, NOW(), NOW()),
(2, 'Medical Laboratory Technologist', 'Laboratory', 'Main Hospital', 'Full-time', 'Open', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 'Perform complex laboratory tests and analyze results. Maintain laboratory equipment and ensure quality control. Requirements: Bachelor degree in Medical Technology, ASCP certification preferred.', 1, NULL, NOW(), NOW()),
(3, 'Physical Therapist', 'Rehabilitation', 'Rehabilitation Center', 'Full-time', 'Open', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'Provide physical therapy services to patients. Develop treatment plans and monitor progress. Requirements: DPT degree, state licensure required.', 1, NULL, NOW(), NOW()),
(4, 'Clinical Pharmacist', 'Pharmacy', 'Main Hospital', 'Full-time', 'Open', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 'Dispense medications, provide drug information, and ensure patient safety. Work with healthcare team. Requirements: PharmD degree, state licensure, hospital experience preferred.', 1, NULL, NOW(), NOW()),
(5, 'Radiology Technician', 'Radiology', 'Main Hospital', 'Part-time', 'Open', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'Operate imaging equipment, position patients, and ensure quality images. Part-time position with flexible schedule. Requirements: Associate degree, ARRT certification.', 1, NULL, NOW(), NOW()),
(6, 'Respiratory Therapist', 'Respiratory Care', 'Main Hospital', 'Full-time', 'Closed', DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'Provide respiratory care to patients. Manage ventilators and perform diagnostic tests. Requirements: Associate degree, RRT credential.', 1, NULL, NOW(), NOW());

-- =====================================================
-- 4. APPLICATIONS DATA
-- =====================================================

INSERT INTO `applications_hr1` (`id`, `user_id`, `job_posting_id`, `status`, `applied_date`, `interview_date`, `interview_location`, `interview_description`, `documents`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'Applicant', DATE_SUB(CURDATE(), INTERVAL 10 DAY), NULL, NULL, NULL, '["cv.pdf", "license.pdf"]', NOW(), NOW()),
(2, 6, 2, 'Applicant', DATE_SUB(CURDATE(), INTERVAL 5 DAY), NULL, NULL, NULL, '["resume.pdf", "certificate.pdf"]', NOW(), NOW()),
(3, 7, 3, 'Candidate', DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Main Hospital - Conference Room A', 'Initial screening interview', '["cv.pdf"]', NOW(), NOW()),
(4, 8, 4, 'Candidate', DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Main Hospital - HR Office', 'Final interview completed', '["cv.pdf", "license.pdf", "references.pdf"]', NOW(), NOW()),
(5, 9, 5, 'Probation', DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'Main Hospital', 'Interview completed, offer accepted', '["cv.pdf", "license.pdf"]', NOW(), NOW()),
(6, 11, 1, 'Applicant', DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL, NULL, NULL, '["resume.pdf"]', NOW(), NOW()),
(7, 12, 2, 'Applicant', DATE_SUB(CURDATE(), INTERVAL 4 DAY), NULL, NULL, NULL, '["cv.pdf", "certificate.pdf"]', NOW(), NOW());

-- =====================================================
-- 5. TASK SETS & TASKS DATA
-- =====================================================

INSERT INTO `task_sets_hr1` (`id`, `name`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Medical License Requirements', 'Required documents and certifications for medical staff', 1, NOW(), NOW()),
(2, 'Background Check Package', 'Complete background verification requirements', 1, NOW(), NOW()),
(3, 'Orientation Tasks', 'New employee orientation and training tasks', 2, NOW(), NOW()),
(4, 'Credentialing Documents', 'Professional credentialing and verification documents', 1, NOW(), NOW());

INSERT INTO `tasks_hr1` (`id`, `task_set_id`, `title`, `description`, `category`, `department`, `is_required`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Submit Medical License', 'Provide current medical license copy', 'Licensing', 'All', TRUE, 1, NOW(), NOW()),
(2, 1, 'Submit DEA Registration', 'Provide DEA registration if applicable', 'Licensing', 'All', TRUE, 2, NOW(), NOW()),
(3, 1, 'Submit Board Certification', 'Provide board certification documents', 'Licensing', 'All', FALSE, 3, NOW(), NOW()),
(4, 2, 'Complete Background Check Form', 'Fill out background check authorization', 'Verification', 'All', TRUE, 1, NOW(), NOW()),
(5, 2, 'Submit References', 'Provide three professional references', 'Verification', 'All', TRUE, 2, NOW(), NOW()),
(6, 2, 'Fingerprint Submission', 'Complete fingerprinting process', 'Verification', 'All', TRUE, 3, NOW(), NOW()),
(7, 3, 'Attend HR Orientation', 'Complete HR orientation session', 'Training', 'All', TRUE, 1, NOW(), NOW()),
(8, 3, 'Complete Safety Training', 'Finish safety and compliance training', 'Training', 'All', TRUE, 2, NOW(), NOW()),
(9, 3, 'Review Employee Handbook', 'Read and acknowledge employee handbook', 'Training', 'All', TRUE, 3, NOW(), NOW()),
(10, 4, 'Submit Education Transcripts', 'Provide official education transcripts', 'Credentialing', 'All', TRUE, 1, NOW(), NOW()),
(11, 4, 'Submit Professional References', 'Provide professional reference letters', 'Credentialing', 'All', TRUE, 2, NOW(), NOW());

-- Job Task Sets Assignment
INSERT INTO `job_task_sets_hr1` (`id`, `job_posting_id`, `task_set_id`, `is_required`, `assigned_at`, `assigned_by`) VALUES
(1, 1, 1, TRUE, NOW(), 1),
(2, 1, 2, TRUE, NOW(), 1),
(3, 1, 3, TRUE, NOW(), 1),
(4, 2, 1, TRUE, NOW(), 1),
(5, 2, 2, TRUE, NOW(), 1),
(6, 2, 4, TRUE, NOW(), 1),
(7, 3, 1, TRUE, NOW(), 2),
(8, 3, 2, TRUE, NOW(), 2),
(9, 3, 3, TRUE, NOW(), 2),
(10, 4, 1, TRUE, NOW(), 1),
(11, 4, 2, TRUE, NOW(), 1),
(12, 4, 4, TRUE, NOW(), 1);

-- Applicant Tasks (Some completed, some pending)
INSERT INTO `applicant_tasks_hr1` (`id`, `user_id`, `task_id`, `job_posting_id`, `completed`, `completed_at`, `submitted_document`, `notes`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 5, TRUE, DATE_SUB(NOW(), INTERVAL 5 DAY), '/documents/license_9.pdf', 'License verified', NOW(), NOW()),
(2, 9, 4, 5, TRUE, DATE_SUB(NOW(), INTERVAL 4 DAY), '/documents/bgcheck_9.pdf', 'Background check cleared', NOW(), NOW()),
(3, 9, 7, 5, TRUE, DATE_SUB(NOW(), INTERVAL 3 DAY), NULL, 'Orientation completed', NOW(), NOW()),
(4, 9, 8, 5, FALSE, NULL, NULL, 'Pending', NOW(), NOW()),
(5, 9, 9, 5, FALSE, NULL, NULL, 'Pending', NOW(), NOW()),
(6, 8, 1, 4, TRUE, DATE_SUB(NOW(), INTERVAL 2 DAY), '/documents/license_8.pdf', 'License submitted', NOW(), NOW()),
(7, 8, 4, 4, FALSE, NULL, NULL, 'In progress', NOW(), NOW()),
(8, 7, 1, 3, TRUE, DATE_SUB(NOW(), INTERVAL 1 DAY), '/documents/license_7.pdf', 'License verified', NOW(), NOW()),
(9, 7, 4, 3, FALSE, NULL, NULL, 'Awaiting submission', NOW(), NOW());

-- Onboarding Tasks (matches existing structure)
INSERT INTO `onboarding_tasks_hr1` (`id`, `title`, `department`, `category`, `completed`, `assigned_to`, `user_id`, `required_for_phase`, `created_at`, `updated_at`) VALUES
(1, 'Upload ID Documentation', 'Emergency', 'Pre-onboarding', 1, 'candidate', 5, 1, NOW(), NOW()),
(2, 'Health Safety E-Learning', 'Laboratory', 'Training', 0, 'staff', 6, 2, NOW(), NOW()),
(3, 'Verify Medical License', 'Emergency', 'Pre-onboarding', 0, 'admin', NULL, 1, NOW(), NOW()),
(4, 'IT Account Setup', 'IT', 'IT Setup', 0, 'admin', 9, 1, NOW(), NOW()),
(5, 'Orientation Session', 'HR', 'Orientation', 0, 'staff', 5, 2, NOW(), NOW()),
(6, 'Complete Background Check', 'All', 'Pre-onboarding', 1, 'candidate', 9, 1, NOW(), NOW()),
(7, 'Submit Education Transcripts', 'All', 'Pre-onboarding', 0, 'candidate', 8, 1, NOW(), NOW());

-- =====================================================
-- 6. QUESTION SETS & QUESTIONS DATA
-- =====================================================

INSERT INTO `question_sets_hr1` (`id`, `title`, `description`, `type`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Technical Skills Assessment', 'Evaluate technical competencies for healthcare positions', 'assessment', TRUE, 1, NOW(), NOW()),
(2, 'Behavioral Interview Questions', 'Standard behavioral interview questions', 'interview', TRUE, 1, NOW(), NOW()),
(3, 'Clinical Knowledge Test', 'Test clinical knowledge and decision-making', 'assessment', TRUE, 2, NOW(), NOW()),
(4, 'Cultural Fit Survey', 'Assess alignment with hospital values', 'survey', TRUE, 1, NOW(), NOW());

INSERT INTO `questions_hr1` (`id`, `question_set_id`, `question_text`, `question_type`, `options`, `is_required`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'How many years of experience do you have in your field?', 'text', NULL, TRUE, 1, NOW(), NOW()),
(2, 1, 'Rate your proficiency with electronic health records (EHR) systems', 'rating', NULL, TRUE, 2, NOW(), NOW()),
(3, 1, 'Which certifications do you currently hold?', 'multiple-choice', '["ACLS", "BLS", "PALS", "CCRN", "Other"]', TRUE, 3, NOW(), NOW()),
(4, 2, 'Describe a time when you had to handle a difficult patient situation', 'text', NULL, TRUE, 1, NOW(), NOW()),
(5, 2, 'How do you prioritize tasks in a high-pressure environment?', 'text', NULL, TRUE, 2, NOW(), NOW()),
(6, 2, 'Give an example of how you worked effectively in a team', 'text', NULL, TRUE, 3, NOW(), NOW()),
(7, 3, 'What is your approach to patient safety?', 'text', NULL, TRUE, 1, NOW(), NOW()),
(8, 3, 'How do you stay updated with medical best practices?', 'text', NULL, TRUE, 2, NOW(), NOW()),
(9, 4, 'How important is teamwork in healthcare?', 'rating', NULL, TRUE, 1, NOW(), NOW()),
(10, 4, 'Are you comfortable working in a diverse environment?', 'yes-no', NULL, TRUE, 2, NOW(), NOW());

-- Applicant Responses (Sample assessment answers)
INSERT INTO `applicant_responses_hr1` (`id`, `user_id`, `question_id`, `question_set_id`, `response_text`, `response_value`, `submitted_at`) VALUES
(1, 5, 1, 1, '5 years of experience in emergency nursing', NULL, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(2, 5, 2, 1, NULL, '4', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(3, 5, 3, 1, NULL, 'ACLS, BLS', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(4, 6, 1, 1, '3 years in laboratory technology', NULL, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(5, 6, 2, 1, NULL, '5', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(6, 7, 4, 2, 'I once handled a patient who was very anxious about their procedure. I took time to explain everything clearly and provided emotional support.', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 7, 5, 2, 'I prioritize based on urgency and patient safety, always ensuring critical cases come first.', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(8, 8, 7, 3, 'Patient safety is my top priority. I always double-check medications and follow protocols strictly.', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(9, 9, 9, 4, NULL, '5', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(10, 9, 10, 4, NULL, 'Yes', DATE_SUB(NOW(), INTERVAL 10 DAY));

-- =====================================================
-- 7. RECOGNITIONS DATA
-- =====================================================

INSERT INTO `award_categories_hr1` (`id`, `name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Outstanding Performance', '🌟', NOW(), NOW()),
(2, 'Team Player', '🤝', NOW(), NOW()),
(3, 'Innovation', '💡', NOW(), NOW()),
(4, 'Leadership', '⭐', NOW(), NOW()),
(5, 'Patient Care Excellence', '❤️', NOW(), NOW());

INSERT INTO `recognitions_hr1` (`id`, `from`, `to`, `reason`, `award_type`, `date`, `congratulations`, `boosts`, `created_at`, `updated_at`) VALUES
(1, 'HR Department', 'Robert Taylor', 'Exceptional attention to detail and patient care. Consistently goes above and beyond in radiology services.', 'Outstanding Performance', CURDATE(), 15, 8, NOW(), NOW()),
(2, 'Dr. Smith', 'Jessica Martinez', 'Outstanding clinical knowledge and excellent communication with patients and staff in pharmacy operations.', 'Patient Care Excellence', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 12, 5, NOW(), NOW()),
(3, 'Rehabilitation Team', 'David Kim', 'Innovative treatment approaches and excellent patient outcomes in physical therapy.', 'Innovation', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 8, 3, NOW(), NOW()),
(4, 'Lab Manager', 'Emily Rodriguez', 'Strong team collaboration and support for colleagues in laboratory operations.', 'Team Player', DATE_SUB(CURDATE(), INTERVAL 15 DAY), 10, 4, NOW(), NOW());

-- =====================================================
-- 8. EVALUATION CRITERIA DATA
-- =====================================================

INSERT INTO `evaluation_criteria_hr1` (`id`, `label`, `section`, `weight`, `created_at`, `updated_at`) VALUES
(1, 'Clinical Skills', 'A', 40, NOW(), NOW()),
(2, 'Communication', 'B', 30, NOW(), NOW()),
(3, 'Problem Solving', 'C', 20, NOW(), NOW()),
(4, 'Teamwork', 'A', 25, NOW(), NOW()),
(5, 'Professionalism', 'B', 35, NOW(), NOW());

INSERT INTO `applicant_evaluations_hr1` (`id`, `user_id`, `criteria_id`, `score`, `evaluator_id`, `comments`, `evaluated_at`) VALUES
(1, 7, 1, 85.50, 1, 'Strong clinical skills demonstrated', NOW()),
(2, 7, 2, 90.00, 1, 'Excellent communication', NOW()),
(3, 7, 3, 88.00, 1, 'Good problem-solving abilities', NOW()),
(4, 8, 1, 92.00, 2, 'Exceptional clinical knowledge', NOW()),
(5, 8, 2, 88.50, 2, 'Very professional', NOW()),
(6, 6, 1, 78.00, 1, 'Good technical skills', NOW()),
(7, 6, 4, 85.00, 1, 'Works well in teams', NOW());

-- =====================================================
-- 9. SETTINGS DATA
-- =====================================================

INSERT INTO `settings_hr1` (`id`, `key`, `value`, `type`, `description`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'Concord HR1', 'string', 'System name displayed in the application', 1, NOW(), NOW()),
(2, 'max_applications_per_job', '100', 'integer', 'Maximum number of applications allowed per job posting', 1, NOW(), NOW()),
(3, 'auto_notify_on_status_change', 'true', 'boolean', 'Automatically notify applicants when status changes', 1, NOW(), NOW()),
(4, 'default_task_set_required', 'true', 'boolean', 'Require task sets for all new job postings', 1, NOW(), NOW()),
(5, 'recognition_approval_required', 'false', 'boolean', 'Require approval before posting recognitions', 1, NOW(), NOW());

-- =====================================================
-- 10. ACTIVITY LOGS DATA (Sample)
-- =====================================================

INSERT INTO `activity_logs_hr1` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'created', 'Job', 1, 'Created new job posting: Registered Nurse - Emergency Department', '192.168.1.100', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 1, 'updated', 'Applicant', 3, 'Updated applicant status to interviewing', '192.168.1.100', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 2, 'created', 'Recognition', 1, 'Posted recognition for Robert Taylor', '192.168.1.101', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 1, 'created', 'TaskSet', 1, 'Created task set: Medical License Requirements', '192.168.1.100', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(5, 1, 'created', 'QuestionSet', 1, 'Created question set: Technical Skills Assessment', '192.168.1.100', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 6 DAY));

-- =====================================================
-- END OF SAMPLE DATA
-- =====================================================

-- Learning Modules Data
INSERT INTO `learning_modules_hr1` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Basic Clinical Hygiene', NOW(), NOW()),
(2, 'EHR Mastery v2', NOW(), NOW()),
(3, 'Patient Ethics 101', NOW(), NOW()),
(4, 'Emergency Protocols', NOW(), NOW()),
(5, 'HIPAA Compliance Training', NOW(), NOW());

-- User Learning Modules Assignments
INSERT INTO `user_learning_modules_hr1` (`id`, `user_id`, `learning_module_id`, `completed`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, NOW(), NOW()),
(2, 5, 2, 0, NOW(), NOW()),
(3, 6, 3, 1, NOW(), NOW()),
(4, 6, 4, 0, NOW(), NOW()),
(5, 9, 1, 1, NOW(), NOW()),
(6, 9, 5, 0, NOW(), NOW());

-- Reset AUTO_INCREMENT values
ALTER TABLE `users_hr1` AUTO_INCREMENT = 15;
ALTER TABLE `applicants_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `job_postings_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `applications_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `task_sets_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `tasks_hr1` AUTO_INCREMENT = 15;
ALTER TABLE `onboarding_tasks_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `question_sets_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `questions_hr1` AUTO_INCREMENT = 15;
ALTER TABLE `recognitions_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `award_categories_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `evaluation_criteria_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `applicant_evaluations_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `learning_modules_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `user_learning_modules_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `applicant_responses_hr1` AUTO_INCREMENT = 15;
ALTER TABLE `applicant_tasks_hr1` AUTO_INCREMENT = 15;
ALTER TABLE `settings_hr1` AUTO_INCREMENT = 10;
ALTER TABLE `activity_logs_hr1` AUTO_INCREMENT = 10;

-- =====================================================
-- END OF SCHEMA AND DATA
-- =====================================================

