# HR1 Module Documentation

## Overview

The HR1 module is the primary Human Resources management system for recruitment and onboarding processes. It handles job postings, applicant management, new hire onboarding, performance assessments, training validation, and employee recognition. The module is exclusively accessible to users with the `admin_hr1` role and integrates with other HR modules (HR2, HR3, HR4) for a comprehensive workflow.

## Authorization

All HR1 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_hr1'` can access HR1 functionalities
- Unauthorized access results in a 403 Forbidden response

## Dashboard

### Controller: AdminHr1DashboardController

The HR1 dashboard provides an overview of recruitment metrics and recent activities.

#### Metrics Displayed
- **Total Applicants**: Count of all applicants in the system
- **Accepted Count**: Number of applicants with status 'accepted' or 'onboarded'
- **Rejected Count**: Number of applicants with status 'rejected'
- **Active Jobs**: Number of active job postings

#### Charts and Data
- **Status Distribution Chart**: Breakdown of applicants by application status
- **Department Distribution Chart**: Applicants grouped by department
- **Recent Applications**: Last 5 applications with department information

#### Data Sources
- `applicants_hr1` table for applicant counts and status breakdowns
- `job_postings_hr1` table for active job counts
- `departments_hr2` table for department names

## Recruitment Management

### Controller: AdminRecruitmentController

Manages job postings and integrates with HR4 job requests.

#### Features

##### Job Postings List
- Displays all job postings ordered by creation date
- Shows HR4 open jobs for potential publishing
- Metrics: Total needed applicants, active/inactive posting counts

##### Job Posting Details
- View individual posting information
- Toggle active/inactive status

##### Publishing from HR4
- Publish HR4 job requests as HR1 postings
- Prevents duplicate publishing
- Maps HR4 fields to HR1 posting structure:
  - `title`, `description`, `positions_available` → `needed_applicants`
  - Sets default `track_type = 'residency'`
  - Uses `department` as `dept_code`

#### Database Tables
- `job_postings_hr1`: HR1 job postings
- `available_jobs_hr4`: HR4 job requests

## Applicant Management

### Controller: ApplicantManagementController

Handles applicant lifecycle from application to acceptance.

#### Features

##### Applicant Listing
- Paginated list with filtering by:
  - Department
  - Specialization
  - Application status
- Dynamic specialization dropdown based on selected department

##### Applicant Details
- Comprehensive applicant information
- Interview schedule details (joined from HR3)
- Validator information for interviews

##### Resume Download
- Decompresses and serves stored PDF resumes
- Validates file existence before serving

##### Status Updates
- Update application status: pending, under_review, interview, accepted, rejected, onboarded
- Automatic actions on status change to 'accepted':
  - Creates new hire record in `new_hires_hr1`
  - Creates onboarding assessment record in `onboarding_assessments_hr1`

#### Database Tables
- `applicants_hr1`: Main applicant data
- `departments_hr2`: Department information
- `department_specializations_hr2`: Specialization data
- `interview_schedule_hr3`: Interview scheduling
- `new_hires_hr1`: New hire records
- `onboarding_assessments_hr1`: Assessment tracking

## New Hire Management

### Controller: NewHireController

Manages the onboarding process for accepted applicants.

#### Features

##### New Hire Listing
- Filtered view by department, specialization, status
- Integration with assessment status and user accounts
- Recent HR4 sync history

##### New Hire Details
- Complete hire information with department joins

##### Resume Download
- Same decompression logic as applicant resumes

##### Assessment Validation
- Validates HR2 assessment results
- Calculates final pass/fail based on average score (≥75% = passed)
- Updates assessment records with validation details

##### Status Updates
- Status progression: onboarding → active → inactive
- **Activation Process** (status = 'active'):
  - Validates assessment completion and passing status
  - Creates user account with employee role
  - Creates employee record with generated credentials
  - Sends welcome email with login details
  - Updates assessment status to 'completed'

##### HR4 Synchronization
- Manual handover of active employees to HR4
- Creates records in `hired_users_hr4` with compensation setup
- Prevents duplicate syncs

#### Database Tables
- `new_hires_hr1`: New hire tracking
- `onboarding_assessments_hr1`: Assessment data
- `onboarding_assessment_scores_hr1`: Individual scores
- `users`: User accounts
- `employees`: Employee records
- `hired_users_hr4`: HR4 handover tracking

## Assessment Performance

### Controller: AdminAssessmentPerformanceController

Manages final validation of onboarding assessments.

#### Features

##### Assessment Overview
- Lists all onboarding assessments with pagination
- Metrics: Total passed, failed, and assessed counts

##### Assessment Details
- Individual assessment records with competency scores
- Joins assessor and validator employee information
- Calculates average rating across all scores

##### Assessment Validation
- Validates assessments with status 'assessed'
- Calculates final status based on average rating (≥75% = passed)
- Updates assessment record with validation details
- Marks individual scores with validator information
- Uses database transactions for data integrity

#### Database Tables
- `onboarding_assessments_hr1`: Assessment records
- `onboarding_assessment_scores_hr1`: Individual competency scores
- `employees`: Assessor/validator information

## Training Performance

### Controller: AdminTrainingPerformanceController

Handles validation of employee training scores from HR2.

#### Features

##### Employee Listing
- Filtered by department and specialization
- Dynamic specialization loading via AJAX

##### Training Scores Review
- Detailed view of employee training scores
- Competency information from HR2
- Evaluator details
- Normalized grade calculation (weighted average)

##### Score Validation
- Finalizes training grades
- Calculates percentage based on total/max points
- Stores validated results in HR1 table
- Updates HR2 scores status to 'completed'

#### Database Tables
- `employees`: Employee information
- `employee_training_scores_hr2`: Training scores from HR2
- `competency_hr2`: Competency definitions
- `validated_training_performance_hr1`: HR1 validation records

## Social Recognition

### Controller: AdminSocialRecognitionController

Manages employee recognition and rewards system.

#### Features

##### Recognition Posts Management
- CRUD operations for recognition posts
- Image upload and storage
- Employee selection from active new hires

##### Recognition Metrics
- Total posts, likes, comments, engagement rate

##### Bonus Awards
- Automatic bonus awarding via HR4 integration
- Updates direct compensation (bonus field)
- Creates indirect compensation records
- Default bonus amount: $500

#### Database Tables
- `recognition_posts`: Recognition content
- `employees`: Recognized employees
- `direct_compensation` (HR4): Bonus tracking
- `indirect_compensation` (HR4): Benefit records

## Workflow Integration

### HR Module Dependencies
- **HR2**: Provides assessment scores, training data, departments, specializations
- **HR3**: Supplies interview scheduling information
- **HR4**: Receives new hires, handles compensation, provides job requests

### Data Flow
1. **Recruitment**: HR4 job requests → HR1 postings → Applicant applications
2. **Assessment**: HR1 applicants → HR2 assessments → HR1 validation → New hires
3. **Training**: HR2 training scores → HR1 validation → Employee records
4. **Onboarding**: HR1 new hires → User/employee creation → HR4 handover

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_hr1 role
- **Data Validation**: Comprehensive input validation on all forms
- **Transaction Safety**: Database transactions for critical operations
- **File Security**: Secure storage and serving of resume files
- **Audit Trail**: Tracking of validations, status changes, and syncs

## Error Handling

- Graceful error messages for validation failures
- Rollback mechanisms for failed transactions
- Existence checks before operations
- User-friendly feedback via session flashes

## Performance Optimizations

- **Pagination**: Large datasets paginated (10-15 items per page)
- **Efficient Queries**: Selective column retrieval, proper joins
- **AJAX Loading**: Dynamic specialization loading without full page reload
- **Indexing**: Assumes proper database indexing on frequently queried columns
