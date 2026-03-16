<?php

use App\Models\core1\Encounter;
use App\Models\core1\Triage;
use App\Models\core1\Prescription;
use App\Models\core1\MedicationAdministration;
use App\Models\User;

// 1. Verify Triage captured created_by
$triage = Triage::latest()->first();
if ($triage) {
    echo "Triage ID: " . $triage->id . "\n";
    echo "Created By: " . ($triage->created_by ?? 'NULL') . "\n";
    echo "Creator Name: " . ($triage->creator->name ?? 'N/A') . "\n";
} else {
    echo "No triage record found.\n";
}

// 2. Verify Medication Administration relation
$rx = Prescription::latest()->first();
if ($rx) {
    echo "Prescription ID: " . $rx->id . "\n";
    $admin = MedicationAdministration::create([
        'prescription_id' => $rx->id,
        'administered_by' => User::first()->id,
        'administered_at' => now(),
    ]);
    echo "Admin Record ID: " . $admin->id . "\n";
    echo "Admin Name: " . ($admin->administrator->name ?? 'N/A') . "\n";
    
    $rx_with_admin = Prescription::with('administrations.administrator')->find($rx->id);
    echo "Administrations count: " . $rx_with_admin->administrations->count() . "\n";
} else {
    echo "No prescription record found.\n";
}
