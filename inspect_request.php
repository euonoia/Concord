<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$req = DB::table('payroll_ess_requests_hr4')->where('id', 150004)->first();
$hr2 = DB::table('payroll_request_hr2')->where('employee_id', $req->employee_id)->orderByDesc('created_at')->first();

print_r($req);
print_r($hr2);

$comp = DB::table('direct_compensations_hr4')->where('employee_id', $req->employee_id)->orderByDesc('month')->get();
foreach ($comp as $c) {
    print_r($c);
}
