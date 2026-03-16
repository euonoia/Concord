<?php

use Illuminate\Support\Facades\Route;
// Import Controllers
use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1WarehouseController;
use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1ProcurementController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminVendorController;
use App\Http\Controllers\admin\Logistics\Logistics2\AdminVehicleReservationController;

// --- General Dashboard ---
Route::get('/dashboard', function () { 
    return view('logistics.dashboard'); 
})->name('logistics.dashboard');


// --- Logistics 1 (Internal Warehouse & Procurement) ---
Route::prefix('logistics1')->name('admin.logistics1.')->group(function () {
    
    // Warehouse
    Route::get('/warehouse', [AdminLogistics1WarehouseController::class, 'index'])
        ->name('warehouse.index');

    // Procurement
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/', [AdminLogistics1ProcurementController::class, 'index'])->name('index');
        Route::post('/request', [AdminLogistics1ProcurementController::class, 'store'])->name('store');
    });
});


Route::prefix('logistics2')->name('admin.logistics2.')->group(function () {
    
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/index', [AdminVendorController::class, 'index'])->name('index');
        Route::post('/process/{id}', [AdminVendorController::class, 'processRequest'])->name('process');
    });

    Route::prefix('vehicle')->name('vehicle.')->group(function () {
        Route::get('/index', [AdminVehicleReservationController::class, 'index'])->name('index');
        Route::post('/transit/{id}', [AdminVehicleReservationController::class, 'startTransit'])->name('transit');
        Route::post('/complete/{id}', [AdminVehicleReservationController::class, 'completeDelivery'])->name('complete');
    });
});