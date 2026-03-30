# Authentication System Documentation

## Overview

The authentication system for the Concord application is implemented using Laravel's built-in authentication framework. It provides comprehensive user management including registration, login, logout, and role-based access control with automatic redirection to appropriate dashboards.

## User Registration

### Process Flow

The registration process (`store` method) handles new user account creation with the following steps:

1. **Input Validation**
   - `username`: Required, string, max 50 characters, must be unique
   - `email`: Required, valid email format, must be unique
   - `password`: Required, minimum 8 characters, must be confirmed
   - `role_slug`: Required, must be one of the predefined roles
   - `first_name`: Required, string, max 255 characters
   - `last_name`: Required, string, max 255 characters

2. **User Type Determination**
   - If `role_slug` contains 'patient': user_type = 'patient'
   - Otherwise: user_type = 'staff'

3. **Database Transaction**
   - Creates a `User` record with:
     - Hashed password using Laravel's Hash facade
     - user_type, role_slug, is_active = 1
   - For staff users: Creates an `Employee` record linked to the user
   - For patient users: Creates a `Patient` record with auto-generated MRN

4. **Post-Registration**
   - Automatically logs in the new user
   - Redirects to role-appropriate dashboard

## User Login

### Process Flow

The login process (`login` method) supports flexible authentication:

1. **Input Validation**
   - `login`: Required, can be either username or email
   - `password`: Required

2. **Credential Verification**
   - Determines login type (email vs username) using PHP's `filter_var`
   - Validates credentials against active users (is_active = 1, deleted_at = null)
   - Uses Laravel's `Auth::validate()` for secure credential checking

3. **Successful Login**
   - Logs in the user using `Auth::login()`
   - Regenerates session for security
   - Updates user's `last_login_at` and `last_login_ip`
   - Redirects to role-appropriate dashboard

4. **Failed Login**
   - Returns with error message: "The provided credentials do not match our records or account is inactive."

### Two-Factor Authentication 

The codebase includes commented-out 2FA functionality:
- OTP generation and email sending
- OTP verification with attempt limiting (max 5 attempts)
- Session-based 2FA flow

## User Logout

### Process Flow

The logout process (`destroy` method) ensures secure session termination:

1. **Session Cleanup**
   - Logs out user using `Auth::logout()`
   - Invalidates the current session
   - Regenerates CSRF token

2. **Redirection**
   - Redirects to login page

## Role-Based Redirection

After successful authentication, users are automatically redirected to their role-specific dashboard using the `redirectByUserRole` method:

### HR Module Dashboards
- `admin_hr1` → `admin.hr1.dashboard`
- `admin_hr2` → `admin.hr2.dashboard`
- `admin_hr3` → `admin.hr3.dashboard`
- `admin_hr4` → `admin.hr4.dashboard`

### Logistics Module Dashboards
- `admin_logistics1` → `admin.logistics1.dashboard`
- `admin_logistics2` → `admin.logistics2.dashboard`

### Core Module Dashboards
- `admin_core1` → `core1.admin.dashboard`
- `admin_core2` → `admin.core2.dashboard`
- `core_admin` → `core2.dashboard`

### Financials Dashboard
- `admin_financials` → `admin.financials.dashboard`

### Staff Dashboards
- `doctor` → `core1.doctor.dashboard`
- `nurse` → `core1.nurse.dashboard`
- `head_nurse` → `core1.nurse.dashboard`
- `receptionist` → `core1.receptionist.dashboard`
- `billing_officer` → `core1.billing.dashboard`
- `employee` → `hr.dashboard`

### Patient Dashboard
- `patient` → `core1.patient.dashboard`

### Default
- Any unmatched role → `/` (home page)

## User Types and Roles

### User Types
- **staff**: Healthcare and administrative personnel
- **patient**: Medical patients

### Allowed Role Slugs
- admin_hr1, admin_hr2, admin_hr3, admin_hr4
- admin_logistics1, admin_logistics2
- admin_core1, admin_core2, core_admin
- admin_financials
- doctor, nurse, head_nurse, receptionist, billing_officer
- employee
- patient

## Security Features

- **Password Hashing**: Uses Laravel's secure hashing
- **Session Security**: Session regeneration on login/logout
- **Account Status**: Only active users can authenticate
- **Soft Deletes**: Respects deleted_at for user accounts
- **Input Validation**: Comprehensive server-side validation
- **CSRF Protection**: Laravel's built-in CSRF protection
- **OTP System**: Infrastructure in place for future 2FA implementation

## Database Models

### User Model
- Core authentication model
- Fields: username, email, password, user_type, role_slug, is_active, last_login_at, last_login_ip

### Employee Model
- Linked to User for staff members
- Fields: employee_id, first_name, last_name, hire_date, is_on_duty

### Patient Model
- Linked to User for patients
- Fields: patient_id, mrn, first_name, last_name, email, registration_status

### OTP Model
- Fields: identifier, otp_code, expires_at, attempts, is_used

## Notes

- MRN (Medical Record Number) is auto-generated for patients
- All database operations use transactions for data integrity
- Role-based redirection ensures users only access appropriate interfaces
