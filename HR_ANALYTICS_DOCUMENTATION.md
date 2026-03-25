# HR Analytics Module Documentation

## 📊 Module Overview

The **HR Analytics Module** is a comprehensive intelligence and reporting system that provides actionable insights into workforce metrics and labor costs. It consists of two main dashboards:

1. **HR KPI Dashboard** - Employee and organizational metrics
2. **Payroll & Labor Cost Analytics** - Financial cost analysis

---

## 🎯 Module Features

### **HR KPI Dashboard**
Displays key performance indicators for human resource management:

#### Core Metrics:
| Metric | Description | Value |
|--------|-------------|-------|
| **Total Headcount** | Total active employees | e.g., 150 |
| **New Hires (MTD)** | Month-to-date new hires | e.g., 5 |
| **New Hires (YTD)** | Year-to-date new hires | e.g., 28 |
| **Vacant Positions** | Unfilled job openings | e.g., 12 |
| **Vacancy Rate** | % of positions unfilled | e.g., 8% |
| **Turnover Rate** | % of employees leaving monthly | e.g., 2.1% |
| **Employees Left (MTD)** | Departures this month | e.g., 3 |
| **Average Tenure** | Average years employed | e.g., 3.5 years |
| **Promotions (MTD)** | Promotions this month | e.g., 2 |

#### Visual Components:
- 📊 **KPI Cards** - Quick metric overview with color-coded status
- 📈 **Headcount Trends** - 12-month line chart showing employee growth
- 🥧 **Department Distribution** - Pie chart of employees by department
- 🏥 **Department Health Scores** - Department-level performance indicators
- 👥 **New Hires List** - Recent hires with join dates and days employed
- ⚠️ **Attrition Analysis** - Breakdown of employee departures by reason

---

### **Payroll & Labor Cost Analytics**
Analyzes compensation spending and labor cost metrics:

#### Core Metrics:
| Metric | Description | Example |
|--------|-------------|---------|
| **Monthly Payroll Total** | Sum of all salaries (gross) | ₱2,500,000 |
| **Monthly Deductions** | Total deductions (SSS, tax, etc) | ₱350,000 |
| **Monthly Net Pay** | Actual cash paid out | ₱2,150,000 |
| **YTD Payroll Total** | Year-to-date total | ₱22,500,000 |
| **Cost Per Employee** | Average salary per headcount | ₱16,667 |
| **Active Employees** | Number of active payroll entries | 150 |
| **Budget Status** | Actual vs budgeted spending | 103% (over/on-track) |
| **Compensation Total** | Comprehensive compensation sum | ₱2,550,000 |

#### Visual Components:
- 💳 **Summary Cards** - Current month financial overview
- 📈 **Payroll Trends** - 12-month payroll spending history
- 🥧 **Cost Breakdown** - Pie chart: Salaries, Allowances, OT, Bonus, Training
- 🏢 **Cost by Department** - Table: Headcount & total cost per department
- 💼 **Cost by Position** - Table: Salary & cost by job position
- 📊 **Deduction Breakdown** - Detailed deduction types (SSS, PhilHealth, etc)
- 📈 **Salary Distribution** - Min/Avg/Max salary statistics

---

## 🔌 Routes & Endpoints

### Web Routes

```
/admin/hr4/analytics                    → Analytics Landing Page
/admin/hr4/analytics/kpi                → KPI Dashboard
/admin/hr4/analytics/payroll            → Payroll Analytics Dashboard
```

### API Endpoints

```
GET  /admin/hr4/analytics/kpi/data              → KPI data as JSON
GET  /admin/hr4/analytics/kpi/department-health → Department scores as JSON
GET  /admin/hr4/analytics/payroll/data          → Payroll summary as JSON
GET  /admin/hr4/analytics/payroll/export        → Export payroll report as CSV
GET  /admin/hr4/analytics/payroll/revenue       → Revenue comparison data
```

---

## 📁 File Structure

```
app/Http/Controllers/admin/Hr/hr4/
├── HRAnalyticsController.php           (KPI logic & calculations)
└── PayrollAnalyticsController.php      (Payroll logic & calculations)

resources/views/admin/hr4/analytics/
├── index.blade.php                     (Analytics landing page)
├── kpi_dashboard.blade.php             (KPI dashboard view)
└── payroll_analytics.blade.php         (Payroll analytics view)
```

---

## 🎨 Dashboard Layouts

### KPI Dashboard Layout
```
┌─────────────────── HR KPI Dashboard ────────────────┐
│                                                      │
│  ┌──────┬──────┬──────┬──────┬──────┬──────┐        │
│  │Total │ New  │Vacant│Turn- │ Avg  │Promo-│        │
│  │Head  │Hires │Pos.  │over  │Tenure│tions │        │
│  └──────┴──────┴──────┴──────┴──────┴──────┘        │
│                                                      │
│  ┌──────────────────┬──────────────────┐            │
│  │Headcount Trends  │ Dept. Distribution           │
│  │ (Line Chart)     │ (Pie Chart)      │            │
│  └──────────────────┴──────────────────┘            │
│                                                      │
│  ┌─── Department Health Scores ────────┐            │
│  │ Sales        95% ✓ Healthy         │            │
│  │ IT           89% ✓ Healthy         │            │
│  │ Operations   72%   Fair            │            │
│  └────────────────────────────────────┘            │
│                                                      │
│  ┌─── New Hires (Last 30 Days) ───┐                │
│  │ Juan Dela Cruz | Sales Mgr | 45 days            │
│  │ Maria Santos   | IT Dev | 30 days              │
│  └────────────────────────────────┘                │
│                                                      │
└──────────────────────────────────────────────────────┘
```

### Payroll Analytics Layout
```
┌──── Payroll & Labor Cost Analytics ────┐
│                                         │
│  ┌────┬────┬────┬────┬────┐           │
│  │Payroll│Ded │NetPay│Cost│Budget│   │
│  │₱2.5M │₱350k│₱2.15M│₱16.6k│103%│  │
│  └────┴────┴────┴────┴────┘           │
│                                         │
│  ┌──────────────┬──────────────┐       │
│  │Payroll Trends│ Cost Breakdown       │
│  │(Line Chart)  │ (Pie Chart)  │       │
│  └──────────────┴──────────────┘       │
│                                         │
│  ┌─ Cost by Department ──────────┐     │
│  │ Sales        ₱800k   45 emps  │     │
│  │ IT           ₱950k   32 emps  │     │
│  │ Operations   ₱700k   35 emps  │     │
│  └───────────────────────────────┘     │
│                                         │
│  ┌─ Deduction Analysis ──────────┐     │
│  │ SSS: ₱122.5k (35%)            │     │
│  │ Tax: ₱70k (20%)               │     │
│  │ PhilHealth: ₱87.5k (25%)      │     │
│  └───────────────────────────────┘     │
│                                         │
└─────────────────────────────────────────┘
```

---

## 🔧 Controllers

### HRAnalyticsController

**Key Methods:**

```php
public function dashboard(Request $request)
// Main KPI dashboard view

private function getKPIData()
// Returns: totalHeadcount, newHiresMTD, vacantPositions, turnoverRate, etc.

private function getHeadcountTrends()
// Returns: 12-month employee count trend

private function getDepartmentBreakdown()
// Returns: List of departments with employee count and %

private function getNewHiresData()
// Returns: Employees hired in last 30 days

private function getAttritionData()
// Returns: Breakdown of departures by reason

public function getDepartmentHealthScores()
// Returns: Health score (0-100) for each department

public function getKPIDataJson()
// API endpoint returning KPI data as JSON
```

---

### PayrollAnalyticsController

**Key Methods:**

```php
public function dashboard(Request $request)
// Main payroll analytics dashboard view

private function getPayrollSummary()
// Returns: monthlyTotal, deductions, netPay, costPerEmployee, budget%, etc.

private function getCostByDepartment()
// Returns: Headcount, total salary, avg salary per department

private function getCostByPosition()
// Returns: Count and salary breakdown by position

private function getPayrollTrends()
// Returns: 12-month payroll spending trend

private function getDeductionBreakdown()
// Returns: Breakdown of SSS, tax, PhilHealth, PAG-IBIG, etc.

private function getCostAnalysis()
// Returns: Cost breakdown - Salaries, Allowances, OT, Bonus, Training

private function getSalaryDistribution()
// Returns: Min, Max, Avg, Median salary statistics

public function getSummaryJson()
// API endpoint returning payroll summary as JSON

public function exportReport(Request $request)
// Export CSV report with summary & cost breakdown
```

---

## 📊 Data Sources

### KPI Dashboard pulls from:
- `employees` table - Headcount, status, hire date, departments
- `needed_positions` table - Vacant positions
- All employee status tracking

### Payroll Analytics pulls from:
- `payrolls` table - Salary, deductions, net pay
- `direct_compensations_hr4` table - Allowances, overtime, bonus, training reward
- `employees` table - Department & position associations
- `department_position_titles_hr2` table - Base salary by position

---

## 🎯 Use Cases

### For HR Managers:
- ✅ Track hiring velocity and forecast staffing needs
- ✅ Monitor turnover rates and identify flight risks
- ✅ Evaluate department health and performance
- ✅ Plan promotions and succession

### For Finance/CFO:
- ✅ Monitor payroll spending vs budget
- ✅ Analyze cost per department and position
- ✅ Track deduction ratios and compliance
- ✅ Benchmark salary distributions

### For Department Heads:
- ✅ See their department's current headcount
- ✅ Understand labor cost allocation
- ✅ Monitor team composition

### For Strategy/Executive:
- ✅ Big picture workforce trends
- ✅ Labor cost as % of revenue
- ✅ Organizational health metrics

---

## 🚀 How to Access

### 1. Through Admin Portal:
```
Dashboard → HR4 → Analytics
```

### 2. Direct URLs:
```
http://localhost:8000/admin/hr4/analytics              (Landing page)
http://localhost:8000/admin/hr4/analytics/kpi          (KPI Dashboard)
http://localhost:8000/admin/hr4/analytics/payroll      (Payroll Analytics)
```

### 3. API Access:
```bash
# Get KPI data as JSON
curl http://localhost:8000/admin/hr4/analytics/kpi/data

# Get Payroll summary as JSON
curl http://localhost:8000/admin/hr4/analytics/payroll/data

# Get Department health scores
curl http://localhost:8000/admin/hr4/analytics/kpi/department-health

# Export payroll report
curl http://localhost:8000/admin/hr4/analytics/payroll/export > payroll.csv
```

---

## 📈 Key Insights

### KPI Dashboard Insights:
- **Trending Up?** Headcount growing → Check vacancy rate to plan hiring
- **High Turnover?** Investigate departments with low health scores
- **New Hire Velocity?** Shows hiring pace vs plan
- **Long Tenure?** Indicates stable, experienced workforce

### Payroll Analytics Insights:
- **Budget % > 100%?** Overspending - investigate why
- **Highest Cost Dept?** May need cost optimization
- **Salary Distribution?** Check for internal equity
- **Deduction Trends?** Monitor tax and benefit changes

---

## 🔐 Permissions

Currently, these dashboards are accessible to HR4 admin users. To restrict access:

```php
// In controller constructor, add:
public function __construct()
{
    $this->middleware('auth');
    // Add policy check if needed
}
```

---

## 🌱 Future Enhancements

- [ ] Predictive analytics (forecast hiring needs)
- [ ] Email alerts (budget overages, vacancy alerts)
- [ ] Comparison reports (dept vs dept, period vs period)
- [ ] Custom date range filtering
- [ ] Role-based access control
- [ ] Mobile dashboard view
- [ ] Real-time notifications
- [ ] Benchmarking against industry standards

---

## 📝 Notes

- All calculations are real-time based on current database state
- Charts use Chart.js for interactive visualization
- Responsive design works on desktop, tablet, mobile
- CSV export includes 12-month trends and department breakdown
- Budget baseline is currently set to ₱2,400,000 (can be made configurable)

---

## 🛠️ Technical Stack

- **Backend**: Laravel PHP
- **Frontend**: Blade templating
- **Charts**: Chart.js 3.9.1
- **UI Framework**: Custom CSS with responsive grid
- **Database**: TiDB (MySQL compatible)

---

## 📞 Support

For questions or enhancements:
1. Check the controller methods for calculation logic
2. Review the view files for layout/styling
3. Test endpoints via API for data validation
4. Adjust calculations in controller methods as needed

---

**Version**: 1.0  
**Last Updated**: March 22, 2026  
**Status**: Production Ready ✅
