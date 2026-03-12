        <?php
        // routes/modules/admin.php

        use Illuminate\Support\Facades\Route;

        use App\Http\Controllers\admin\Hr\hr1\ApplicantManagementController;
        use App\Http\Controllers\admin\Hr\hr1\NewHireController;
        use App\Http\Controllers\admin\Hr\hr1\AdminTrainingPerformanceController;

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

        use App\Http\Controllers\admin\Hr\hr4\AdminCoreHumanCapitalController;
        use App\Http\Controllers\admin\Hr\hr4\AdminDirectCompensationController;
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
            Route::get('/newhires', [NewHireController::class, 'index'])->name('hr1.newhires.index');
            Route::get('/newhires/{id}', [NewHireController::class, 'show'])->name('hr1.newhires.show');
            Route::get('/newhires/{id}/resume', [NewHireController::class, 'downloadResume'])->name('hr1.newhires.download');
            Route::post('/newhires/{id}/status', [NewHireController::class, 'updateStatus'])->name('hr1.newhires.updateStatus');

           Route::get('/training-performance', [AdminTrainingPerformanceController::class, 'index'])
                 ->name('hr1.training.performance.index');

            Route::get('/training-performance/{employee_id}', [AdminTrainingPerformanceController::class, 'show'])
                ->name('hr1.training.performance.show');
            
            // Fixed: Removed leading /admin/ as the prefix handles it
            Route::post('/training-performance/{employee_id}/validate', [AdminTrainingPerformanceController::class, 'validateAndStore'])
                ->name('hr1.training.performance.validate');
        });

        // --- HR2 Department ---
        Route::prefix('hr2')->group(function () {

            // --- Succession Management ---
            Route::get('/departments/{dept_code}/specializations', [AdminSuccessionController::class, 'getSpecializations'])->name('departments.specializations');
            Route::get('/departments/{dept_code}/positions', [AdminSuccessionController::class, 'getPositions'])->name('departments.positions');
            Route::get('/departments/{dept_id}/employees', [AdminSuccessionController::class, 'getEmployeesByDeptAndSpec']);

            // --- Learning & Modules ---
            Route::get('/departments/{dept_code}/{spec}/competencies', [AdminLearningController::class, 'getCompetencies'])->name('departments.competencies');
            Route::get('/generate-module-code/{dept}/{spec}', [AdminLearningController::class,'generateModuleCode'])->name('hr2.generateModuleCode');
                
            // --- Learning Materials ---
            Route::get('/learning-materials', [AdminLearningMaterialsController::class, 'selector'])->name('learning.materials.selector');
            Route::get('/materials/{moduleCode}/list', [AdminLearningMaterialsController::class, 'listMaterials'])->name('learning.materials.list');
            Route::post('/{moduleCode}/materials', [AdminLearningMaterialsController::class, 'store'])->name('learning.materials.store');
            Route::delete('/materials/{id}', [AdminLearningMaterialsController::class, 'destroy'])->name('learning.materials.destroy');
            Route::get('/modules/{dept}/{spec}', [AdminLearningMaterialsController::class, 'getModulesByDeptSpec']);

            // --- Competency Verification ---
            Route::get('/competency-verification', [AdminCompetencyVerificationController::class,'index'])->name('admin.hr2.competency.verification.index');
            Route::post('/competency-verification/{id}/verify', [AdminCompetencyVerificationController::class,'verify'])->name('admin.hr2.competency.verify');

            // --- Training Viewer (AdminTrainingController) ---
            Route::get('/training', [AdminTrainingController::class,'index'])->name('hr2.training');
            Route::get('/training/{id}', [AdminTrainingController::class,'show'])->name('training.show');
            // Basic employee list (No evaluation scores)
            Route::get('/eligible-employees', [AdminTrainingController::class,'getEligibleEmployees'])->name('hr2.training.employees');

            // --- Training EVALUATION (AdminTrainingEvaluationController) ---
            // NEW UNIQUE URL for evaluation data to avoid collision
            Route::get('/evaluation-eligible-employees', [AdminTrainingEvaluationController::class, 'getEligibleEmployees'])->name('hr2.evaluation.employees');
            
            Route::get('/training-evaluation/evaluate', [AdminTrainingEvaluationController::class, 'showEvaluation'])->name('hr2.training_evaluation.show');
            Route::post('/training-evaluation/evaluate', [AdminTrainingEvaluationController::class, 'storeEvaluation'])->name('hr2.training_evaluation.store');

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