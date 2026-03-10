<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\hr1\DashboardController_hr1;
use App\Http\Controllers\Hr\hr1\ApplicantController_hr1;
use App\Http\Controllers\Hr\hr1\JobController_hr1;
use App\Http\Controllers\Hr\hr1\ApplicationController_hr1;
use App\Http\Controllers\Hr\hr1\RecognitionController_hr1;
use App\Http\Controllers\Hr\hr1\OnboardingController_hr1;
use App\Http\Controllers\Hr\hr1\LearningModuleController_hr1;
use App\Http\Controllers\Hr\hr1\EvaluationController_hr1;

// HR1 Index Route
Route::prefix('hr/hr1')->name('hr.hr1.')->group(function () {
    Route::get('/', fn () => view('hr.hr1.index'))->name('index');
});

// Change FROM:
Route::get('/dashboard_hr1', [DashboardController_hr1::class, 'index'])->name('dashboard_hr1');

// API Routes
Route::prefix('api/hr1')->group(function () {
    // Applicants
    Route::get('/applicants', [ApplicantController_hr1::class, 'index']);
    Route::post('/applicants', [ApplicantController_hr1::class, 'store']);
    Route::get('/applicants/{id}', [ApplicantController_hr1::class, 'show']);
    Route::patch('/applicants/{id}', [ApplicantController_hr1::class, 'update']);
    Route::patch('/applicants/{id}/status', [ApplicantController_hr1::class, 'updateStatus']);
    Route::get('/applicants/export', [ApplicantController_hr1::class, 'exportByStatus']);

    // Jobs
    Route::get('/jobs', [JobController_hr1::class, 'index']);
    Route::post('/jobs', [JobController_hr1::class, 'store']);
    Route::patch('/jobs/{id}', [JobController_hr1::class, 'update']);
    Route::delete('/jobs/{id}', [JobController_hr1::class, 'destroy']);

    // Applications
    Route::post('/applications', [ApplicationController_hr1::class, 'store']);
    Route::get('/applications/{id}', [ApplicationController_hr1::class, 'show']);
    Route::patch('/applications/{id}', [ApplicationController_hr1::class, 'update']);
    Route::delete('/applications/{id}', [ApplicationController_hr1::class, 'destroy']);
    Route::post('/applications/{id}/interview', [ApplicationController_hr1::class, 'scheduleInterview']);
    
    // Question Sets - Submit Assessment
    Route::post('/question-sets/{id}/submit', [EvaluationController_hr1::class, 'submitAssessment']);
    
    // Learning Modules - Complete
    Route::post('/modules/complete/{id}', [LearningModuleController_hr1::class, 'markComplete']);
    
    // Candidate Profile
    Route::patch('/candidate/profile', [DashboardController_hr1::class, 'updateCandidateProfile']);
    Route::patch('/candidate/status', [DashboardController_hr1::class, 'updateCandidateStatus']);

    // Recognitions
    Route::get('/recognitions', [RecognitionController_hr1::class, 'index']);
    Route::post('/recognitions', [RecognitionController_hr1::class, 'store']);
    Route::patch('/recognitions/{id}', [RecognitionController_hr1::class, 'update']);
    Route::post('/recognitions/{id}/congratulate', [RecognitionController_hr1::class, 'congratulate']);
    Route::post('/recognitions/{id}/boost', [RecognitionController_hr1::class, 'boost']);
    Route::delete('/recognitions/{id}', [RecognitionController_hr1::class, 'destroy']);
    
    // Task Sets
    Route::get('/task-sets', [OnboardingController_hr1::class, 'taskSets']);
    Route::post('/task-sets', [OnboardingController_hr1::class, 'storeTaskSet']);
    Route::patch('/task-sets/{id}', [OnboardingController_hr1::class, 'updateTaskSet']);
    Route::delete('/task-sets/{id}', [OnboardingController_hr1::class, 'destroyTaskSet']);
    
    // Question Sets
    Route::get('/question-sets', [EvaluationController_hr1::class, 'questionSets']);
    Route::post('/question-sets', [EvaluationController_hr1::class, 'storeQuestionSet']);
    Route::patch('/question-sets/{id}', [EvaluationController_hr1::class, 'updateQuestionSet']);
    Route::patch('/question-sets/{id}/assign-job', [EvaluationController_hr1::class, 'assignToJob']);
    Route::delete('/question-sets/{id}', [EvaluationController_hr1::class, 'destroyQuestionSet']);
    
    // Admin Profile
    Route::patch('/admin/profile', [DashboardController_hr1::class, 'updateProfile']);

    // Onboarding
    Route::get('/tasks', [OnboardingController_hr1::class, 'index']);
    Route::post('/tasks', [OnboardingController_hr1::class, 'store']);
    Route::patch('/tasks/{id}/status', [OnboardingController_hr1::class, 'updateStatus']);
    
    // Applicant Tasks (from applicant_tasks_hr1 table)
    Route::get('/applicant-tasks', [OnboardingController_hr1::class, 'applicantTasks']);
    Route::post('/applicant-tasks', [OnboardingController_hr1::class, 'storeApplicantTask']);
    Route::patch('/applicant-tasks/{id}/status', [OnboardingController_hr1::class, 'updateApplicantTaskStatus']);
    Route::patch('/applicant-tasks/{id}', [OnboardingController_hr1::class, 'updateApplicantTask']);
    Route::delete('/applicant-tasks/{id}', [OnboardingController_hr1::class, 'deleteApplicantTask']);

    // Learning Modules
    Route::get('/modules', [LearningModuleController_hr1::class, 'index']);
    Route::post('/modules', [LearningModuleController_hr1::class, 'store']);
    Route::post('/modules/assign/{userId}', [LearningModuleController_hr1::class, 'assign']);

    // Evaluation
    Route::get('/evaluation-criteria', [EvaluationController_hr1::class, 'index']);
    Route::post('/evaluation-criteria', [EvaluationController_hr1::class, 'store']);
    Route::delete('/evaluation-criteria/{id}', [EvaluationController_hr1::class, 'destroy']);
});

