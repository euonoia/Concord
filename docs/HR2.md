# HR2 Module Documentation

## Overview

The HR2 module is the core Human Resources management system focused on employee development, competency management, training, and succession planning. It handles learning modules, competency verification, training evaluations, onboarding assessments, and employee self-service (ESS) requests. The module integrates with HR1 (recruitment), HR3 (scheduling), and HR4 (compensation) for a comprehensive employee lifecycle management system.

## Authorization

All HR2 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_hr2'` can access HR2 functionalities
- Unauthorized access results in a 403 Forbidden response

## Dashboard

### Controller: AdminDashboardController

The HR2 dashboard provides comprehensive metrics and analytics for HR operations.

#### Key Metrics
- **Total Employees**: Active employee count
- **Active Competencies**: Number of active competency definitions
- **Total Departments**: Active department count
- **Pending ESS Requests**: Outstanding employee self-service requests
- **Active Learning Modules**: Current learning content count
- **Succession Positions**: Positions with succession planning
- **Upcoming Trainings**: Training sessions in next 30 days
- **Recent Completions**: Training evaluations in last 30 days

#### Advanced Analytics
- **Top Performers**: Top 5 employees by weighted training average
- **ESS Requests by Status**: Distribution of request statuses
- **Training Completion Rate**: Percentage of completed vs total evaluations
- **Succession Candidates**: Active succession pipeline count
- **Department Metrics**: Employee distribution by department
- **Average Performance Score**: Overall training performance average
- **Performance Distribution**: Score ranges across employees

#### Data Sources
- `employees`: Employee records
- `competency_hr2`: Competency definitions
- `departments_hr2`: Department information
- `ess_request_hr2`: Employee requests
- `learning_modules_hr2`: Learning content
- `succession_positions_hr2`: Succession planning
- `training_sessions_hr2`: Training schedules
- `employee_training_scores_hr2`: Training evaluations

## Competency Management

### Controller: CompetencyController

Manages the competency framework for different departments and specializations.

#### Features

##### Competency Listing
- Filtered by department and specialization
- Shows associated new hire counts
- Auto-generated competency codes (format: DEPT-SPEC-XXX)

##### Competency Creation
- Department and specialization selection
- Competency group categorization
- Description and rotation order
- Automatic code generation

##### Competency Deletion
- Soft removal of competencies

##### AJAX Filters
- Dynamic specialization loading by department

#### Database Tables
- `competency_hr2`: Competency definitions
- `departments_hr2`: Department data
- `department_specializations_hr2`: Specialization mappings
- `new_hires_hr1`: New hire associations

### Controller: AdminCompetencyVerificationController

Handles verification of employee competency completions.

#### Features

##### Completion Listing
- Search by employee name or ID
- Filter by verification status (verified/pending)
- Employee and competency details

##### Competency Verification
- Mark completions as verified
- Add verification notes
- Update completion status
- Link to verifier's employee record

#### Database Tables
- `employee_competency_completion_hr2`: Completion records
- `employees`: Employee information

## Learning Management

### Controller: AdminLearningController

Manages learning modules and automatic enrollment.

#### Features

##### Module Creation
- Competency-linked modules
- Department and specialization targeting
- Module types: Compliance, Clinical, Simulation, Research, Other
- Mandatory vs optional designation
- Duration tracking

##### Automatic Enrollment
- Auto-enroll residents in matching modules
- Based on department and specialization

##### AJAX Utilities
- Dynamic specialization loading
- Competency selection by dept/spec
- Module code auto-generation

#### Database Tables
- `learning_modules_hr2`: Module definitions
- `course_enrolls_hr2`: Enrollment records
- `competency_hr2`: Linked competencies
- `employees`: Resident data

### Controller: AdminLearningEnrollController

Provides manual enrollment capabilities.

#### Features

##### Employee Selection
- List all employees with department information
- Filter by department/specialization

##### Module Assignment
- Show available modules by employee specialization
- Display current enrollment status
- Bulk module assignment

##### Enrollment Processing
- Create course enrollments
- Update competency enrollments
- Create competency completion records
- Mark as pending verification

#### Database Tables
- `employees`: Employee records
- `learning_modules_hr2`: Available modules
- `course_enrolls_hr2`: Course enrollments
- `competency_enroll_hr2`: Competency enrollments
- `employee_competency_completion_hr2`: Completion tracking

### Controller: AdminLearningMaterialsController

Manages learning materials for modules.

#### Features

##### Material Organization
- Department and specialization filtering
- Module selection within dept/spec

##### Material Upload
- File uploads (PDF, DOC, PPT) up to 10MB
- URL links for external resources
- Material type categorization

##### Material Management
- List materials by module
- Delete materials with file cleanup

#### Database Tables
- `learning_materials_hr2`: Material records
- `learning_modules_hr2`: Module associations
- `departments_hr2`: Department data

## Training Management

### Controller: AdminTrainingController

Provides training oversight and reporting.

#### Features

##### Training Analytics
- Department and specialization filtering
- Competency-based employee eligibility
- Training schedule integration (HR3)
- Evaluation score tracking

##### Employee Reports
- Eligible employees with completion status
- Training schedules and scores
- Evaluator information
- Validated performance data

#### Database Tables
- `validated_training_performance_hr1`: HR1 validations
- `employees`: Employee data
- `competency_hr2`: Competency definitions
- `training_schedule_hr3`: HR3 schedules
- `employee_training_scores_hr2`: Training scores

### Controller: AdminTrainingEvaluationController

Handles training evaluation and scoring.

#### Features

##### Evaluation Matrix
- Department/specialization/competency filtering
- Employee eligibility checking
- Training schedule validation
- Duplicate evaluation prevention

##### Score Submission
- Competency-based evaluation criteria
- Score validation and calculation
- Automatic status updates
- Enrollment status progression

##### AJAX Data Loading
- Dynamic filters for dept/spec/competency
- Eligible employee lists with training status

#### Database Tables
- `employees`: Employee records
- `competency_hr2`: Competency definitions
- `employee_training_scores_hr2`: Evaluation scores
- `training_schedule_hr3`: Training schedules
- `competency_enroll_hr2`: Enrollment tracking
- `employee_competency_completion_hr2`: Completion records

## Onboarding Assessment

### Controller: AdminOnboardingAssessmentController

Manages assessment of new hires from HR1.

#### Features

##### Assessment Listing
- All onboarding assessments from HR1
- Validated assessment tracking

##### Reference ID Validation
- Check application reference IDs
- Prevent duplicate assessments

##### Assessment Matrix
- Predefined competency evaluation
- Technical Knowledge (40%), Communication (30%), Problem Solving (30%)

##### Score Calculation
- Weighted scoring system
- Automatic level determination (Advanced/Intermediate/Basic/Beginner)
- Status update to 'assessed'

#### Database Tables
- `onboarding_assessments_hr1`: Assessment records
- `onboarding_assessment_scores_hr1`: Individual scores
- `employees`: Assessor information

## Succession Planning

### Controller: AdminSuccessionController

Manages leadership succession and career progression.

#### Features

##### Succession Pipeline
- Active candidate management
- Readiness levels: Ready Now, 1-2 Years, 3+ Years, Emergency
- Training performance integration
- Position-based nominations

##### Candidate Management
- Add candidates to pipeline
- Development plan tracking
- Performance metric integration

##### Promotion Processing
- Candidate promotion execution
- Status updates to Fellowship
- HR4 promotion record creation
- Position title changes

##### Promoted Candidates History
- Track promotion history
- Old/new position details
- Promotion timestamps

#### Database Tables
- `successor_candidates_hr2`: Candidate pipeline
- `validated_training_performance_hr1`: Performance data
- `employees`: Employee records
- `department_position_titles_hr2`: Position definitions
- `promoted_employees_hr4`: HR4 promotion tracking

## ESS (Employee Self-Service) Management

### Controller: AdminEssController

Handles employee self-service requests.

#### Features

##### Request Management
- List all ESS requests with employee details
- Status tracking and updates

##### Status Processing
- Approve/Reject/Close requests
- Automatic shift deactivation for approved leave requests
- Request archiving after processing

##### Transaction Safety
- Database transactions for data integrity
- Logging for audit trails
- Error handling with rollback

#### Database Tables
- `ess_request_hr2`: ESS requests
- `employees`: Employee information
- `shifts_hr3`: HR3 shift data

## Workflow Integration

### HR Module Dependencies
- **HR1**: Provides new hires, assessment data, training validations
- **HR3**: Supplies training schedules, shift management, interview data
- **HR4**: Receives promotions, compensation updates

### Data Flow
1. **Competency Setup**: HR2 defines competencies → Learning modules created → Employees enrolled
2. **Training**: HR3 schedules training → HR2 evaluates → HR1 validates → Performance recorded
3. **Onboarding**: HR1 applicants → HR2 assessment → HR1 validation → Employee activation
4. **Succession**: HR2 identifies candidates → Performance tracking → Promotion to HR4

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_hr2 role
- **Data Validation**: Comprehensive input validation
- **Transaction Safety**: Database transactions for critical operations
- **File Security**: Secure storage of learning materials
- **Audit Trail**: Status change tracking and logging

## Performance Optimizations

- **AJAX Loading**: Dynamic data loading without page refreshes
- **Efficient Queries**: Optimized joins and selective data retrieval
- **Pagination**: Large datasets paginated appropriately
- **Caching**: Assumed database indexing for performance

## Error Handling

- Graceful error messages for validation failures
- Prevention of duplicate operations
- Existence checks before processing
- User-friendly feedback via session messages
