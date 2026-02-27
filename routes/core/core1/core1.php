<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Core\core1\core1Controller;
use App\Http\Controllers\user\Core\core1\Admin\AdminDashboardController;
use App\Http\Controllers\user\Core\core1\Doctor\DoctorDashboardController;
use App\Http\Controllers\user\Core\core1\Nurse\NurseDashboardController;
use App\Http\Controllers\user\Core\core1\Patient\PatientDashboardController;
use App\Http\Controllers\user\Core\core1\Receptionist\ReceptionistDashboardController;
use App\Http\Controllers\user\Core\core1\Billing\BillingDashboardController;
use App\Http\Controllers\user\Core\core1\PatientManagementController;
use App\Http\Controllers\user\Core\core1\AppointmentController;
use App\Http\Controllers\user\Core\core1\InpatientController;
use App\Http\Controllers\user\Core\core1\OutpatientController;
use App\Http\Controllers\user\Core\core1\MedicalRecordController;
use App\Http\Controllers\user\Core\core1\BillingController;
use App\Http\Controllers\user\Core\core1\DischargeController;
use App\Http\Controllers\user\Core\core1\StaffManagementController;
use App\Http\Controllers\user\Core\core1\ReportsController;
use App\Http\Controllers\user\Core\core1\SettingsController;

Route::prefix('core1')->name('core1.')->group(function () {
    // Main index
    Route::get('/', [core1Controller::class, 'index'])->name('index');
    // Nested routes
    Route::get('/policies', [core1Controller::class, 'policies'])->name('policies');
    Route::get('/reports', [core1Controller::class, 'reports'])->name('reports');
});

Route::middleware([])->group(function () {
    // Dashboard Routes by Role
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('core1.admin.dashboard');
        Route::get('/overview', [AdminDashboardController::class, 'overview'])->name('core1.admin.overview');
    });
    
    Route::prefix('doctor')->middleware('role:doctor')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('core1.doctor.dashboard');
        Route::get('/overview', [DoctorDashboardController::class, 'overview'])->name('core1.doctor.overview');
    });
    
    Route::prefix('nurse')->middleware('role:nurse,head_nurse')->group(function () {
        Route::get('/dashboard', [NurseDashboardController::class, 'index'])->name('core1.nurse.dashboard');
        Route::get('/overview', [NurseDashboardController::class, 'overview'])->name('core1.nurse.overview');
    });
    
    Route::prefix('patient')->middleware('role:patient')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('core1.patient.dashboard');
        Route::get('/overview', [PatientDashboardController::class, 'overview'])->name('core1.patient.overview');
    });
    
    Route::prefix('receptionist')->middleware('role:receptionist')->group(function () {
        Route::get('/dashboard', [ReceptionistDashboardController::class, 'index'])->name('core1.receptionist.dashboard');
        Route::get('/overview', [ReceptionistDashboardController::class, 'overview'])->name('core1.receptionist.overview');
        // Online Appointment Actions
        Route::post('/online-appointments/{appointment}/approve', [\App\Http\Controllers\user\Core\core1\Receptionist\OnlineAppointmentController::class, 'approve'])->name('core1.receptionist.online-appointments.approve');
        Route::post('/online-appointments/{appointment}/reject', [\App\Http\Controllers\user\Core\core1\Receptionist\OnlineAppointmentController::class, 'reject'])->name('core1.receptionist.online-appointments.reject');
        Route::get('/online-appointments/pending', [ReceptionistDashboardController::class, 'pendingBookingsJson'])->name('core1.receptionist.online-appointments.pending');
    });
    
    Route::prefix('billing')->middleware('role:billing')->group(function () {
        Route::get('/dashboard', [BillingDashboardController::class, 'index'])->name('core1.billing.dashboard');
        Route::get('/overview', [BillingDashboardController::class, 'overview'])->name('core1.billing.overview');
    });
    
    // Shared Feature Routes
    Route::middleware('role:admin,doctor,nurse,head_nurse,receptionist')->group(function () {
        Route::get('/patients', [PatientManagementController::class, 'index'])->name('core1.patients.index');
        Route::get('/patients/create', [PatientManagementController::class, 'create'])->name('core1.patients.create');
        Route::post('/patients', [PatientManagementController::class, 'store'])->name('core1.patients.store');
        Route::get('/patients/{patient}', [PatientManagementController::class, 'show'])->name('core1.patients.show');
        Route::get('/patients/{patient}/edit', [PatientManagementController::class, 'edit'])->name('core1.patients.edit');
        Route::put('/patients/{patient}', [PatientManagementController::class, 'update'])->name('core1.patients.update');
        Route::delete('/patients/{patient}', [PatientManagementController::class, 'destroy'])->name('core1.patients.destroy');
        Route::post('/patients/{patient}/assign-nurse', [PatientManagementController::class, 'assignNurse'])->name('core1.patients.assign-nurse');
    });
    
    Route::middleware('role:admin,doctor,patient,receptionist')->group(function () {
        Route::get('/appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('core1.appointments.check-availability');
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('core1.appointments.index');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('core1.appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('core1.appointments.store');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('core1.appointments.show');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('core1.appointments.update');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('core1.appointments.destroy');
    });

    Route::middleware('role:doctor')->group(function() {
        Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])->name('core1.appointments.accept');
        Route::post('/appointments/{appointment}/decline', [AppointmentController::class, 'decline'])->name('core1.appointments.decline');
    });

    Route::middleware('role:admin,doctor,nurse,head_nurse')->group(function () {
        Route::get('/inpatient', [InpatientController::class, 'index'])->name('core1.inpatient.index');
    });

    Route::middleware('role:admin,doctor')->group(function () {
        Route::get('/outpatient', [OutpatientController::class, 'index'])->name('core1.outpatient.index');
        Route::get('/discharge', [DischargeController::class, 'index'])->name('core1.discharge.index');
    });

    Route::post('/patients/{patient}/move', 
        [\App\Http\Controllers\user\Core\core1\PatientManagementController::class, 'move']
    )->name('core1.patients.move');
    Route::patch('/inpatient/{patient}/deactivate', [InpatientController::class, 'deactivate'])->name('core1.inpatients.deactivate');
    Route::patch('/patients/{patient}/status', 
        [PatientManagementController::class, 'updateStatus']
    )->name('core1.patients.updateStatus');
    Route::post('/outpatient/{id}/update-status',
        [OutpatientController::class, 'updateStatus']
    )->name('core1.outpatient.updateStatus');
    Route::post('/core1/outpatient/{id}/triage',
        [OutpatientController::class, 'saveTriage']
    )->name('core1.outpatient.saveTriage');
    // Prescriptions
    Route::post('/outpatient/prescription/store', [OutpatientController::class, 'storePrescription'])
        ->name('core1.outpatient.storePrescription');

    Route::put('/outpatient/prescription/update/{id}', [OutpatientController::class, 'updatePrescription'])
        ->name('core1.outpatient.updatePrescription');

    Route::post('/core1/outpatient/store-lab-order',
        [OutpatientController::class, 'storeLabOrder']
    )->name('core1.outpatient.storeLabOrder');

    Route::post('/outpatient/follow-up/store',
        [OutpatientController::class,'storeFollowUp']
    )->name('core1.outpatient.storeFollowUp');
    Route::put('/outpatient/follow-up/{id}/update', [OutpatientController::class, 'updateFollowUp'])
        ->name('core1.outpatient.updateFollowUp');
        
    Route::middleware('role:admin,doctor,nurse,head_nurse,patient')->group(function () {
        Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('core1.medical-records.index');
        Route::get('/medical-records/{patient}', [MedicalRecordController::class, 'show'])->name('core1.medical-records.show');
    });
    
    Route::middleware('role:admin,billing,patient')->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('core1.billing.index');
        Route::get('/billing/{bill}', [BillingController::class, 'show'])->name('core1.billing.show');
    });
    
    Route::middleware('role:admin')->group(function () {
        Route::get('/staff', [StaffManagementController::class, 'index'])->name('core1.staff.index');
        Route::get('/staff/create', [StaffManagementController::class, 'create'])->name('core1.staff.create');
        Route::post('/staff', [StaffManagementController::class, 'store'])->name('core1.staff.store');
    });
    
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportsController::class, 'index'])->name('core1.reports.index');
    });
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('core1.settings.index');
});
