<?php

namespace App\Http\Controllers\admin\Hr\hr2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\admin\Hr\hr2\Competency;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr2\EmployeeTrainingScore;
use App\Models\admin\Hr\hr2\EssRequest;
use App\Models\admin\Hr\hr2\LearningModule;
use App\Models\admin\Hr\hr2\SuccessionPosition;
use App\Models\admin\Hr\hr2\TrainingSessions;
use App\Models\admin\Hr\hr2\SuccessorCandidate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Authorize only HR2 Admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr2') {
            abort(403);
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        // Total employees
        $totalEmployees = Employee::count();

        // Active competencies
        $activeCompetencies = Competency::where('is_active', 1)->count();

        // Total departments
        $totalDepartments = Department::where('is_active', 1)->count();

        // Pending ESS requests
        $pendingEssRequests = EssRequest::where('status', 'pending')->count();

        // Active learning modules
        $activeLearningModules = LearningModule::where('is_active', 1)->count();

        // Succession positions
        $successionPositions = SuccessionPosition::count();

        // Upcoming training sessions (next 30 days)
        $upcomingTrainings = TrainingSessions::where('start_datetime', '>=', now())
            ->where('start_datetime', '<=', now()->addDays(30))
            ->count();

        // Recent training completions (last 30 days)
        $recentCompletions = EmployeeTrainingScore::where('created_at', '>=', now()->subDays(30))->count();

        // Top performing employees
        $topPerformers = EmployeeTrainingScore::select(
                'employee_training_scores_hr2.employee_id',
                'employees.first_name',
                'employees.last_name'
            )
            ->selectRaw('SUM(employee_training_scores_hr2.total_score) as total_score')
            ->selectRaw('AVG(employee_training_scores_hr2.total_score) as avg_score')
            ->selectRaw('COUNT(*) as evaluations_count')
            ->selectRaw('ROUND((SUM(employee_training_scores_hr2.total_score) / (COUNT(*) * 400)) * 100, 2) as weighted_average')
            ->join('employees', 'employees.employee_id', '=', 'employee_training_scores_hr2.employee_id')
            ->groupBy('employee_training_scores_hr2.employee_id', 'employees.first_name', 'employees.last_name')
            ->orderBy('weighted_average', 'desc')
            ->limit(5)
            ->get();

        // ESS Requests by status
        $essRequestsByStatus = DB::table('ess_request_hr2')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Training completion rate
        $totalEvaluations = EmployeeTrainingScore::count();
        $completedTrainings = EmployeeTrainingScore::where('status', 'completed')->count();
        $trainingCompletionRate = $totalEvaluations > 0
            ? round(($completedTrainings / $totalEvaluations) * 100, 2)
            : 0;

        // Succession candidates pipeline
        $successionCandidates = SuccessorCandidate::count();

        // Department-wise metrics
        $departmentMetrics = Department::leftJoin('employees', 'departments_hr2.department_id', '=', 'employees.department_id')
            ->select('departments_hr2.name as department_name', DB::raw('COUNT(employees.employee_id) as emp_count'))
            ->groupBy('departments_hr2.name')
            ->orderBy('emp_count', 'desc')
            ->limit(5)
            ->get();

        // Average performance score
        $avgPerformanceScore = EmployeeTrainingScore::where('status', 'completed')
            ->average('total_score');

        // Performance distribution
        $performanceDistribution = DB::table(
            DB::raw('(SELECT 
                employee_id,
                ROUND((SUM(total_score) / (COUNT(*) * 400)) * 100, 2) as weighted_avg
            FROM employee_training_scores_hr2
            GROUP BY employee_id) as emp_scores')
        )
            ->select(
                DB::raw('CASE 
                    WHEN weighted_avg >= 90 THEN "Excellent"
                    WHEN weighted_avg >= 75 THEN "Good"
                    WHEN weighted_avg >= 60 THEN "Satisfactory"
                    ELSE "Needs Improvement"
                END as grade'),
                DB::raw('COUNT(*) as employee_count')
            )
            ->groupBy(DB::raw('CASE 
                    WHEN weighted_avg >= 90 THEN "Excellent"
                    WHEN weighted_avg >= 75 THEN "Good"
                    WHEN weighted_avg >= 60 THEN "Satisfactory"
                    ELSE "Needs Improvement"
                END'))
            ->get();

        return view('admin.hr2.dashboard', compact(
            'totalEmployees',
            'activeCompetencies',
            'totalDepartments',
            'pendingEssRequests',
            'activeLearningModules',
            'successionPositions',
            'upcomingTrainings',
            'recentCompletions',
            'topPerformers',
            'essRequestsByStatus',
            'trainingCompletionRate',
            'successionCandidates',
            'departmentMetrics',
            'avgPerformanceScore',
            'performanceDistribution'
        ));
    }
}