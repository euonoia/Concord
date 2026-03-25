<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\user\Core\core1\Patient;
use App\Policies\PatientPolicy;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\Prescription;
use App\Models\core1\LabOrder;
use App\Models\core1\SurgeryOrder;
use App\Models\core1\DietOrder;
use App\Observers\core1\MedicalRecordSyncObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Patient::class, PatientPolicy::class);

        // Medical Record Synchronization
        Triage::observe(MedicalRecordSyncObserver::class);
        Consultation::observe(MedicalRecordSyncObserver::class);
        Prescription::observe(MedicalRecordSyncObserver::class);
        LabOrder::observe(MedicalRecordSyncObserver::class);
        SurgeryOrder::observe(MedicalRecordSyncObserver::class);
        DietOrder::observe(MedicalRecordSyncObserver::class);
    }
}
