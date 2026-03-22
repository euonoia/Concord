<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\admin\Hr\hr3\Shift;

$emps = Employee::whereNotNull('specialization')->get();
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

foreach ($emps as $emp) {
    foreach ($days as $day) {
        Shift::updateOrCreate(
            ['employee_id' => $emp->employee_id, 'day_of_week' => $day],
            [
                'shift_name' => 'Morning Shift',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'is_active' => true
            ]
        );
    }
    echo "Seeded shifts for {$emp->first_name} {$emp->last_name} ({$emp->employee_id})\n";
}

echo "--- All specialized staff are now scheduled 8AM-5PM on Weekdays ---\n";
