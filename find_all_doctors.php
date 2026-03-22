<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;

echo "--- ALL EMPLOYEES AND THEIR USER ROLES ---\n";
$emps = Employee::all();
foreach ($emps as $e) {
    if (!$e->user_id) {
        $role = "NO USER LINK";
    } else {
        $u = User::find($e->user_id);
        $role = $u ? $u->role_slug : "USER NOT FOUND ({$e->user_id})";
    }
    echo "Employee: {$e->employee_id} ({$e->first_name} {$e->last_name}) | Spec: " . ($e->specialization ?? 'NONE') . " | Role: {$role}\n";
}
