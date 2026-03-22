# HR4 Payroll Request System - Implementation Summary

## Overview
The payroll request system allows HR2 to submit payroll requests for employees, which are then reviewed and approved by HR4 personnel. When approved, payroll entries are automatically created with salary and net pay information.

## System Components

### 1. **Database Tables**

#### `payroll_request_hr2` (External HR2 Table)
Stores incoming payroll requests from HR2:
- `id` (bigint) - Request ID
- `employee_id` (string) - Employee ID from HR2
- `salary` (decimal) - Gross salary amount
- `net_pay` (decimal) - Net pay amount
- `details` (text) - Request description
- `request_type` (string) - Type: payroll, bonus, deduction
- `status` (string) - Status: pending, approved, rejected
- `created_at` - Request submission time
- `updated_at` - Last update time

#### `payroll_ess_requests_hr4` (HR4 Request Tracking)
Tracks all payroll requests synced from HR2:
- `id` - Request ID
- `employee_id` (bigint) - Employee ID (from employees table)
- `request_type` - Type of request
- `details` - Request details
- `status` - Status: pending, approved, rejected
- `approved_by` - User ID who approved
- `approval_notes` - Approval/rejection notes
- `requested_date` - Date of request
- `approved_date` - Date of approval/rejection
- `created_at`, `updated_at` - Timestamps

#### `payrolls` (Payroll Entries)
Final payroll records created when requests are approved:
- `id` - Payroll entry ID
- `employee_id` (bigint) - Employee database ID
- `salary` - Gross salary (decimal)
- `deductions` - Total deductions (decimal)
- `net_pay` - Net pay after deductions (decimal)
- `pay_date` - Pay date
- `created_at`, `updated_at` - Timestamps

---

## API Endpoints

### 1. **Submit Payroll Request from HR2**
**Endpoint:** `POST /api/payroll/request-from-hr2`

**Request Body:**
```json
{
  "employee_id": "EMP001",
  "salary": 50000.00,
  "net_pay": 45000.00,
  "details": "Monthly payroll for March 2026",
  "request_type": "payroll"
}
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Payroll request submitted successfully",
  "request_id": 1,
  "employee_id": "EMP001",
  "status": "pending"
}
```

**Response (Error - 422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { ... }
}
```

---

### 2. **Get Payroll Request Details**
**Endpoint:** `GET /api/payroll/request/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "employee_id": "EMP001",
    "employee_name": "Juan Dela Cruz",
    "salary": 50000.00,
    "net_pay": 45000.00,
    "details": "Monthly payroll for March 2026",
    "request_type": "payroll",
    "status": "pending",
    "created_at": "2026-03-22T10:30:00Z",
    "updated_at": "2026-03-22T10:30:00Z"
  }
}
```

---

### 3. **Get Employee Payroll Information**
**Endpoint:** `GET /api/payroll/employee/{employeeId}`

**Response:**
```json
{
  "success": true,
  "data": {
    "employee_id": "EMP001",
    "employee_name": "Juan Dela Cruz",
    "department": "Sales",
    "position": "Sales Manager",
    "salary": 50000.00,
    "net_pay": 50000.00,
    "salary_source": "Direct Compensation (HR4)"
  }
}
```

---

## Admin Web Interface

### Request Management Dashboard
**Route:** `GET /admin/hr4/ess-requests`

**Features:**
- View all payroll requests with status filters
- Filter by status (Pending, Approved, Rejected)
- Filter by request type (Payroll, Bonus, Deduction)
- View request details including employee information
- Approve or reject requests
- Sync requests from HR2 system

### Request Detail View
**Route:** `GET /admin/hr4/ess-requests/{id}`

**Displays:**
- Employee information (Name, ID, Department, Position)
- Request details (Type, Date, Description)
- **Payroll Information Section** (NEW):
  - Gross Salary
  - Net Pay
  - Salary source (HR2 Sync or Direct Compensation or Position)
- Approval information (who approved, when, notes)
- Action buttons (Approve/Reject for pending requests)

---

## Workflow

### Step 1: HR2 Submits Payroll Request
```
HR2 System → API POST /api/payroll/request-from-hr2 → payroll_request_hr2 table
```

### Step 2: HR4 Syncs Requests
```
Admin navigates to /admin/hr4/ess-requests
Clicks "Sync from HR2"
→ Requests from payroll_request_hr2 are synced to payroll_ess_requests_hr4
```

### Step 3: HR4 Reviews and Approves
```
Admin views request details at /admin/hr4/ess-requests/{id}
Sees employee info and payroll details (salary/net_pay)
Clicks "Approve Request"
→ Request status updated to "approved"
→ Payroll entry automatically created in payrolls table
→ payroll_request_hr2 table status updated to "approved"
```

### Step 4: Payroll Entry Created
```
When request is approved, system creates payroll entry:
- employee_id (mapped from employee)
- salary (from HR2 request, DirectCompensation, or position)
- net_pay (from HR2 request or calculated)
- deductions (calculated: salary - net_pay)
- pay_date (current date)
```

---

## Salary Resolution Logic

When processing a payroll request, the system retrieves salary from multiple sources in this order:

1. **HR2 Sync Data** (Primary):
   - Uses salary/net_pay provided in the HR2 request

2. **Direct Compensation (HR4)** (Secondary):
   - Latest monthly compensation record
   - Calculated as: Base Salary + Shift Allowance + Overtime Pay + Bonus + Training Reward

3. **Position Base Salary** (Tertiary/Fallback):
   - Default salary from employee's position

If net_pay is not provided, it defaults to the gross salary.

---

## Key Features

✅ **Bi-directional Sync**: Requests from HR2 are synced to HR4  
✅ **Salary Transparency**: Shows salary/net pay in request details  
✅ **Multiple Salary Sources**: Handles different compensation scenarios  
✅ **Automatic Payroll Creation**: Approved requests automatically create payroll entries  
✅ **Audit Trail**: Tracks all approvals, rejections, and notes  
✅ **Status Tracking**: Clear request lifecycle from pending to approved/rejected  

---

## Usage Examples

### Example 1: HR2 Submits Payroll Request via API
```bash
curl -X POST "http://localhost:8000/api/payroll/request-from-hr2" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": "EMP001",
    "salary": 50000.00,
    "net_pay": 45000.00,
    "details": "March 2026 Payroll",
    "request_type": "payroll"
  }'
```

### Example 2: Check Employee Payroll Info
```bash
curl -X GET "http://localhost:8000/api/payroll/employee/EMP001"
```

### Example 3: Admin Approves Request
1. Go to `/admin/hr4/ess-requests`
2. Click "View" on the request
3. Click "Approve Request"
4. Add approval notes (optional)
5. Payroll entry is automatically created

---

## Database Schema Migration

To create the payroll_request_hr2 table, run:
```bash
php artisan migrate
```

This will execute the migration: `2026_03_22_000000_create_payroll_request_hr2_table.php`

---

## Notes

- The `payroll_request_hr2` table is the interface between HR2 and HR4 systems
- All requests must have a valid employee_id that exists in the employees table
- Salary data is preserved from HR2 for transparency and audit purposes
- Approved requests cannot be unapproved; rejections are logged with reason
- The system handles decimal salaries with 2 decimal place precision

---

## Error Handling

- **404**: Employee not found
- **422**: Validation error (missing or invalid required fields)
- **500**: Database or system error
- All errors return JSON response with error message and details

---

## Future Enhancements

- Bulk request submission from HR2
- Request edit/update functionality
- Approval workflow with notifications
- Payroll calculation rules based on request type
- Integration with external HR2 system webhook
