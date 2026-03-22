<?php
        // routes/modules/admin.php

        use Illuminate\Support\Facades\Route;

        use App\Http\Controllers\admin\Hr\hr1\ApplicantManagementController;

        use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
        use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
        use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
        use App\Http\Controllers\admin\Hr\hr2\AdminEssController;

        // use App\Http\Controllers\admin\Hr\hr3\AdminTimesheetController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftController;

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
        Route::get('/hr1/dashboard', function () { return view('admin.hr1.dashboard'); })->name('admin.hr1.dashboard');
        Route::get('/hr2/dashboard', function () { return view('admin.hr2.dashboard'); })->name('admin.hr2.dashboard');
        Route::get('/hr3/dashboard', function () { return view('admin.hr3.dashboard'); })->name('admin.hr3.dashboard');
        Route::get('/hr4/dashboard', function () { return view('admin.hr4.dashboard'); })->name('admin.hr4.dashboard');

        // Logistics Modular
        Route::get('/logistics1/dashboard', function () { return view('admin.logistics1.index'); })->name('admin.logistics1.dashboard');
        Route::get('/logistics2/dashboard', function () { return view('admin.logistics2.index'); })->name('admin.logistics2.dashboard');

        // Core Modular
        Route::get('/core1/dashboard', function () { return view('admin.core1.index'); })->name('admin.core1.dashboard');
        Route::get('/core2/dashboard', function () { return view('admin.core2.index'); })->name('admin.core2.dashboard');

        // Financials (General)
        Route::get('/financials/dashboard', function () { return view('admin.financials.index'); })->name('admin.financials.dashboard');

      // --- HR1 Department ---
        Route::prefix('hr1')->group(function () {

            // Applicant Management
            Route::get('/applicants', [ApplicantManagementController::class, 'index'])->name('hr1.applicants.index');
            Route::get('/applicants/{id}', [ApplicantManagementController::class, 'show'])->name('hr1.applicants.show');
            Route::get('applicants/{id}/resume', [ApplicantManagementController::class, 'downloadResume'])
                ->name('hr1.applicants.download');
            Route::post('applicants/{id}/status', [ApplicantManagementController::class, 'updateStatus'])->name('hr1.applicants.updateStatus');

            // New Hires Management
            Route::get('/newhires', [\App\Http\Controllers\admin\Hr\hr1\NewHireController::class, 'index'])->name('hr1.newhires.index');
            Route::get('/newhires/{id}', [\App\Http\Controllers\admin\Hr\hr1\NewHireController::class, 'show'])->name('hr1.newhires.show');
            Route::get('/newhires/{id}/resume', [\App\Http\Controllers\admin\Hr\hr1\NewHireController::class, 'downloadResume'])->name('hr1.newhires.download');
            Route::post('/newhires/{id}/status', [\App\Http\Controllers\admin\Hr\hr1\NewHireController::class, 'updateStatus'])->name('hr1.newhires.updateStatus');

        });


        // --- HR2 Department ---
        Route::prefix('hr2')->group(function () {
            // Specializations by Department
            Route::get('/departments/{dept_code}/specializations', [AdminSuccessionController::class, 'getSpecializations'])
                ->name('departments.specializations');
            // Positions by Department + Specialization
            Route::get('/departments/{dept_code}/positions', [AdminSuccessionController::class, 'getPositions'])
                ->name('departments.positions');
            // Employees by Department and specialization
            Route::get('/departments/{dept_id}/employees', [AdminSuccessionController::class, 'getEmployeesByDeptAndSpec']);

        
        });
        Route::resource('competencies', CompetencyController::class);
        Route::resource('learning', AdminLearningController::class);
        Route::resource('training', AdminTrainingController::class);
        Route::prefix('succession')->group(function () {
            Route::get('/', [AdminSuccessionController::class, 'index'])->name('succession.index');

            // Candidates
            Route::post('/candidate', [AdminSuccessionController::class, 'storeCandidate'])->name('succession.candidate.store');
            Route::delete('/candidate/{id}', [AdminSuccessionController::class, 'destroyCandidate'])->name('succession.candidate.destroy');
            
            //promote candidate
            Route::post('/candidate/{id}/promote', [AdminSuccessionController::class, 'promoteCandidate'])->name('succession.candidate.promote');
        });
        Route::prefix('ess')->group(function () {
            Route::get('/', [AdminEssController::class, 'index'])->name('ess.index');
            Route::post('/{id}/status', [AdminEssController::class, 'updateStatus'])->name('ess.updateStatus');
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
            Route::get('/employees/{employee}/edit', [AdminCoreHumanCapitalController::class, 'editEmployee'])
                ->name('hr4.employees.edit');

            Route::put('/employees/{employee}', [AdminCoreHumanCapitalController::class, 'updateEmployee'])
                ->name('hr4.employees.update');

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