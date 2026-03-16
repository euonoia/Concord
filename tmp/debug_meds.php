<?php
use App\Models\user\Core\core1\Patient;
use App\Models\user\Core\core1\Encounter;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$patient = Patient::where('mrn', '!=', '')->first();
if (!$patient) {
    echo "No patient found";
    exit;
}

echo "Patient: " . $patient->first_name . " " . $patient->last_name . "\n";
foreach ($patient->encounters as $e) {
    echo "Encounter ID: " . $e->id . " Type: " . $e->type . " Status: " . $e->status . "\n";
    if ($e->admission) {
        echo "  Admission Status: " . $e->admission->status . " Discharge: " . ($e->admission->discharge_date ?? 'N/A') . "\n";
    }
    foreach ($e->prescriptions as $p) {
        echo "    Prescription: " . $p->medication . " Admins: " . $p->administrations->count() . "\n";
    }
}
