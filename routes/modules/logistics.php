<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1WarehouseController;
use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1ProcurementController;
use App\Http\Controllers\admin\Logistics\Logistics1\AdminMaintenanceController;

use App\Http\Controllers\admin\Logistics\Logistics2\AdminVendorController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminVehicleReservationController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminFleetController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminDocumentTrackingLabOrdersController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminVendorPortalController;


// --- Logistics 1 Group ---
Route::prefix('logistics1')->name('admin.logistics1.')->group(function () {
    Route::get('/warehouse', [AdminLogistics1WarehouseController::class, 'index'])->name('warehouse.index');
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/', [AdminLogistics1ProcurementController::class, 'index'])->name('index');
        Route::post('/request', [AdminLogistics1ProcurementController::class, 'store'])->name('store');
    });

Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [AdminMaintenanceController::class, 'index'])->name('index');
        Route::post('/repair', [AdminMaintenanceController::class, 'recordRepair'])->name('repair');
    });
});

// --- Logistics 2 Group ---
Route::prefix('logistics2')->name('admin.logistics2.')->group(function () {
    
    // Vendor
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/index', [AdminVendorController::class, 'index'])->name('index');
        Route::post('/process/{id}', [AdminVendorController::class, 'processRequest'])->name('process');
    });

    // Vehicle
    Route::prefix('vehicle')->name('vehicle.')->group(function () {
        Route::get('/index', [AdminVehicleReservationController::class, 'index'])->name('index');
        Route::post('/transit/{id}', [AdminVehicleReservationController::class, 'startTransit'])->name('transit');
        Route::post('/complete/{id}', [AdminVehicleReservationController::class, 'completeDelivery'])->name('complete');
    });

    // Fleet (Correctly Nested Now)
    Route::prefix('fleet')->name('fleet.')->group(function () {
        Route::get('/', [AdminFleetController::class, 'index'])->name('index');
        Route::post('/store', [AdminFleetController::class, 'store'])->name('store');
        Route::post('/update-status/{id}', [AdminFleetController::class, 'updateStatus'])->name('update_status');
    });

    Route::get('/audit', [App\Http\Controllers\admin\Logistics\Logistics2\AdminAuditController::class, 'index'])->name('audit.index');
    // --- Document Tracking (Lab Orders from CORE1) ---
    Route::prefix('document')->name('document.')->group(function () {
        Route::get('/', [AdminDocumentTrackingLabOrdersController::class, 'index'])->name('index');
        Route::get('/result/{id}', [AdminDocumentTrackingLabOrdersController::class, 'viewResult'])->name('result');
    });
    Route::get('/diet-orders', [AdminDocumentTrackingLabOrdersController::class, 'dietIndex'])
    ->name('document.diet');
    Route::get('/surgery-orders', [AdminDocumentTrackingLabOrdersController::class, 'surgeryIndex'])
    ->name('document.surgery');

    // Vendor Master (Vendor Portal)
Route::prefix('vendor-portal')->name('vendor.portal.')->group(function () {

    Route::get('/', [AdminVendorPortalController::class, 'index'])->name('index');
    Route::get('/create', [AdminVendorPortalController::class, 'create'])->name('create');
    Route::post('/store', [AdminVendorPortalController::class, 'store'])->name('store');

    Route::get('/edit/{id}', [AdminVendorPortalController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [AdminVendorPortalController::class, 'update'])->name('update');

    Route::post('/delete/{id}', [AdminVendorPortalController::class, 'destroy'])->name('delete');
});
// Warehouse Purchase Orders (from Logistics1)
Route::prefix('warehouse-purchase-orders')->name('purchase.')->group(function () {

    Route::get('/', [App\Http\Controllers\admin\Logistics\Logistics2\AdminWarehousePurchaseOrdersController::class, 'index'])
        ->name('index');

    Route::post('/assign/{id}', [App\Http\Controllers\admin\Logistics\Logistics2\AdminWarehousePurchaseOrdersController::class, 'assignVehicle'])
        ->name('assign');
});
});