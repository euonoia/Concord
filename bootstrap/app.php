<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::bind('patient', function ($value) {
                if (is_numeric($value)) {
                    $patient = \App\Models\core1\Patient::find($value);
                    if ($patient) return $patient;
                }
                return \App\Models\core1\Patient::where('patient_id', $value)->firstOrFail();
            });
        }
    )
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->trustProxies(at: '*'); 
       $middleware->alias([
           'role' => \App\Http\Middleware\Core1RoleMiddleware::class,
       ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
