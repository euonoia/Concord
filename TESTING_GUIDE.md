# Attendance Hours Integration - Verification & Testing Guide

## ✅ Implementation Checklist

### Database Migrations
- ✅ Created migration: `2026_03_20_add_hours_columns_to_attendance_logs_hr3_table.php`
- ✅ Created migration: `2026_03_20_add_hours_columns_to_direct_compensations_hr4_table.php`

### Models Updated
- ✅ `AttendanceLog.php` - Added new fields to fillable array
- ✅ `DirectCompensation.php` - Added fields to fillable and casts

### Controllers Updated
- ✅ `AdminDirectCompensationController.php` - Updated `generate()` method to calculate hours
- ✅ `PayrollController.php` - Updated `getAttendance()` to return hours data

### New Files Created
- ✅ `app/Helpers/AttendanceHelper.php` - Utility class for attendance calculations
- ✅ `ATTENDANCE_INTEGRATION.md` - Complete documentation

---

## 🗄️ Database Schema

### attendance_logs_hr3 Table
```sql
ALTER TABLE attendance_logs_hr3 ADD COLUMN worked_hours DECIMAL(8,2) DEFAULT 0;
ALTER TABLE attendance_logs_hr3 ADD COLUMN overtime_hours DECIMAL(8,2) DEFAULT 0;
ALTER TABLE attendance_logs_hr3 ADD COLUMN night_diff_hours DECIMAL(8,2) DEFAULT 0;
```

### direct_compensations_hr4 Table
```sql
ALTER TABLE direct_compensations_hr4 ADD COLUMN worked_hours DECIMAL(8,2) DEFAULT 0;
ALTER TABLE direct_compensations_hr4 ADD COLUMN overtime_hours DECIMAL(8,2) DEFAULT 0;
ALTER TABLE direct_compensations_hr4 ADD COLUMN night_diff_hours DECIMAL(8,2) DEFAULT 0;
```

---

## 🧪 Testing Instructions

### Pre-Migration Checklist
1. **Backup Database**
   ```bash
   # Backup MySQL database
   mysqldump -u root -p test > backup_before_hours.sql
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Verify Tables**
   ```bash
   php artisan tinker
   
   # Check attendance_logs_hr3
   Schema::getColumnListing('attendance_logs_hr3')
   
   # Check direct_compensations_hr4  
   Schema::getColumnListing('direct_compensations_hr4')
   ```

### Test Attendance Calculation

#### Test 1: Calculate Worked Hours
```php
// In tinker or test
$helper = new \App\Helpers\AttendanceHelper;

$clockIn = \Carbon\Carbon::parse('2026-03-20 08:00:00');
$clockOut = \Carbon\Carbon::parse('2026-03-20 17:00:00');

$worked = $helper::calculateWorkedHours($clockIn, $clockOut);
echo "Worked hours: " . $worked; // Should be 9.00
```

#### Test 2: Calculate Overtime Hours
```php
$helper = new \App\Helpers\AttendanceHelper;

$clockIn = \Carbon\Carbon::parse('2026-03-20 08:00:00');
$clockOut = \Carbon\Carbon::parse('2026-03-20 17:30:00'); // 9.5 hours

$worked = $helper::calculateWorkedHours($clockIn, $clockOut);
$overtime = $helper::calculateOvertimeHours($worked);
echo "Overtime hours: " . $overtime; // Should be 1.50
```

#### Test 3: Calculate Night Differential Hours
```php
$helper = new \App\Helpers\AttendanceHelper;

// Night shift: 10 PM to 6 AM (8 hours, all ND)
$clockIn = \Carbon\Carbon::parse('2026-03-20 22:00:00');
$clockOut = \Carbon\Carbon::parse('2026-03-21 06:00:00');

$ndHours = $helper::calculateNightDiffHours($clockIn, $clockOut);
echo "Night diff hours: " . $ndHours; // Should be 8.00
```

#### Test 4: Mixed Shift (Day + Night)
```php
$helper = new \App\Helpers\AttendanceHelper;

// Mixed: 8 PM to 2 AM (6 hours, 4 ND)
$clockIn = \Carbon\Carbon::parse('2026-03-20 20:00:00');
$clockOut = \Carbon\Carbon::parse('2026-03-21 02:00:00');

$worked = $helper::calculateWorkedHours($clockIn, $clockOut); // 6 hours
$ndHours = $helper::calculateNightDiffHours($clockIn, $clockOut); // 4 hours
```

### Test Monthly Compensation Generation

1. **Ensure test attendance data exists:**
   ```bash
   # Add test attendance logs if needed
   php artisan tinker
   
   $att = new \App\Models\admin\Hr\hr3\AttendanceLog;
   $att->employee_id = 1;
   $att->clock_in = \Carbon\Carbon::parse('2026-03-20 08:00:00');
   $att->clock_out = \Carbon\Carbon::parse('2026-03-20 17:00:00');
   $att->save();
   ```

2. **Generate Monthly Compensation:**
   - Go to HR4 Dashboard
   - Click "Generate Compensation"
   - Select month: 2026-03
   - System should:
     - Calculate all employees' hours
     - Store in `direct_compensations_hr4`

3. **Verify Results:**
   ```bash
   php artisan tinker
   
   $comp = \App\Models\admin\Hr\hr4\DirectCompensation::where([
       'employee_id' => 1,
       'month' => '2026-03'
   ])->first();
   
   echo "Worked: " . $comp->worked_hours;
   echo "Overtime: " . $comp->overtime_hours;
   echo "ND: " . $comp->night_diff_hours;
   ```

### Test Payroll Integration

1. **Get Attendance AJAX:**
   ```bash
   # Test endpoint
   curl "http://localhost:8000/admin/hr4/payroll/get-attendance/1"
   
   # Should return JSON with:
   # - attendances array
   # - total_days
   # - total_hours
   # - worked_hours
   # - overtime_hours
   # - night_diff_hours
   ```

2. **Create Payroll:**
   - Go to Payroll > Add Payroll
   - Select employee
   - Attendance summary should show hours breakdown
   - Save payroll

---

## 📊 Sample Test Data

### Test Employee 1: Regular Day Shift
```blade
Date: 2026-03-20
Clock In: 08:00
Clock Out: 17:00
Expected:
- Worked: 9.00
- Overtime: 1.00
- Night Diff: 0.00
```

### Test Employee 2: Night Shift
```blade
Date: 2026-03-20
Clock In: 22:00
Clock Out: 06:00 (next day)
Expected:
- Worked: 8.00
- Overtime: 0.00
- Night Diff: 8.00
```

### Test Employee 3: Mixed Shift
```blade
Date: 2026-03-20
Clock In: 20:00
Clock Out: 04:00 (next day)
Expected:
- Worked: 8.00
- Overtime: 0.00
- Night Diff: 6.00 (22-04 = 6 hours)
```

---

## 🔍 Query Verification

### Check Migration Success
```sql
DESC attendance_logs_hr3;
DESC direct_compensations_hr4;
```

### Verify Data
```sql
-- Check attendance hours are calculated
SELECT employee_id, clock_in, clock_out, worked_hours, overtime_hours, night_diff_hours
FROM attendance_logs_hr3
WHERE employee_id = 1;

-- Check compensation hours are stored
SELECT employee_id, month, worked_hours, overtime_hours, night_diff_hours
FROM direct_compensations_hr4
WHERE employee_id = 1
ORDER BY month DESC;
```

### Monthly Summary
```sql
SELECT 
    employee_id,
    month,
    worked_hours,
    overtime_hours,
    night_diff_hours,
    base_salary,
    shift_allowance
FROM direct_compensations_hr4
WHERE month = '2026-03'
ORDER BY employee_id;
```

---

## 🚀 Deployment Steps

1. **Backup Production Database**
   ```bash
   mysqldump -u prod_user -p test > prod_backup_2026_03_20.sql
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate --env=production
   ```

3. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

4. **Test in Production**
   - Test attendance calculation
   - Generate monthly compensation
   - Verify payroll reports

5. **Monitor**
   - Check error logs
   - Verify no performance degradation
   - Confirm data accuracy

---

## ⚠️ Troubleshooting

### Issue: "Table doesn't exist" when calculating hours
**Solution:** Ensure migrations have run:
```bash
php artisan migrate
php artisan migrate:status
```

### Issue: Hours are 0 for all employees
**Solution:** Check if attendance logs exist for the month:
```sql
SELECT COUNT(*) FROM attendance_logs_hr3
WHERE MONTH(clock_in) = 3
AND YEAR(clock_in) = 2026;
```

### Issue: Decimal precision issues
**Solution:** AttendanceHelper already handles rounding to 2 decimals, but verify in database:
```sql
SELECT * FROM direct_compensations_hr4 LIMIT 1;
-- All hour columns should show X.XX format
```

### Issue: Night differential calculation seems wrong
**Solution:** Remember ND is 10 PM (22:00) to 6 AM (06:00), not the whole shift:
```php
// Example: 8 PM to 2 AM = 6 hours worked
// Only 22:00-23:59 and 00:00-01:59 = 4 hours ND (not 6)
```

---

## 📝 API Endpoints Affected

### GET `/admin/hr4/payroll/get-attendance/{employeeId}`
**Returns:** Attendance logs + hours summary for current month
**New Fields:**
- `worked_hours` - Total month hours
- `overtime_hours` - Total month overtime
- `night_diff_hours` - Total month ND

### POST `/admin/hr4/direct-compensations/generate`
**Action:** Generates monthly compensation with hours
**New Fields Stored:**
- `worked_hours`
- `overtime_hours`
- `night_diff_hours`

---

## 📚 Helper Class Usage Examples

```php
use App\Helpers\AttendanceHelper;

// Get monthly summary for employee 1, March 2026
$summary = AttendanceHelper::getMonthlyHoursSummary(1, '2026-03');
// Returns: ['worked_hours' => 160.00, 'overtime_hours' => 10.00, 'night_diff_hours' => 24.00, ...]

// Get daily detail
$daily = AttendanceHelper::getDailyAttendanceDetail(1, '2026-03-20');
// Returns: ['date' => '2026-03-20', 'clock_in' => '08:00:00', 'worked_hours' => 9.00, ...]

// Calculate overtime pay
$overtimePay = AttendanceHelper::calculateOvertimePay(10, 250, 1.25);
// 10 hours × 250/hr × 1.25 multiplier = 3125

// Calculate night differential pay
$ndPay = AttendanceHelper::calculateNightDiffPay(24, 250, 0.10);
// 24 hours × 250/hr × 10% = 600
```

---

## ✅ Final Verification

- [ ] Migrations run successfully
- [ ] attendance_logs_hr3 has new columns
- [ ] direct_compensations_hr4 has new columns
- [ ] AttendanceHelper class exists
- [ ] AdminDirectCompensationController uses AttendanceHelper
- [ ] PayrollController returns hours data
- [ ] Test compensation generation works
- [ ] Test payroll creation shows hours
- [ ] Database queries show correct values
- [ ] No error logs
