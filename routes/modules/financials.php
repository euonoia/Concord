<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\Financials\AdminFinancialsReimbursementController;
use App\Http\Controllers\admin\Financials\AdminBillsCollectionController;

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

Route::prefix('bills')->group(function () {

    // List all bills
    Route::get('/', [AdminBillsCollectionController::class, 'index'])
        ->name('financials.bills.index');

    // Show single bill details
    Route::get('/{id}', [AdminBillsCollectionController::class, 'show'])
        ->name('financials.bills.show');

    // Mark bill as paid
    Route::post('/{id}/pay', [AdminBillsCollectionController::class, 'markAsPaid'])
        ->name('financials.bills.pay');
});

