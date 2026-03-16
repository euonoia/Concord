<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('logistics.dashboard'); 
})->name('logistics.dashboard');

// Add payroll, staff management here


use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1WarehouseController;

Route::get('/warehouse', [AdminLogistics1WarehouseController::class, 'index'])
    ->name('admin.logistics1.warehouse.index');

use App\Http\Controllers\admin\Logistics\Logistics1\AdminLogistics1ProcurementController;

Route::get('/procurement', [AdminLogistics1ProcurementController::class, 'index'])
    ->name('admin.logistics1.procurement.index');

Route::post('/procurement/request', [AdminLogistics1ProcurementController::class, 'store'])
    ->name('admin.logistics1.procurement.store');