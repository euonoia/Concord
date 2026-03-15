<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\Financials\AdminFinancialsReimbursementController;

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