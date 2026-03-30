# HR4 Module Documentation

## Overview

The HR4 module is the central Human Resources management system focused on core human capital, compensation, analytics, and payroll processing. It serves as the final authority for employee lifecycle management, integrating data from HR1 (recruitment), HR2 (development), and HR3 (operations) to provide comprehensive workforce analytics and compensation management.

## Authorization

All HR4 controllers enforce strict authorization:
- Only authenticated users with `role_slug = 'admin_hr4'` can access HR4 functionalities
- Unauthorized access results in a 403 Forbidden response

## Core Human Capital Management

### Controller: AdminCoreHumanCapitalController

Manages the complete employee lifecycle and succession planning.

#### Features

##### Employee Overview
- Complete employee listing with department and position details
- User account integration
- Job postings management
- Succession pipeline tracking
- Promoted employees history

##### Succession Management
- Active succession candidates by readiness level
- Promotion processing with rank-based advancement
- Automatic status updates to Fellowship
- Promotion history tracking

##### Employee Operations
- Status management (active/inactive/resigned/terminated)
- Position and department assignments

##### New Hire Processing
- Process HR1 hired users into employee records
- Automatic user account creation
- Compensation setup initialization

#### Database Tables
- `employees`: Core employee data
- `departments_hr2`: Department information
- `department_position_titles_hr2`: Position definitions
- `successor_candidates_hr2`: Succession pipeline
- `hired_users_hr4`: HR1 handover tracking
- `promoted_employees_hr4`: Promotion history
- `available_jobs_hr4`: Job postings

## Direct Compensation Management

### Controller: AdminDirectCompensationController

Handles salary administration and compensation planning.

#### Features

##### Monthly Compensation Generation
- Automated salary calculation for all employees
- Attendance-based overtime and shift allowances
- Position-based base salaries
- Monthly compensation records

##### Training Rewards Management
- HR1 training performance integration
- Bonus calculation and awarding
- Position-based reward eligibility

##### Job Postings Management
- Create, edit, update job postings
- Department and specialization filtering
- Competency-based job requirements
- Status management (open/closed)

##### Compensation Analytics
- Zero salary alerts
- Monthly compensation tracking
- Employee-specific compensation details

#### Database Tables
- `direct_compensation`: Monthly compensation records
- `employees`: Employee data
- `training_performance_hr1`: Training scores
- `available_jobs_hr4`: Job postings
- `department_specializations_hr2`: Specialization data
- `competency_hr2`: Competency requirements

## ESS (Employee Self-Service) Requests

### Controller: EssRequestController

Manages employee self-service requests from HR2.

#### Features

##### Request Processing
- Approve/reject payroll requests
- Status tracking and filtering
- Request details and history

##### HR2 Synchronization
- Sync requests from HR2 system
- Status updates and conflict resolution
- Automated data mapping

##### Request Analytics
- Pending, approved, rejected counts
- Request type filtering
- Employee-specific request history

#### Database Tables
- `payroll_ess_requests_hr4`: ESS request records
- `payroll_request_hr2`: HR2 request source
- `employees`: Employee information

## HR Analytics

### Controller: HRAnalyticsController

Provides comprehensive HR key performance indicators and analytics.

#### Features

##### KPI Dashboard
- Total headcount and active employees
- New hires (MTD/YTD)
- Vacancy rates and turnover
- Average tenure and promotion tracking

##### Trends Analysis
- Headcount trends over 12 months
- Department breakdown with percentages
- New hires in last 30 days
- Attrition analysis by reason

##### Department Health Scores
- Position fill rates
- Turnover calculations
- Health score algorithms

#### Analytics Data
- **Headcount Metrics**: Active/inactive counts, growth trends
- **Recruitment KPIs**: New hire rates, vacancy analysis
- **Retention Metrics**: Turnover rates, tenure averages
- **Succession KPIs**: Promotion tracking, pipeline readiness

## Payroll Analytics

### Controller: PayrollAnalyticsController

Provides detailed payroll and labor cost analytics.

#### Features

##### Payroll Summary
- Monthly and YTD payroll totals
- Deduction breakdowns
- Cost per employee calculations
- Budget vs actual comparisons

##### Cost Analysis
- Department-wise cost distribution
- Position-based cost analysis
- Payroll trends over time
- Salary distribution statistics

##### Revenue Integration
- Payroll as percentage of revenue
- Cost analysis breakdowns
- Export capabilities

#### Analytics Data
- **Cost Breakdown**: Salaries, allowances, overtime, bonuses
- **Department Costs**: Labor costs by department
- **Position Costs**: Compensation by role
- **Distribution Stats**: Min/max/median salaries

## Payroll API

### Controller: PayrollApiController

Provides API endpoints for payroll operations.

#### Features

##### Payroll Request Submission
- HR2 payroll request creation
- Salary calculation from compensation data
- Request validation and processing

##### Employee Payroll Information
- Retrieve employee salary details
- Multiple salary source fallback
- Position-based salary defaults

##### Request Details Retrieval
- Individual request information
- Employee details integration
- Status and history tracking

#### API Endpoints
- `POST /api/payroll/submit-request`: Submit payroll request
- `GET /api/payroll/request/{id}`: Get request details
- `GET /api/payroll/employee/{employeeId}`: Get employee payroll info

## Workflow Integration

### HR Module Dependencies
- **HR1**: Provides hired users and training performance data
- **HR2**: Supplies succession candidates, competencies, and ESS requests
- **HR3**: Provides attendance data for compensation calculations

### Data Flow
1. **Recruitment**: HR1 hires → HR4 employee creation → Compensation setup
2. **Development**: HR2 training → HR4 rewards → Direct compensation updates
3. **Operations**: HR3 attendance → HR4 overtime calculations → Payroll processing
4. **Succession**: HR2 candidates → HR4 promotions → Position updates

## Compensation Structure

### Direct Compensation Components
- **Base Salary**: Position-based minimum compensation
- **Shift Allowance**: HR3 shift-based additional pay
- **Overtime Pay**: Attendance-based overtime calculations
- **Bonus**: Performance and recognition-based rewards
- **Training Reward**: HR1 training performance bonuses

### Calculation Logic
- Monthly generation for all active employees
- Attendance integration for accurate overtime
- Position hierarchy for salary determination
- Training performance for bonus eligibility

## Analytics Framework

### KPI Categories
- **Workforce Metrics**: Headcount, turnover, tenure
- **Recruitment Metrics**: New hires, vacancy rates
- **Financial Metrics**: Cost per employee, budget utilization
- **Development Metrics**: Training completion, promotion rates

### Reporting Features
- Real-time dashboard updates
- Historical trend analysis
- Department comparisons
- Export capabilities for external reporting

## Security Considerations

- **Role-Based Access**: Strict enforcement of admin_hr4 role
- **Data Validation**: Comprehensive input validation
- **Transaction Safety**: Database transactions for critical operations
- **Audit Trail**: Status changes and approval tracking

## Performance Optimizations

- **Efficient Queries**: Optimized joins and aggregations
- **Caching**: KPI data caching for dashboard performance
- **Pagination**: Large datasets properly paginated
- **AJAX Loading**: Dynamic data loading without page refreshes

## Business Rules

### Employee Status Management
- Active employees only receive compensation
- Status changes affect succession eligibility
- Terminated employees removed from active calculations

### Compensation Rules
- Base salary required for all positions
- Overtime calculated from attendance logs
- Bonuses awarded based on performance metrics
- Training rewards linked to HR1 validations

### Succession Rules
- Only 'Ready Now' candidates can be promoted
- Rank-based position advancement
- Automatic status updates to Fellowship
- Promotion history maintained for analytics

### Payroll Rules
- Monthly processing for all active employees
- Deductions calculated as percentages
- Net pay calculations with standard deductions
- Budget monitoring and alerts

## Integration Points

### External Systems
- **HR1**: Training performance and new hire data
- **HR2**: Succession candidates and ESS requests
- **HR3**: Attendance logs for compensation
- **Financial Systems**: Budget and revenue data

### API Integration
- RESTful endpoints for payroll operations
- JSON responses for analytics data
- Secure authentication for all API calls
- Rate limiting and error handling

## Future Enhancements

### Planned Features
- Advanced predictive analytics
- Automated compensation recommendations
- Enhanced succession planning algorithms
- Integration with external payroll systems
- Mobile app support for ESS functions

### Scalability Considerations
- Database optimization for large employee bases
- Caching strategies for performance
- Modular architecture for feature expansion
- API versioning for backward compatibility
