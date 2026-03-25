<?php
        // routes/modules/admin.php

        use Illuminate\Support\Facades\Route;

        use App\Http\Controllers\admin\Hr\hr1\ApplicantManagementController;
        use App\Http\Controllers\admin\Hr\hr1\NewHireController;
        use App\Http\Controllers\admin\Hr\hr1\AdminTrainingPerformanceController;
        use App\Http\Controllers\admin\Hr\hr1\AdminRecruitmentController;
        use App\Http\Controllers\admin\Hr\hr1\AdminHr1DashboardController;
        use App\Http\Controllers\admin\Hr\hr1\AdminSocialRecognitionController;
        use App\Http\Controllers\admin\Hr\hr1\AdminAssessmentPerformanceController;

        use App\Http\Controllers\admin\Hr\hr2\AdminLearningEnrollController;
        use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
        use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
        use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
        use App\Http\Controllers\admin\Hr\hr2\AdminEssController;
        use App\Http\Controllers\admin\Hr\hr2\AdminLearningMaterialsController;
        use App\Http\Controllers\admin\Hr\hr2\AdminCompetencyVerificationController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingEvaluationController;
        use App\Http\Controllers\admin\Hr\hr2\AdminOnboardingAssessmentController;


        // use App\Http\Controllers\admin\Hr\hr3\AdminTimesheetController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftController;
        use App\Http\Controllers\admin\Hr\hr3\AdminInterviewScheduleController;
        use App\Http\Controllers\admin\Hr\hr3\AdminTrainingScheduleController;
        use App\Http\Controllers\admin\Hr\hr3\AdminLeaveManagementController;
        use App\Http\Controllers\admin\Hr\hr3\AdminClaimsController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftRequestController;
        
        use App\Http\Controllers\admin\Hr\hr4\AdminCoreHumanCapitalController;
        use App\Http\Controllers\admin\Hr\hr4\AdminDirectCompensationController;
        use App\Http\Controllers\admin\Hr\hr4\EssRequestController;
        use App\Http\Controllers\admin\Hr\hr4\HRAnalyticsController;
        use App\Http\Controllers\admin\Hr\hr4\PayrollAnalyticsController;
        use App\Http\Controllers\PayrollController;
        use App\Http\Controllers\PayrollReportController;


        // --- Admin Dashboard ---
        // --- Modular Admin Dashboards ---

        // HR Modular
        Route::get('/hr1/dashboard', [AdminHr1DashboardController::class, 'index'])->name('admin.hr1.dashboard');
        Route::get('/hr2/dashboard', function () { return view('admin.hr2.dashboard'); })->name('admin.hr2.dashboard');
        Route::get('/hr3/dashboard', function () { return view('admin.hr3.dashboard'); })->name('admin.hr3.dashboard');
        Route::get('/hr4/dashboard', function () { return view('admin.hr4.dashboard'); })->name('admin.hr4.dashboard');

        // Logistics Modular
        Route::get('/logistics1/dashboard', function () { return view('admin._logistics1.dashboard'); })->name('admin.logistics1.dashboard');
        Route::get('/logistics2/dashboard', function () { return view('admin._logistics2.dashboard'); })->name('admin.logistics2.dashboard');

        // Core Modular
        Route::get('/core1/dashboard', function () { return view('admin.core1.index'); })->name('admin.core1.dashboard');
        Route::get('/core2/dashboard', function () { return view('admin.core2.index'); })->name('admin.core2.dashboard');

        // Financials (General)
        Route::get('/financials/dashboard', function () { return view('admin.financials.dashboard'); })->name('admin.financials.dashboard');

      // --- HR1 Department ---
        Route::prefix('hr1')->group(function () {

            // Applicant Management
            Route::get('/applicants', [ApplicantManagementController::class, 'index'])->name('hr1.applicants.index');
            Route::get('/applicants/{id}', [ApplicantManagementController::class, 'show'])->name('hr1.applicants.show');
            Route::get('applicants/{id}/resume', [ApplicantManagementController::class, 'downloadResume'])
                ->name('hr1.applicants.download');
            Route::post('applicants/{id}/status', [ApplicantManagementController::class, 'updateStatus'])->name('hr1.applicants.updateStatus');

            // New Hires Management
            Route::get('/newhires', [NewHireController::class, 'index'])->name('hr1.newhires.index');
            Route::get('/newhires/{id}', [NewHireController::class, 'show'])->name('hr1.newhires.show');
            Route::get('/newhires/{id}/resume', [NewHireController::class, 'downloadResume'])->name('hr1.newhires.download');
            Route::post('/newhires/{id}/status', [NewHireController::class, 'updateStatus'])->name('hr1.newhires.updateStatus');
            Route::post('/newhires/{applicant_id}/validate-assessment', [NewHireController::class, 'validateAssessment'])->name('hr1.newhires.validateAssessment');
            Route::post('/newhires/sync-hr4', [NewHireController::class, 'syncToHr4'])->name('hr1.newhires.syncHr4');


           Route::get('/training-performance', [AdminTrainingPerformanceController::class, 'index'])
                 ->name('hr1.training.performance.index');

            Route::get('/training-performance/{employee_id}', [AdminTrainingPerformanceController::class, 'show'])
                ->name('hr1.training.performance.show');
            
            // Fixed: Removed leading /admin/ as the prefix handles it
            Route::post('/training-performance/{employee_id}/validate', [AdminTrainingPerformanceController::class, 'validateAndStore'])
                ->name('hr1.training.performance.validate');

            // Recruitment (Job Postings)
            Route::get('/recruitment', [AdminRecruitmentController::class, 'index'])->name('hr1.recruitment.index');
            Route::get('/recruitment/{id}', [AdminRecruitmentController::class, 'show'])->name('hr1.recruitment.show');
            Route::post('/recruitment/{id}/toggle', [AdminRecruitmentController::class, 'toggle'])->name('hr1.recruitment.toggle');
            Route::post('/recruitment/publish-hr4/{id}', [AdminRecruitmentController::class, 'publishFromHr4'])->name('hr1.recruitment.publishHr4');

            // Social Recognition (CRUD)
            Route::resource('recognition', AdminSocialRecognitionController::class)->names([
                'index' => 'admin.hr1.recognition.index',
                'create' => 'admin.hr1.recognition.create',
                'store' => 'admin.hr1.recognition.store',
                'edit' => 'admin.hr1.recognition.edit',
                'update' => 'admin.hr1.recognition.update',
                'destroy' => 'admin.hr1.recognition.destroy',
            ]);
            Route::post('recognition/{id}/sync-hr4', [AdminSocialRecognitionController::class, 'syncToHr4'])->name('admin.hr1.recognition.syncHr4');
              
            Route::get('/onboarding-assessment', [AdminOnboardingAssessmentController::class, 'index'])
                ->name('onboarding.assessment.public');

            Route::post('/onboarding-assessment/check', [AdminOnboardingAssessmentController::class, 'checkReference'])
                ->name('onboarding.assessment.check');

            Route::get('/onboarding-assessment/matrix/{id}', [AdminOnboardingAssessmentController::class, 'matrix'])
                ->name('onboarding.assessment.matrix');

            Route::post('/onboarding-assessment/matrix/{id}/submit', [AdminOnboardingAssessmentController::class, 'submitAssessment'])
                ->name('onboarding.assessment.submit');
            
            // Performance Management Group
            Route::prefix('performance')->group(function () {
                
                // Training Performance (Existing)
                Route::get('/training', [AdminTrainingPerformanceController::class, 'index'])->name('hr1.training.performance.index');
                Route::get('/training/{employee_id}', [AdminTrainingPerformanceController::class, 'show'])->name('hr1.training.performance.show');
                Route::post('/training/{employee_id}/validate', [AdminTrainingPerformanceController::class, 'validateAndStore'])->name('hr1.training.performance.validate');

                // Assessment Performance (New)
                Route::get('/assessment', [AdminAssessmentPerformanceController::class, 'index'])->name('hr1.assessment.performance.index');
                Route::get('/assessment/{id}', [AdminAssessmentPerformanceController::class, 'show'])->name('hr1.assessment.performance.show');
                Route::post('/assessment/{id}/validate', [AdminAssessmentPerformanceController::class, 'validateAssessment'])->name('hr1.assessment.performance.validate');
            });
        });

        // --- HR2 Department ---
     // routes/modules/admin.php

use App\Http\Controllers\admin\Hr\hr2\AdminDashboardController;

// HR2 Dashboard
    Route::prefix('hr2')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.hr2.dashboard');

        // --- Learning Enrollment ---
        Route::controller(AdminLearningEnrollController::class)->group(function () {
            Route::get('learning/enroll', 'index')->name('hr2.learning.enroll');
            Route::get('learning/enroll/{id}', 'showEnrollment')->name('hr2.learning.show_enroll');
            Route::post('learning/assign-modules', 'assignModules')->name('hr2.learning.assign');
        });

        // --- Succession Management ---
        Route::controller(AdminSuccessionController::class)->group(function () {
            Route::get('/departments/{dept_code}/specializations', 'getSpecializations')->name('departments.specializations');
            Route::get('/departments/{dept_code}/positions', 'getPositions')->name('departments.positions');
            Route::get('/departments/{dept_id}/employees', 'getEmployeesByDeptAndSpec');
        });

        // --- Training & Evaluation ---
        Route::get('/training', [AdminTrainingController::class, 'index'])->name('hr2.training');
        Route::get('/eligible-employees', [AdminTrainingController::class, 'getEligibleEmployees'])->name('hr2.training.employees');
        Route::get('/validated-employees', [AdminTrainingController::class, 'getValidatedEmployees']);
        Route::get('/training/evaluate', [AdminTrainingEvaluationController::class, 'showEvaluation'])->name('hr2.training.evaluate');
        Route::post('/training/evaluate/store', [AdminTrainingEvaluationController::class, 'storeEvaluation'])->name('hr2.training_evaluation.store');
        Route::get('/get-specializations/{dept}', [AdminTrainingEvaluationController::class, 'getSpecializations']);
        Route::get('/get-competencies/{dept}/{spec}', [AdminTrainingEvaluationController::class, 'getCompetencies']);

        // --- Learning Modules ---
        Route::controller(AdminLearningController::class)->group(function () {
            Route::get('/departments/{dept_code}/{spec}/competencies', 'getCompetencies')->name('departments.competencies');
            Route::get('/generate-module-code/{dept}/{spec}', 'generateModuleCode')->name('hr2.generateModuleCode');
        });

        // --- Learning Materials ---
        Route::controller(AdminLearningMaterialsController::class)->group(function () {
            Route::get('/learning-materials', 'selector')->name('learning.materials.selector');
            Route::get('/materials/{moduleCode}/list', 'listMaterials')->name('learning.materials.list');
            Route::post('/{moduleCode}/materials', 'store')->name('learning.materials.store');
            Route::delete('/materials/{id}', 'destroy')->name('learning.materials.destroy');
            Route::get('/modules/{dept}/{spec}', 'getModulesByDeptSpec');
        });

        // --- Competency Verification ---
        Route::prefix('competency-verification')->group(function () {
            Route::get('/', [AdminCompetencyVerificationController::class, 'index'])
                ->name('admin.hr2.competency.verification.index');
            Route::post('/{id}/verify', [AdminCompetencyVerificationController::class, 'verify'])
                ->name('admin.hr2.competency.verify');
        });

        // --- ESS Requests ---
        Route::prefix('ess')->group(function () {
            Route::get('/', [AdminEssController::class, 'index'])->name('ess.index');
            Route::post('/{id}/status', [AdminEssController::class, 'updateStatus'])->name('ess.updateStatus');
        });

        // --- Competencies & Succession Resource Routes ---
        Route::resource('competencies', CompetencyController::class);
        Route::resource('learning', AdminLearningController::class);

        Route::prefix('succession')->group(function () {
            Route::get('/', [AdminSuccessionController::class, 'index'])->name('succession.index');
            Route::post('/candidate', [AdminSuccessionController::class, 'storeCandidate'])->name('succession.candidate.store');
            Route::delete('/candidate/{id}', [AdminSuccessionController::class, 'destroyCandidate'])->name('succession.candidate.destroy');
            Route::post('/candidate/{id}/promote', [AdminSuccessionController::class, 'promoteCandidate'])->name('succession.candidate.promote');
        });

    });
        // --- END OF HR2 Department ---

        // --- HR3 Department ---
        Route::prefix('hr3')->group(function () {
            // Route::get('/timesheet', [AdminTimesheetController::class, 'index'])->name('timesheet.index');
            // Route::get('/timesheet/{employeeId}', [AdminTimesheetController::class, 'show'])->name('timesheet.show');

            Route::get('/shifts', [AdminShiftController::class, 'index'])->name('shifts.index');
            Route::post('/shifts', [AdminShiftController::class, 'store'])->name('shifts.store');
            Route::delete('/shifts/{id}', [AdminShiftController::class, 'destroy'])->name('shifts.destroy');
            Route::get('/get-employees/{dept_id}', [AdminShiftController::class, 'getEmployeesByDept']);
           
            Route::get('/schedule',
                [AdminInterviewScheduleController::class,'index'])
                ->name('schedule.index');

            Route::post('/schedule',
                [AdminInterviewScheduleController::class,'store'])
                ->name('schedule.store');

            Route::get('/get-specializations/{dept}',
                [AdminInterviewScheduleController::class,'getSpecializations']);
            Route::get('/get-interview-applicants/{dept}', [AdminInterviewScheduleController::class, 'getInterviewApplicants']);

            Route::get('/training-schedule', [AdminTrainingScheduleController::class, 'index'])->name('training_schedule.index');
            Route::post('/training-schedule', [AdminTrainingScheduleController::class, 'store'])->name('training_schedule.store');
            Route::get('/get-verified-competencies/{emp_id}', [AdminTrainingScheduleController::class, 'getCompetencies']);

            // Designated Leave Management Routes
            Route::prefix('leave')->group(function () {
                Route::get('/', [AdminLeaveManagementController::class, 'index'])
                    ->name('admin.hr3.leave.index');
                    
                Route::post('/{id}/update', [AdminLeaveManagementController::class, 'updateStatus'])
                    ->name('admin.hr3.leave.update');
            });
           
            // Claims & Reimbursement
            Route::get('/claims', [AdminClaimsController::class, 'index'])->name('admin.hr3.claims.index');
            Route::post('/claims/{claim_id}/approve', [AdminClaimsController::class, 'approve'])->name('admin.hr3.claims.approve');
            Route::post('/claims/{claim_id}/reject', [AdminClaimsController::class, 'reject'])->name('admin.hr3.claims.reject');

            Route::get('/shift-requests', [AdminShiftRequestController::class, 'index'])
                ->name('admin.hr3.shift_requests.index');

            Route::post('/shift-requests/{id}/approve', [AdminShiftRequestController::class, 'approve'])
                ->name('admin.hr3.shift_requests.approve');

            Route::post('/shift-requests/{id}/reject', [AdminShiftRequestController::class, 'reject'])
                ->name('admin.hr3.shift_requests.reject');
        });

        // --- END OF HR3 Department ---

        // --- HR4 Department ---

        Route::prefix('hr4')->group(function () {

            // Core Human Capital
            Route::get('/core-human-capital', [AdminCoreHumanCapitalController::class, 'index'])
                ->name('hr4.core');

            Route::post('/core-human-capital/process-hired', [AdminCoreHumanCapitalController::class, 'processHiredUsers'])
                ->name('hr4.core.process_hired');

            // Employee CRUD
            // Edit and Update routes disabled - only status modification allowed
            // Route::get('/employees/{employee}/edit', [AdminCoreHumanCapitalController::class, 'editEmployee'])
            //     ->name('hr4.employees.edit');
            // Route::put('/employees/{employee}', [AdminCoreHumanCapitalController::class, 'updateEmployee'])
            //     ->name('hr4.employees.update');

            Route::delete('/employees/{employee}', [AdminCoreHumanCapitalController::class, 'deleteEmployee'])
                ->name('hr4.employees.delete');

            Route::patch('/employees/{employee}/status', [AdminCoreHumanCapitalController::class, 'updateEmployeeStatus'])
                ->name('hr4.employees.update_status');

            // Direct Compensation
            Route::get('/direct-compensation', [AdminDirectCompensationController::class, 'index'])
                ->name('hr4.direct_compensation.index');

            Route::post('/direct-compensation/generate', [AdminDirectCompensationController::class, 'generate'])
                ->name('hr4.direct_compensation.generate');

            // Job Postings
            Route::get('/job-postings', [AdminDirectCompensationController::class, 'jobPostingsIndex'])
                ->name('hr4.job_postings.index');

            Route::get('/job-postings/create', [AdminDirectCompensationController::class, 'createJobPosting'])
                ->name('hr4.job_postings.create');

            Route::get('/job-postings/{positionId}/specializations', [AdminDirectCompensationController::class, 'getSpecializationsByPosition'])
                ->name('hr4.job_postings.specializations');

            Route::get('/job-postings/{positionId}/details', [AdminDirectCompensationController::class, 'getPositionDetails'])
                ->name('hr4.job_postings.details');

            Route::get('/job-postings/competencies', [AdminDirectCompensationController::class, 'getCompetenciesBySpecializationAndPosition'])
                ->name('hr4.job_postings.competencies');

            Route::post('/job-postings', [AdminDirectCompensationController::class, 'storeJobPosting'])
                ->name('hr4.job_postings.store');

            Route::get('/job-postings/{jobPosting}', [AdminDirectCompensationController::class, 'showJobPosting'])
                ->name('hr4.job_postings.show');

            Route::get('/job-postings/{jobPosting}/edit', [AdminDirectCompensationController::class, 'editJobPosting'])
                ->name('hr4.job_postings.edit');

            Route::put('/job-postings/{jobPosting}', [AdminDirectCompensationController::class, 'updateJobPosting'])
                ->name('hr4.job_postings.update');

            Route::delete('/job-postings/{jobPosting}', [AdminDirectCompensationController::class, 'archiveJobPosting'])
                ->name('hr4.job_postings.destroy');

            // Training Rewards Management
            Route::get('/training-rewards', [AdminDirectCompensationController::class, 'trainingRewardsIndex'])
                ->name('hr4.training_rewards.index');

            Route::get('/training-rewards/{employee}', [AdminDirectCompensationController::class, 'showEmployeeTrainingRewards'])
                ->name('hr4.training_rewards.show');

            // Payroll Management
            Route::resource('payroll', PayrollController::class, ['as' => 'hr4']);
            Route::post('/payroll/request-budget-allocation', [PayrollController::class, 'requestBudgetAllocation'])->name('hr4.payroll.request_budget_allocation');
            Route::get('/payroll/reports', [PayrollController::class, 'reports'])->name('hr4.payroll.reports');
            Route::get('/payroll/get-attendance/{employeeId}', [PayrollController::class, 'getAttendance'])->name('hr4.payroll.getAttendance');
            Route::get('/payroll/get-salary/{employeeId}', [PayrollController::class, 'getSalary'])->name('hr4.payroll.getSalary');
            Route::get('/payroll/get-employee-position/{employeeId}', [PayrollController::class, 'getEmployeePosition'])->name('hr4.payroll.getEmployeePosition');
            Route::get('/payroll/get-position-salary/{positionId}', [PayrollController::class, 'getPositionSalary'])->name('hr4.payroll.getPositionSalary');

            // Payroll Reports
            Route::get('/payroll-reports', [PayrollReportController::class, 'index'])->name('hr4.payroll_reports.index');
            Route::get('/payroll-reports/detailed', [PayrollReportController::class, 'detailed'])->name('hr4.payroll_reports.detailed');
            Route::get('/payroll-reports/export', [PayrollReportController::class, 'export'])->name('hr4.payroll_reports.export');
            Route::get('/payroll-reports/employee/{employeeId}', [PayrollReportController::class, 'employeeHistory'])->name('hr4.payroll_reports.employee');

            // ESS Payroll Requests Management
            Route::get('/ess-requests', [EssRequestController::class, 'index'])->name('hr4.ess_requests.index');
            Route::get('/ess-requests/{id}', [EssRequestController::class, 'show'])->name('hr4.ess_requests.show');
            Route::post('/ess-requests/{id}/approve', [EssRequestController::class, 'approve'])->name('hr4.ess_requests.approve');
            Route::post('/ess-requests/{id}/reject', [EssRequestController::class, 'reject'])->name('hr4.ess_requests.reject');
            Route::post('/ess-requests/sync', [EssRequestController::class, 'syncFromHr2'])->name('hr4.ess_requests.sync');

            // HR Analytics Module
            Route::prefix('/analytics')->group(function () {
                // Analytics Landing
                Route::get('/', function () { return view('admin.hr4.analytics.index'); })->name('hr4.analytics.index');

                // KPI Dashboard
                Route::get('/kpi', [HRAnalyticsController::class, 'dashboard'])->name('hr4.analytics.kpi');
                Route::get('/kpi/data', [HRAnalyticsController::class, 'getKPIDataJson'])->name('hr4.analytics.kpi.data');
                Route::get('/kpi/department-health', [HRAnalyticsController::class, 'getDepartmentHealthScores'])->name('hr4.analytics.kpi.health');

                // Payroll Analytics
                Route::get('/payroll', [PayrollAnalyticsController::class, 'dashboard'])->name('hr4.analytics.payroll');
                Route::get('/payroll/data', [PayrollAnalyticsController::class, 'getSummaryJson'])->name('hr4.analytics.payroll.data');
                Route::get('/payroll/export', [PayrollAnalyticsController::class, 'exportReport'])->name('hr4.analytics.payroll.export');
                Route::get('/payroll/revenue', [PayrollAnalyticsController::class, 'getRevenueComparison'])->name('hr4.analytics.payroll.revenue');
            });

        });