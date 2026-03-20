<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\admin\Hr\hr2\Department;

// Mimic AppointmentController@create
$doctors = User::where('role_slug', 'doctor')->with('employee.department')->get();
$departments = Department::where('is_active', true)->get();

echo "--- DOCTOR COUNT: " . $doctors->count() . " ---\n";
foreach ($doctors as $doctor) {
    echo "ID: {$doctor->id} | Name: {$doctor->username} | Dept: " . ($doctor->employee->department_id ?? 'NULL') . "\n";
}

// Mimic the Blade rendering for one doctor
if ($doctors->count() > 0) {
    $doctor = $doctors->first();
    $dataDept = $doctor->employee->department_id ?? '';
    echo "\n--- BLADE RENDERING MOCK ---\n";
    echo "data-department=\"{$dataDept}\"\n";
    $label = ($doctor->employee && $doctor->employee->first_name) ? $doctor->employee->full_name : $doctor->username;
    $spec = ($doctor->employee && $doctor->employee->specialization) ? "({$doctor->employee->specialization})" : "";
    echo "Label Content: {$label} {$spec}\n";
}
