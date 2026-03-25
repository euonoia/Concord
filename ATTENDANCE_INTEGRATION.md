# HR4 Attendance Hours Integration with Payroll

## Overview
Integrated attendance hours tracking (worked_hours, overtime_hours, night_diff_hours) from HR3 attendance logs into the HR4 payroll system.

---

## Database Changes

### 1. New Migration: Add Hours Columns to attendance_logs_hr3
**File:** `database/migrations/2026_03_20_add_hours_columns_to_attendance_logs_hr3_table.php`

**Columns Added:**
- `worked_hours` (decimal 8,2) - Total hours worked per day
- `overtime_hours` (decimal 8,2) - Hours beyond 8 hours per day
- `night_diff_hours` (decimal 8,2) - Hours worked between 10 PM - 6 AM

### 2. New Migration: Add Hours Columns to direct_compensations_hr4
**File:** `database/migrations/2026_03_20_add_hours_columns_to_direct_compensations_hr4_table.php`

**Columns Added:**
- `worked_hours` - Total hours worked for the month
- `overtime_hours` - Total overtime hours for the month
- `night_diff_hours` - Total night differential hours for the month

---

## Code Updates

### 1. AttendanceLog Model
**File:** `app/Models/admin/Hr/hr3/AttendanceLog.php`

- Added fields to `$fillable` array:
  - worked_hours
  - overtime_hours
  - night_diff_hours

- Added `calculateHours()` method to compute hours based on clock_in/clock_out

### 2. DirectCompensation Model
**File:** `app/Models/admin/Hr/hr4/DirectCompensation.php`

- Updated `$fillable` array to include:
  - worked_hours
  - overtime_hours
  - night_diff_hours

- Updated `$casts` to properly cast new fields as `decimal:2`

### 3. AdminDirectCompensationController
**File:** `app/Http/Controllers/admin/Hr/hr4/AdminDirectCompensationController.php`

#### Updated Methods:

**`generate(Request $request)` (Line 55)**
- Now calculates attendance hours for each employee in the given month
- Calls new `calculateAttendanceHours()` method
- Stores calculated hours in DirectCompensation table

**New Method: `calculateAttendanceHours($employeeId, $month)` (Line 94)**
- Fetches attendance logs for the employee for the given month
- Calculates:
  - `worked_hours`: Total minutes / 60
  - `overtime_hours`: Hours beyond 8 per day
  - `night_diff_hours`: Hours between 10 PM - 6 AM
- Returns array: `[totalWorked, totalOvertime, totalND]`

### 4. PayrollController
**File:** `app/Http/Controllers/PayrollController.php`

#### Updated Method: `getAttendance($employeeId)`
- Now fetches compensation data from DirectCompensation table
- Returns additional fields in JSON response:
  - `worked_hours` - Total month hours
  - `overtime_hours` - Total overtime
  - `night_diff_hours` - Total night differential
- Also includes individual attendance log details

---

## How It Works

### Workflow:

1. **Admin clicks "Generate Compensation" in HR4 Dashboard**
   - System retrieves all employees for the specified month

2. **For each employee:**
   - Fetches all attendance logs from `attendance_logs_hr3`
   - Calculates:
     - Total worked hours
     - Overtime hours (beyond 8 per day)
     - Night differential hours (10 PM - 6 AM)

3. **Stores in DirectCompensation:**
   - Updates `direct_compensations_hr4` with:
     - Base salary
     - Shift allowance
     - Bonus
     - **NEW:** worked_hours, overtime_hours, night_diff_hours

4. **Payroll Integration:**
   - PayrollController can now fetch these hours
   - Payroll form displays hours summary
   - Reports can include hours breakdown

---

## Database Queries

### Fetch Monthly Hours for Employee
```sql
SELECT 
    worked_hours,
    overtime_hours,
    night_diff_hours
FROM direct_compensations_hr4
WHERE employee_id = :employee_id
AND month = :month;
```

### Fetch Daily Attendance with Hours
```sql
SELECT 
    clock_in,
    clock_out,
    worked_hours,
    overtime_hours,
    night_diff_hours
FROM attendance_logs_hr3
WHERE employee_id = :employee_id
AND MONTH(clock_in) = :month
AND YEAR(clock_in) = :year;
```

---

## Hour Calculation Logic

### Worked Hours:
```
Total minutes from clock_in to clock_out / 60 = worked_hours
```

### Overtime Hours:
```
Max(0, worked_hours - 8) = overtime_hours
```

### Night Differential Hours:
```
Count any hour between 22:00 (10 PM) and 05:59 (6 AM) = night_diff_hours
```

---

## Migration Steps

Run these commands to apply changes:

```bash
# Run migrations to add new columns
php artisan migrate

# Validate tables were created correctly
php artisan tinker
```

---

## Usage in Payroll Creation

When creating payroll for an employee, the system now:

1. **Shows Attendance Summary:**
   - Total days worked
   - Total hours worked
   - Overtime hours
   - Night differential hours

2. **Uses Hours Data for:**
   - Validating attendance accuracy
   - Calculating overtime pay (if applicable)
   - Computing night differential allowance
   - Generating payroll reports with detailed breakdown

---

## Views/Routes Affected

### Routes that use hours data:
- `GET /admin/hr4/payroll/get-attendance/{employeeId}` - Returns attendance with hours
- `POST /admin/hr4/direct-compensations/generate` - Generates compensation with hours
- `GET /admin/hr4/payroll/reports` - Payroll reports (can display hours)

### Views that can display hours:
- `resources/views/payroll/create.blade.php` - Attendance summary section
- `resources/views/payroll/reports.blade.php` - Payroll reports
- `resources/views/admin/hr4/compensations.blade.php` - Compensation list

---

## Data Validation

The attendance hours calculation includes:
- ✅ Null/empty clock_in/clock_out handling
- ✅ Night shift spanning midnight
- ✅ Decimal precision (2 decimal places)
- ✅ Max validation to prevent negative values

---

## Future Enhancements

1. **Per-Minute Precision:** Currently calculates per hour; can enhance to per-minute
2. **Multiple Shifts:** Support employees working multiple shifts
3. **Break Time Deduction:** Subtract lunch/break from worked hours
4. **Integration with Payroll Rate:** Multiply hours by hourly rate for pay calculation
5. **Attendance Validation:** Alert if attendance hours don't match clock-in/out records

---

## Support

For questions or issues, check:
- `app/Models/admin/Hr/hr4/DirectCompensation.php` - Model definition
- `app/Http/Controllers/admin/Hr/hr4/AdminDirectCompensationController.php` - Generate logic
- `database/migrations/` - Schema definitions
