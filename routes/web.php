<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('onboarding');
});

Route::resource('tasks', TaskController::class);

Route::get('/_ssl-check', function () {
    return [
        'ca_exists' => file_exists('/etc/secrets/isrgrootx1.pem'),
        'ca_readable' => is_readable('/etc/secrets/isrgrootx1.pem'),
        'openssl' => extension_loaded('openssl'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'ca_hash' => file_exists('/etc/secrets/isrgrootx1.pem')
            ? hash_file('sha256', '/etc/secrets/isrgrootx1.pem')
            : null,
    ];
});
