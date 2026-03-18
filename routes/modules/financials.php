<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\Financials\AdminFinancialsReimbursementController;
use App\Http\Controllers\admin\Financials\AdminBillsCollectionController;
use App\Http\Controllers\admin\Financials\AdminBillARController;
use App\Http\Controllers\admin\Financials\MaintenancePayableController;
use App\Http\Controllers\admin\Financials\MaintenanceLedgerController;
use App\Http\Controllers\admin\Financials\BillsLedgerController;


Route::get('/dashboard', function () { 
    return view('financials.dashboard'); 
})->name('finance.dashboard');

Route::prefix('disbursement')->group(function () {

    Route::get('/reimbursement',
        [AdminFinancialsReimbursementController::class,'index']
    )->name('financials.reimbursement.index');


    Route::post('/reimbursement/{id}',
        [AdminFinancialsReimbursementController::class,'reimburse']
    )->name('financials.reimbursement.process');

});


// AP & AR Module

Route::prefix('apar')->group(function () {
    // Existing Receivables routes
    Route::get('/receivables', [AdminBillARController::class, 'indexAR'])->name('financials.apar.index');
    Route::post('/approve/{id}', [AdminBillARController::class, 'approveForCollection'])->name('financials.apar.approve');

    // Maintenance Payable
    Route::get('/maintenance-payable', [MaintenancePayableController::class, 'index'])
         ->name('financials.apar.maintenance-payable');

    Route::post('/maintenance-payable/{id}/pay', [MaintenancePayableController::class, 'markAsPaid'])
         ->name('financials.apar.maintenance-payable.pay');
});
// Collections Module (Now only handles APPROVED bills)
Route::prefix('bills')->group(function () {
    Route::get('/', [AdminBillsCollectionController::class, 'index'])->name('financials.bills.index');

    Route::get('/{id}', [AdminBillsCollectionController::class, 'show'])->name('financials.bills.show');
    Route::post('/{id}/pay', [AdminBillsCollectionController::class, 'markAsPaid'])->name('financials.bills.pay');
});


Route::prefix('ledger')->group(function () {
    Route::get('/maintenance', [MaintenanceLedgerController::class, 'index'])
         ->name('financials.maintenance-ledger');
      Route::get('/bills', [BillsLedgerController::class, 'index'])
         ->name('financials.bills-ledger.index');

    Route::get('/bills/{id}', [BillsLedgerController::class, 'show'])
         ->name('financials.bills-ledger.show');
});