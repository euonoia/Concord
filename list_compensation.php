<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\admin\Hr\hr4\DirectCompensation;

$comps = DirectCompensation::where('employee_id','GEN-0001')->orderByDesc('month')->get();
foreach ($comps as $c) {
    echo $c->month . ' - base=' . $c->base_salary . ' bonus=' . $c->bonus . ' training=' . ($c->training_reward ?? 0) . ' total=' . $c->total_compensation . "\n";
}
