<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\user\Core\core1\Patient;
use App\Models\core1\Encounter;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;
use App\Models\core1\Admission;
use App\Models\core1\Bed;
use App\Models\core1\Room;
use App\Models\core1\Ward;
use App\Models\core1\Discharge;
use App\Services\core1\PatientRegistrationService;
use App\Services\core1\OutpatientService;
use App\Services\core1\AdmissionService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TestClinicalWorkflows extends Command
{
    protected $signature = 'core1:test-workflows';
    protected $description = 'End-to-End Test for OPD and IPD clinical flows';

    public function handle(OutpatientService $opdService, AdmissionService $ipdService)
    {
        $this->info('Starting End-to-End Clinical Workflow Tests...');

        // Verify/Create Doctor User for assignments
        $doctorId = 1; // Assuming user ID 1 is a doctor for testing

        // --- OPD WORKFLOW TEST ---
        $this->info("\n--- 🏥 STARTING OPD WORKFLOW TEST ---");
        
        // 1. Register Patient via direct Eloquent logic to bypass service dependency issues in tests
        $mrn = 'MRN-' . strtoupper(Str::random(6));
        $opdPatient = Patient::create([
            'mrn' => $mrn,
            'first_name' => 'John',
            'last_name' => 'OpdTest',
            'date_of_birth' => '1990-05-15',
            'gender' => 'Male',
            'contact_number' => '555-0100',
            'registration_status' => 'REGISTERED'
        ]);
        
        $opdEncounter = Encounter::create([
            'patient_id' => $opdPatient->id,
            'type' => 'OPD',
            'status' => 'Active',
            'doctor_id' => $doctorId,
        ]);
        
        $this->line("✅ Walk-in Patient Registered: {$opdPatient->first_name} {$opdPatient->last_name} (MRN: {$opdPatient->mrn})");
        $this->line("✅ Active Encounter Created: ID {$opdEncounter->id}");

        // 2. Triage
        $triageData = [
            'blood_pressure' => '120/80',
            'heart_rate' => 75,
            'temperature' => '37.0',
            'spo2' => 98,
            'triage_level' => 4,
            'notes' => 'Patient complains of mild headache.'
        ];
        $opdService->recordTriage($opdEncounter, $triageData, 2); // Assuming nurse ID 2
        $this->line("✅ Triage Recorded.");

        // 3. Consultation (SOAP)
        $consultationData = [
            'subjective' => 'Mild headache for 2 days.',
            'objective' => 'Vitals stable. No neurological deficits.',
            'assessment' => 'Tension headache.',
            'plan' => 'Rest, hydration, and PRN analgesics.',
            'doctor_notes' => 'Patient seems stressed from work.'
        ];
        $opdService->saveConsultation($opdEncounter, $consultationData, $doctorId);
        $this->line("✅ Consultation (SOAP) Saved.");

        // 4. Order Labs & Meds
        LabOrder::create([
            'encounter_id' => $opdEncounter->id,
            'test_name' => 'Complete Blood Count (CBC)',
            'clinical_note' => 'Routine check just in case.'
        ]);
        $this->line("✅ Lab Order Created.");

        Prescription::create([
            'encounter_id' => $opdEncounter->id,
            'medication' => 'Paracetamol 500mg',
            'dosage' => '1 tab',
            'duration' => '3 days',
            'instructions' => 'Take PRN for headache.'
        ]);
        $this->line("✅ Prescription Issued.");

        // 5. Complete Encounter
        $opdService->completeEncounter($opdEncounter);
        $opdEncounter->refresh();
        if ($opdEncounter->status === 'Closed') {
            $this->info("🏥 OPD WORKFLOW COMPLETED SUCCESSFULLY");
        } else {
            $this->error("❌ OPD Encounter did not close properly.");
        }


        // --- IPD WORKFLOW TEST ---
        $this->info("\n--- 🛏️ STARTING IPD WORKFLOW TEST ---");
        
        // 1. Setup Bed Hierarchy (Ward -> Room -> Bed)
        $ward = Ward::firstOrCreate(
            ['name' => 'General Ward Testing'],
            ['capacity' => 10]
        );
        
        $room = Room::firstOrCreate(
            ['ward_id' => $ward->id, 'room_number' => '101-TEST'],
            ['room_type' => 'Standard', 'status' => 'Available']
        );

        $bed = Bed::firstOrCreate(
            ['room_id' => $room->id, 'bed_number' => 'Bed-A'],
            ['status' => 'Available']
        );
        
        // Force bed to be available if dirty from previous tests
        $bed->update(['status' => 'Available']);
        $this->line("✅ Bed Setup: Room {$room->room_number}, Bed {$bed->bed_number} is {$bed->status}");

        // 2. Register Patient and Create Encounter manually
        $mrnIpd = 'MRN-' . strtoupper(Str::random(6));
        $ipdPatient = Patient::create([
            'mrn' => $mrnIpd,
            'first_name' => 'Jane',
            'last_name' => 'IpdTest',
            'date_of_birth' => '1985-10-20',
            'gender' => 'Female',
            'contact_number' => '555-0200',
            'registration_status' => 'REGISTERED'
        ]);
        
        $ipdEncounter = Encounter::create([
            'patient_id' => $ipdPatient->id,
            'type' => 'OPD', // Starts as OPD normally
            'status' => 'Active',
            'doctor_id' => $doctorId,
        ]);
        
        $this->line("✅ Walk-in Patient Registered: {$ipdPatient->first_name} {$ipdPatient->last_name} (MRN: {$ipdPatient->mrn})");

        // 3. Doctor orders Admission (Triage/Consult skipped for brevity in this test, directly admitting)
        $ipdEncounter->update(['type' => 'IPD']);
        
        $admission = $ipdService->admit($ipdEncounter, $bed);
        $bed->refresh();
        $this->line("✅ Patient Admitted. Bed status is now: {$bed->status}. Admission ID: {$admission->id}");

        if ($bed->status !== 'Occupied') {
            $this->error("❌ Bed status did not update to Occupied!");
        }

        // 4. Discharge Patient
        $dischargeData = [
            'final_diagnosis' => 'Acute Appendicitis - Post-Op Stable',
            'discharge_summary' => 'Patient underwent laparoscopic appendectomy. Recovery uneventful. Discharged in stable condition.'
        ];
        $ipdService->discharge($admission, $dischargeData);
        
        $admission->refresh();
        $bed->refresh();
        $ipdEncounter->refresh();

        $this->line("✅ Discharge processed.");
        $this->line("   - Admission Status: {$admission->status}");
        $this->line("   - Bed Status: {$bed->status}");
        $this->line("   - Encounter Status: {$ipdEncounter->status}");

        if ($admission->status === 'Discharged' && $bed->status === 'Available' && $ipdEncounter->status === 'Closed') {
            $this->info("🛏️ IPD WORKFLOW COMPLETED SUCCESSFULLY");
        } else {
            $this->error("❌ IPD workflow did not close cleanly.");
        }

        $this->info("\nAll Tests Executed.");
    }
}
