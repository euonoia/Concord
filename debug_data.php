<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Exactly replicate the controller query
$doctors = User::where('role_slug', 'doctor')->with('employee.department')->get();

echo "Found " . $doctors->count() . " doctors\n\n";

foreach ($doctors as $doctor) {
    echo "Doctor: {$doctor->name} (ID: {$doctor->id})\n";
    echo "  Has employee: " . ($doctor->employee ? 'YES' : 'NO') . "\n";
    
    if ($doctor->employee) {
        echo "  Employee department_id: '{$doctor->employee->department_id}'\n";
        echo "  Employee specialization: '{$doctor->employee->specialization}'\n";
        
        // This is what the Blade renders as data-department
        $dataDept = $doctor->employee->department_id ?? '';
        echo "  data-department would be: '{$dataDept}'\n";
        
        if ($doctor->employee->department) {
            echo "  Department name: '{$doctor->employee->department->name}'\n";
        } else {
            echo "  Department: RELATIONSHIP RETURNS NULL\n";
        }
    }
}
