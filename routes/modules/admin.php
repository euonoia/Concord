        <?php
        // routes/modules/admin.php

        use Illuminate\Support\Facades\Route;

        use App\Http\Controllers\admin\Hr\hr1\ApplicantManagementController;
        use App\Http\Controllers\admin\Hr\hr1\NewHireController;
        use App\Http\Controllers\admin\Hr\hr1\AdminTrainingPerformanceController;
        use App\Http\Controllers\admin\Hr\hr1\AdminRecruitmentController;
        use App\Http\Controllers\admin\Hr\hr1\AdminHr1DashboardController;
        use App\Http\Controllers\admin\Hr\hr1\AdminSocialRecognitionController;
        use App\Http\Controllers\admin\Hr\hr1\AdminOnboardingAssessmentController;

        use App\Http\Controllers\admin\Hr\hr2\AdminLearningEnrollController;
        use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
        use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
        use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
        use App\Http\Controllers\admin\Hr\hr2\AdminEssController;
        use App\Http\Controllers\admin\Hr\hr2\AdminLearningMaterialsController;
        use App\Http\Controllers\admin\Hr\hr2\AdminCompetencyVerificationController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingEvaluationController;


        use App\Http\Controllers\admin\Hr\hr3\AdminTimesheetController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftController;
        use App\Http\Controllers\admin\Hr\hr3\AdminInterviewScheduleController;
        use App\Http\Controllers\admin\Hr\hr3\AdminTrainingScheduleController;
        use App\Http\Controllers\admin\Hr\hr3\AdminLeaveManagementController;
        use App\Http\Controllers\admin\Hr\hr3\AdminClaimsController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftRequestController;
        
        use App\Http\Controllers\admin\Hr\hr4\AdminCoreHumanCapitalController;
        use App\Http\Controllers\admin\Hr\hr4\AdminDirectCompensationController;
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

        });

        // --- HR2 Department ---
      Route::prefix('hr2')->group(function () {


    Route::controller(AdminLearningEnrollController::class)->group(function () {
        // Main Table View
        Route::get('learning/enroll', 'index')->name('hr2.learning.enroll');
        
        // ADD THIS LINE: It defines the selection table route
        Route::get('learning/enroll/{id}', 'showEnrollment')->name('hr2.learning.show_enroll');
        
        // Final Action
        Route::post('learning/assign-modules', 'assignModules')->name('hr2.learning.assign');
    });

    // --- Succession Management ---
    Route::controller(AdminSuccessionController::class)->group(function () {
        Route::get('/departments/{dept_code}/specializations', 'getSpecializations')->name('departments.specializations');
        Route::get('/departments/{dept_code}/positions', 'getPositions')->name('departments.positions');
        Route::get('/departments/{dept_id}/employees', 'getEmployeesByDeptAndSpec');
    });

    // --- Learning & Modules ---
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
                Route::get('/', [AdminCompetencyVerificationController::class, 'index'])->name('admin.hr2.competency.verification.index');
                Route::post('/{id}/verify', [AdminCompetencyVerificationController::class, 'verify'])->name('admin.hr2.competency.verify');
            });

            // --- Shared AJAX Dropdowns ---
            Route::get('/get-specializations/{dept}', [AdminTrainingController::class, 'getSpecializations']);
            Route::get('/get-competencies/{dept}/{spec}', [AdminTrainingController::class, 'getCompetencies']);
        });

        Route::resource('competencies', CompetencyController::class);
        Route::resource('learning', AdminLearningController::class);
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
            Route::get('/timesheet', [AdminTimesheetController::class, 'index'])->name('timesheet.index');

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

            // Direct Compensation
            Route::get('/direct-compensation', [AdminDirectCompensationController::class, 'index'])
                ->name('hr4.direct_compensation.index');

            Route::post('/direct-compensation/generate', [AdminDirectCompensationController::class, 'generate'])
                ->name('hr4.direct_compensation.generate');

        });