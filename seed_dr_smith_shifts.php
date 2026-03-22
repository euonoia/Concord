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
    echo "Employee for dr_smith not found.\n";
    exit;
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
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
    echo "Added/Updated shift for {$day}.\n";
}

echo "--- dr_smith is now scheduled 8AM-5PM on Weekdays ---\n";
