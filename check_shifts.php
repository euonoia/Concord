<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\admin\Hr\hr3\Shift;

$user = User::where('username', 'dr_smith')->first();
if (!$user) {
    echo "User dr_smith not found.\n";
    exit;
}

$emp = Employee::where('user_id', $user->id)->first();
if (!$emp) {
    echo "Employee for user_id {$user->id} not found.\n";
    // Let's list all employees to see what's there
    $all = Employee::limit(5)->get();
    echo "All existing employees (sample):\n";
    foreach($all as $e) { echo "- {$e->employee_id} (user_id: {$e->user_id})\n"; }
    exit;
}

$shifts = Shift::where('employee_id', $emp->employee_id)->get();
echo "--- SHIFTS FOR {$user->username} (Employee ID: {$emp->employee_id}) ---\n";
echo "Count: " . $shifts->count() . "\n";

foreach ($shifts as $s) {
    echo "Day: {$s->day_of_week} | Start: {$s->start_time} | End: {$s->end_time} | Active: " . ($s->is_active ? 'Yes' : 'No') . "\n";
}
