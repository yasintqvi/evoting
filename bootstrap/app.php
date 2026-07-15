<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Silence PHP 8.5 deprecation notices emitted while Laravel 11 merges its base
// config/database.php from vendor (which still references the deprecated
// PDO::MYSQL_ATTR_SSL_CA constant). This runs before LoadConfiguration; Laravel's
// HandleExceptions bootstrapper restores full error reporting immediately after.
error_reporting(error_reporting() & ~E_DEPRECATED);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'voter.only' => \App\Http\Middleware\VoterOnlyAccess::class,
        ]);

        // Convert Persian/Arabic digits to English in all incoming requests.
        $middleware->append(\App\Http\Middleware\ConvertPersianNumbers::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
