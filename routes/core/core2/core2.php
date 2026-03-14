<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Core\core2\Core2DashboardController;
use App\Http\Controllers\user\Core\core2\PharmacyController;
use App\Http\Controllers\user\Core\core2\MedicalPackagesController;
use App\Http\Controllers\user\Core\core2\LaboratoryController;
use App\Http\Controllers\user\Core\core2\SurgeryDietController;
use App\Http\Controllers\user\Core\core2\BedLinenController;

Route::prefix('core2')->name('core2.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [Core2DashboardController::class, 'index'])->name('dashboard');

    // ── PHARMACY ─────────────────────────────────────────────────────────────────
    Route::prefix('pharmacy')->name('pharmacy.')->group(function () {

        // Drug Inventory
        Route::get('/drug-inventory',        [PharmacyController::class, 'drugInventoryIndex'])->name('drug-inventory.index');
        Route::get('/drug-inventory/create', [PharmacyController::class, 'drugInventoryCreate'])->name('drug-inventory.create');
        Route::post('/drug-inventory',       [PharmacyController::class, 'drugInventoryStore'])->name('drug-inventory.store');
        Route::get('/drug-inventory/requestform', [PharmacyController::class, 'drugInventoryRequest'])->name('drug-inventory.requestform');

        // Formula Management
        Route::get('/formula-management',        [PharmacyController::class, 'formulaManagementIndex'])->name('formula-management.index');
        Route::get('/formula-management/create', [PharmacyController::class, 'formulaManagementCreate'])->name('formula-management.create');
        Route::post('/formula-management',       [PharmacyController::class, 'formulaManagementStore'])->name('formula-management.store');

        // Prescriptions
        Route::get('/prescription',        [PharmacyController::class, 'prescriptionIndex'])->name('prescription.index');
        Route::get('/prescription/create', [PharmacyController::class, 'prescriptionCreate'])->name('prescription.create');
        Route::post('/prescription',       [PharmacyController::class, 'prescriptionStore'])->name('prescription.store');
    });

    // ── MEDICAL PACKAGES ──────────────────────────────────────────────────────────
    Route::prefix('medical-packages')->name('medical-packages.')->group(function () {

        // Package Definition & Pricing
        Route::get('/packages',        [MedicalPackagesController::class, 'packagesIndex'])->name('packages.index');
        Route::get('/packages/create', [MedicalPackagesController::class, 'packagesCreate'])->name('packages.create');
        Route::post('/packages',       [MedicalPackagesController::class, 'packagesStore'])->name('packages.store');

        // Patient Package Enrollment
        Route::get('/enrollment',        [MedicalPackagesController::class, 'enrollmentIndex'])->name('enrollment.index');
        Route::get('/enrollment/create', [MedicalPackagesController::class, 'enrollmentCreate'])->name('enrollment.create');
        Route::post('/enrollment',       [MedicalPackagesController::class, 'enrollmentStore'])->name('enrollment.store');
    });

    // ── LABORATORY ────────────────────────────────────────────────────────────────
    Route::prefix('laboratory')->name('laboratory.')->group(function () {

        // Test Ordering & Registration (Received orders only)
        Route::get('/test-orders',        [LaboratoryController::class, 'testOrdersIndex'])->name('test-orders.index');
        Route::get('/test-orders/create', [LaboratoryController::class, 'testOrdersCreate'])->name('test-orders.create');
        Route::post('/test-orders',       [LaboratoryController::class, 'testOrdersStore'])->name('test-orders.store');
        Route::patch('/test-orders/{id}/collect-sample', [LaboratoryController::class, 'collectSample'])->name('test-orders.collect-sample');

        // Sample Tracking & LIS Integration (SampleCollected + Processing)
        Route::get('/sample-tracking',                          [LaboratoryController::class, 'sampleTrackingIndex'])->name('sample-tracking.index');
        Route::patch('/sample-tracking/{id}/start-processing',  [LaboratoryController::class, 'startProcessing'])->name('sample-tracking.start-processing');

        // Result Entry & Validation (Processing + ResultReady + Validated + Sent)
        Route::get('/result-validation',                     [LaboratoryController::class, 'resultValidationIndex'])->name('result-validation.index');
        Route::post('/result-validation/{id}/result',        [LaboratoryController::class, 'enterResult'])->name('result-validation.enter-result');
        Route::post('/result-validation/{id}/validate-send', [LaboratoryController::class, 'validateAndSend'])->name('result-validation.validate-send');
    });

    // ── SURGERY & DIET ────────────────────────────────────────────────────────────
    Route::prefix('surgery-diet')->name('surgery-diet.')->group(function () {

        // Operating Room Booking
        Route::get('/or-booking',        [SurgeryDietController::class, 'orBookingIndex'])->name('or-booking.index');
        Route::get('/or-booking/create', [SurgeryDietController::class, 'orBookingCreate'])->name('or-booking.create');
        Route::post('/or-booking',       [SurgeryDietController::class, 'orBookingStore'])->name('or-booking.store');

        // Nutritional Assessment & Consultation
        Route::get('/nutritional',        [SurgeryDietController::class, 'nutritionalIndex'])->name('nutritional.index');
        Route::get('/nutritional/create', [SurgeryDietController::class, 'nutritionalCreate'])->name('nutritional.create');
        Route::post('/nutritional',       [SurgeryDietController::class, 'nutritionalStore'])->name('nutritional.store');

        // Utilization Reporting
        Route::get('/utilization',        [SurgeryDietController::class, 'utilizationIndex'])->name('utilization.index');
        Route::get('/utilization/create', [SurgeryDietController::class, 'utilizationCreate'])->name('utilization.create');
        Route::post('/utilization',       [SurgeryDietController::class, 'utilizationStore'])->name('utilization.store');
    });

    // ── BED & LINEN ───────────────────────────────────────────────────────────────
    Route::prefix('bed-linen')->name('bed-linen.')->group(function () {

        // Pending Admissions Queue (Core 1 → Core 2 sync)
        Route::get('/pending-admissions', [BedLinenController::class, 'pendingAdmissionsIndex'])->name('pending-admissions.index');
        Route::get('/floor-map-data', [BedLinenController::class, 'floorMapData'])->name('floor-map-data');
        Route::post('/allocate-bed', [BedLinenController::class, 'allocateBed'])->name('allocate-bed');

        // Room Assignment
        Route::get('/room-assignment',        [BedLinenController::class, 'roomAssignmentIndex'])->name('room-assignment.index');
        Route::get('/room-assignment/create', [BedLinenController::class, 'roomAssignmentCreate'])->name('room-assignment.create');
        Route::post('/room-assignment',       [BedLinenController::class, 'roomAssignmentStore'])->name('room-assignment.store');

        // Bed Status & Allocation
        Route::get('/bed-status',        [BedLinenController::class, 'bedStatusIndex'])->name('bed-status.index');
        Route::get('/bed-status/create', [BedLinenController::class, 'bedStatusCreate'])->name('bed-status.create');
        Route::post('/bed-status',       [BedLinenController::class, 'bedStatusStore'])->name('bed-status.store');
        Route::patch('/bed-status/{id}', [BedLinenController::class, 'updateBedStatus'])->name('bed-status.update');

        // Patient Transfer Management
        Route::get('/patient-transfer',        [BedLinenController::class, 'patientTransferIndex'])->name('patient-transfer.index');
        Route::get('/patient-transfer/create', [BedLinenController::class, 'patientTransferCreate'])->name('patient-transfer.create');
        Route::post('/patient-transfer',       [BedLinenController::class, 'patientTransferStore'])->name('patient-transfer.store');

        // House Keeping & Cleaning Status
        Route::get('/house-keeping',        [BedLinenController::class, 'houseKeepingIndex'])->name('house-keeping.index');
        Route::get('/house-keeping/create', [BedLinenController::class, 'houseKeepingCreate'])->name('house-keeping.create');
        Route::post('/house-keeping',       [BedLinenController::class, 'houseKeepingStore'])->name('house-keeping.store');
    });
});
