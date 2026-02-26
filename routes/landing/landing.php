<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentLookupController;

// The main landing page is the root '/'
Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');

Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/api/doctors/by-service-type', [DoctorController::class, 'getByServiceType'])->name('api.doctors.byServiceType');

Route::post('/appointments/lookup', [AppointmentLookupController::class, 'lookup'])->name('appointments.lookup');
Route::post('/appointments/track/{appointmentNo}/cancel', [AppointmentLookupController::class, 'cancel'])->name('appointments.cancel');
