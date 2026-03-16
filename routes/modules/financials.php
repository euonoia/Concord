<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\Financials\AdminFinancialsReimbursementController;
use App\Http\Controllers\admin\Financials\AdminBillsCollectionController;
use App\Http\Controllers\admin\Financials\AdminBillARController;

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
    Route::get('/receivables', [AdminBillARController::class, 'indexAR'])->name('financials.apar.index');
    Route::post('/approve/{id}', [AdminBillARController::class, 'approveForCollection'])->name('financials.apar.approve');
});

// Collections Module (Now only handles APPROVED bills)
Route::prefix('bills')->group(function () {
    Route::get('/', [AdminBillsCollectionController::class, 'index'])->name('financials.bills.index');
    Route::post('/{id}/pay', [AdminBillsCollectionController::class, 'markAsPaid'])->name('financials.bills.pay');
});