<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Hr\UserDashboardController;
use App\Http\Controllers\user\Hr\hr2\UserCompetencyController;
use App\Http\Controllers\user\Hr\hr2\UserLearningController;
use App\Http\Controllers\user\Hr\hr2\UserTrainingController;
use App\Http\Controllers\user\Hr\hr2\UserSuccessionController;
use App\Http\Controllers\user\Hr\hr2\UserEssController;
use App\Http\Controllers\user\Hr\hr2\UserLearningMaterialsController;
use App\Http\Controllers\user\Hr\hr2\UserPayrollController;

// HR3 Controllers
use App\Http\Controllers\user\Hr\hr3\UserAttendanceController;
use App\Http\Controllers\user\Hr\hr3\UserClaimsController;


/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->name('hr.dashboard');


/*
|--------------------------------------------------------------------------
| HR2 - DEVELOPMENT & PLANNING
|--------------------------------------------------------------------------
*/

// Competencies
Route::get('/my-competencies', [UserCompetencyController::class, 'index'])
    ->name('user.competencies.index');

Route::post('/competency/enroll/{competency_code}', [UserCompetencyController::class, 'enroll'])
    ->name('user.competency.enroll');

Route::post('/my-competencies/complete/{competency_code}', [UserCompetencyController::class, 'complete'])
    ->name('user.competency.complete');


// Training & Succession
Route::get('/my-training', [UserTrainingController::class, 'index'])
    ->name('user.training.index');

Route::get('/my-succession', [UserSuccessionController::class, 'index'])
    ->name('user.succession.index');


// ESS (Requests / Leave / Profile / etc.)
Route::get('/ess', [UserEssController::class, 'index'])
    ->name('user.ess.index');

Route::post('/ess/store', [UserEssController::class, 'store'])
    ->name('user.ess.store');

Route::post('/user/request-shift/{id}', [UserEssController::class, 'requestShift'])
    ->name('user.shift.request');


// Payroll
Route::get('/payroll', [UserPayrollController::class, 'index'])
    ->name('user.payroll.index');

Route::post('/payroll/store', [UserPayrollController::class, 'store'])
    ->name('user.payroll.store');


// ESS Payroll Requests
Route::get('/ess-payroll', [UserEssController::class, 'payrollIndex'])
    ->name('user.ess.payroll.index');

Route::post('/ess-payroll/store', [UserEssController::class, 'payrollStore'])
    ->name('user.ess.payroll.store');


// Learning System
Route::prefix('learning')->group(function () {

    // Available Courses
    Route::get('/', [UserLearningController::class, 'index'])
        ->name('user.learning.index');

    Route::post('/enroll/{module_code}', [UserLearningController::class, 'enroll'])
        ->name('user.learning.enroll');

    // Learning Materials
    Route::get('/materials', [UserLearningMaterialsController::class, 'index'])
        ->name('user.learning.materials.index');

    Route::get('/materials/{module_code}', [UserLearningMaterialsController::class, 'showModule'])
        ->name('user.learning.materials.show');

});


/*
|--------------------------------------------------------------------------
| HR3 - ATTENDANCE & TIMEKEEPING
|--------------------------------------------------------------------------
*/

// QR / Face / Attendance
Route::get('/attendance/scan', [UserAttendanceController::class, 'scanView'])
    ->name('user.attendance.scan');

Route::match(['get', 'post'], '/attendance/verify', [UserAttendanceController::class, 'verify'])
    ->name('attendance.verify');

Route::get('/attendance/success', [UserAttendanceController::class, 'success'])
    ->name('user.attendance.success');


// Claims / Reimbursements
Route::post('/claims/store', [UserClaimsController::class, 'store'])
    ->name('user.claims.store');