<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\core1\Prescription;
use App\Models\core1\Encounter;
use App\Models\core1\Patient;
use App\Models\User;

class PharmacySyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test prescription sync from Core 1 to Core 2.
     */
    public function test_prescription_sync_to_core2()
    {
        // Setup
        $doctor = User::factory()->create(['role_slug' => 'doctor']);
        $patient = Patient::create(['name' => 'John Doe', 'mrn' => 'MRN123']);
        $encounter = Encounter::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'type' => 'OPD',
            'status' => 'Active'
        ]);

        $this->actingAs($doctor);

        // Action: Store prescription
        $response = $this->post(route('core1.outpatient.storePrescription'), [
            'encounter_id' => $encounter->id,
            'medication' => 'Paracetamol',
            'dosage' => '500mg',
            'instructions' => 'Twice daily',
            'duration' => '3 days'
        ]);

        $response->assertStatus(302);

        // Assert: Core 1 record exists and is synced
        $prescription = Prescription::first();
        $this->assertEquals('Synced', $prescription->status);
        $this->assertNotNull($prescription->core2_pharmacy_id);

        // Assert: Core 2 record exists
        $this->assertDatabaseHas('prescriptions_core2', [
            'core1_prescription_id' => $prescription->id,
            'status' => 'Received'
        ]);
    }

    /**
     * Test dispensing sync from Core 2 back to Core 1.
     */
    public function test_dispensing_sync_back_to_core1()
    {
        // Setup
        $prescription = Prescription::create([
            'encounter_id' => 1,
            'medication' => 'Paracetamol',
            'dosage' => '500mg',
            'status' => 'Synced',
            'core2_pharmacy_id' => 1
        ]);
        
        $pharmacyOrder = \App\Models\core2\Prescription::create([
            'core1_prescription_id' => $prescription->id,
            'status' => 'Received'
        ]);
        
        $prescription->update(['core2_pharmacy_id' => $pharmacyOrder->id]);

        $pharmacist = User::factory()->create(['role_slug' => 'staff']);
        $this->actingAs($pharmacist);

        // Action: Dispense medication
        $response = $this->post(route('core2.pharmacy.prescription.dispense', $pharmacyOrder->id));

        $response->assertStatus(302);

        // Assert: Core 2 status updated
        $this->assertEquals('Dispensed', $pharmacyOrder->fresh()->status);

        // Assert: Core 1 status updated
        $this->assertEquals('Dispensed', $prescription->fresh()->status);
    }
}
