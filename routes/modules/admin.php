        <?php
        // routes/modules/admin.php

        use Illuminate\Support\Facades\Route;

        use App\Http\Controllers\admin\Hr\hr1\ApplicantManagementController;

        use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
        use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
        use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
        use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
        use App\Http\Controllers\admin\Hr\hr2\AdminEssController;

        use App\Http\Controllers\admin\Hr\hr3\AdminTimesheetController;
        use App\Http\Controllers\admin\Hr\hr3\AdminShiftController;

        use App\Http\Controllers\admin\Hr\hr4\AdminCoreHumanCapitalController;
        use App\Http\Controllers\admin\Hr\hr4\AdminDirectCompensationController;


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

        Route::get('/get-specializations/{dept}', [AdminLearningController::class,'getSpecializations'])
        ->name('hr2.getSpecializations');

        Route::get('/generate-module-code/{dept}/{spec}', [AdminLearningController::class,'generateModuleCode'])
            ->name('hr2.generateModuleCode');
            
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
            Route::get('/timesheet', [AdminTimesheetController::class, 'index'])->name('timesheet.index');

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

            // Direct Compensation
            Route::get('/direct-compensation', [AdminDirectCompensationController::class, 'index'])
                ->name('hr4.direct_compensation.index');

            Route::post('/direct-compensation/generate', [AdminDirectCompensationController::class, 'generate'])
                ->name('hr4.direct_compensation.generate');

        });