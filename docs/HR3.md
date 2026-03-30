# HR3 Module Documentation

## Overview

The HR3 module handles operational HR functions including attendance tracking, shift management, leave administration, interview scheduling, claims processing, and training coordination. It serves as the operational backbone connecting employee scheduling with HR2's development programs and HR1's recruitment processes. The module integrates with HR1 (applicant interviews), HR2 (training schedules), and HR4 (compensation) for comprehensive workforce management.

## Authorization

All HR3 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_hr3'` can access HR3 functionalities
- Unauthorized access results in a 403 Forbidden response

## Attendance Management

### Controller: AdminAttendanceController

Manages automated attendance tracking via QR code system.

#### Features

##### Attendance Station
- Generates unique QR tokens with 30-second expiration
- Cached token verification for security
- Auto-expiring tokens prevent reuse

##### QR Code Generation
- Creates scannable QR codes pointing to verification endpoint
- Token-based secure attendance marking

#### Security Features
- **Token Expiration**: 30-second cache lifetime
- **Unique Tokens**: UUID-based token generation
- **Cache Storage**: Laravel Cache for fast verification

#### Database Tables
- Attendance logs stored in `attendance_logs_hr3`

## Interview Scheduling

### Controller: AdminInterviewScheduleController

Manages interview scheduling for HR1 applicants.

#### Features

##### Schedule Management
- Department and specialization filtering
- Dynamic applicant loading based on status
- Interview scheduling with date/time/location

##### Automated Notifications
- Email notifications to applicants
- InterviewScheduleMail integration
- Error logging for failed emails

##### AJAX Data Loading
- Department-based specialization filtering
- Status-based applicant filtering (interview status)

#### Database Tables
- `interview_schedule_hr3`: Schedule records
- `applicants_hr1`: Applicant data
- `departments_hr2`: Department information

## Leave Management

### Controller: AdminLeaveManagementController

Handles employee leave requests and archiving.

#### Features

##### Leave Request Processing
- View pending leave requests from ESS
- Approve/reject/close requests
- Automatic shift deactivation for approved leaves

##### Archival System
- Move processed requests to archive table
- Preserve request history and decisions
- Link to original ESS request

##### Transaction Safety
- Database transactions for data integrity
- Error logging and rollback mechanisms

#### Database Tables
- `ess_request_hr2`: Leave requests
- `archived_leave_hr3`: Processed leave history
- `shifts_hr3`: Shift deactivation
- `employees`: Employee information

## Shift Management

### Controller: AdminShiftController

Manages employee shift assignments and scheduling.

#### Features

##### Shift Assignment
- Department-based employee selection
- Fixed shift types: Morning (08:00-17:00), Afternoon (14:00-22:00), Night (22:00-08:00)
- Multi-day assignment capability

##### Employee Organization
- Group employees by specialization within departments
- Dynamic employee loading by department

##### Shift Operations
- Create multiple shift instances
- Delete individual shifts

#### Database Tables
- `shifts_hr3`: Shift records
- `employees`: Employee assignments

## Shift Request Management

### Controller: AdminShiftRequestController

Handles employee shift change requests.

#### Features

##### Request Processing
- View pending shift requests
- Approve or reject requests
- Automatic status updates

##### Request Details
- Employee information display
- Shift details (name, day, time)
- Request timestamps

#### Database Tables
- `shifts_hr3`: Shift request data
- `employees`: Employee information

## Claims Management

### Controller: AdminClaimsController

Processes employee claims and reimbursements.

#### Features

##### Claims Processing
- List all claims with employee details
- Approve or reject claims
- Validator tracking

##### Status Management
- Update claim status
- Link validator information
- Audit trail maintenance

#### Database Tables
- `claims_hr3`: Claims records
- `employees`: Employee and validator data

## Training Scheduling

### Controller: AdminTrainingScheduleController

Coordinates face-to-face training sessions with HR2 competencies.

#### Features

##### Training Coordination
- Schedule training for HR2-enrolled competencies
- Trainer assignment from HR2 admins
- Venue and time management

##### Eligibility Filtering
- Only show HR2-enrolled employees
- Competency-specific scheduling
- Training history tracking

##### AJAX Data Loading
- Employee competency retrieval
- Department/specialization info

#### Database Tables
- `training_schedule_hr3`: Training sessions
- `competency_enroll_hr2`: Enrollment data
- `employees`: Employee and trainer info

## Timesheet Management

### Controller: AdminTimesheetController

Provides attendance log oversight.

#### Features

##### Attendance Logs
- View all attendance records
- Employee and department information
- Chronological ordering

##### Log Details
- Clock in/out times
- Employee position and specialization
- Department association

#### Database Tables
- `attendance_logs_hr3`: Attendance records
- `employees`: Employee data
- `departments_hr2`: Department information

## Workflow Integration

### HR Module Dependencies
- **HR1**: Provides applicants for interviews, receives interview schedules
- **HR2**: Supplies competency enrollments for training, receives training schedules
- **HR4**: May receive attendance/payroll data

### Data Flow
1. **Recruitment**: HR1 applicants → HR3 interview scheduling → Email notifications
2. **Training**: HR2 enrollments → HR3 training scheduling → HR2 evaluations
3. **Operations**: Employee requests → ESS (HR2) → HR3 processing → Archive
4. **Attendance**: QR scanning → HR3 logs → Timesheet review

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_hr3 role
- **Token Security**: Short-lived QR tokens for attendance
- **Data Validation**: Comprehensive input validation
- **Transaction Safety**: Database transactions for critical operations
- **Audit Trail**: Status change tracking and validator logging

## Performance Optimizations

- **AJAX Loading**: Dynamic data loading without page refreshes
- **Efficient Queries**: Optimized joins and selective data retrieval
- **Caching**: Token caching for attendance verification
- **Pagination**: Large datasets handled appropriately

## Error Handling

- Graceful error messages for validation failures
- Email failure logging without breaking processes
- Transaction rollbacks for failed operations
- User-friendly feedback via session messages

## Integration Points

### External Systems
- **Email System**: Laravel Mail for notifications
- **Cache System**: Laravel Cache for token management
- **Logging System**: Laravel Log for error tracking

### API Endpoints
- AJAX endpoints for dynamic data loading
- QR verification endpoints for attendance
- CRUD operations for all management functions

## Business Rules

### Attendance
- QR tokens expire in 30 seconds
- Secure token verification required

### Leave Processing
- Approved leaves deactivate associated shifts
- All processed requests are archived
- Original requests deleted after archiving

### Shift Management
- Fixed hospital hours by shift type
- Multi-day assignments supported
- Request approval workflow

### Interview Scheduling
- Only applicants with 'interview' status eligible
- Automatic email notifications
- Validator tracking for audit

### Training Scheduling
- Must have HR2 competency enrollment
- Trainer assignment from HR2 admins
- Competency-specific sessions
