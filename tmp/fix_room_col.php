<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::statement(
    "ALTER TABLE `room_assignments_core2` MODIFY COLUMN `room` VARCHAR(255) DEFAULT NULL"
);

echo "room column widened to VARCHAR(255)\n";
