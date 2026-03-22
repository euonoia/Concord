<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get the doctor's user ID
$doctor = DB::table('users')->where('role_slug', 'doctor')->first();

if (!$doctor) {
    echo "No doctor found in users table.\n";
    exit;
}

// Check if employee already exists
$existing = DB::table('employees')->where('user_id', $doctor->id)->first();
if ($existing) {
    echo "Employee record already exists for {$doctor->username} (dept: {$existing->department_id}).\n";
    exit;
}

// Get a valid specialization for MED-GEN
$spec = DB::table('department_specializations_hr2')
    ->where('dept_code', 'MED-GEN')
    ->where('specialization_name', 'General Internal Medicine')
    ->first();

if (!$spec) {
    echo "No valid specialization found for MED-GEN.\n";
    exit;
}

echo "Using specialization: {$spec->specialization_name}\n";

// Insert using raw DB to avoid any model event issues
$nameParts = explode(' ', $doctor->name ?? $doctor->username);

DB::table('employees')->insert([
    'user_id' => $doctor->id,
    'employee_id' => 'EMP-' . strtoupper(substr(uniqid(), -6)),
    'first_name' => $nameParts[0] ?? 'Doctor',
    'last_name' => $nameParts[1] ?? 'Smith',
    'department_id' => 'MED-GEN',
    'specialization' => $spec->specialization_name,
    'is_on_duty' => 1,
    'hire_date' => date('Y-m-d'),
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Employee record created successfully for {$doctor->username}!\n";

// Verify
$emp = DB::table('employees')->where('user_id', $doctor->id)->first();
echo "Verified: Employee ID={$emp->employee_id}, Dept={$emp->department_id}, Spec={$emp->specialization}\n";
