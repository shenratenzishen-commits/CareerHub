<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Set storage path BEFORE Application is created
$basePath    = dirname(__DIR__);
$storagePath = getenv('APP_STORAGE') ?: $_SERVER['APP_STORAGE'] ?? $basePath . '/storage';

// Ensure /tmp dirs exist (Vercel only)
if (str_starts_with($storagePath, '/tmp')) {
    foreach ([
        $storagePath . '/app/public',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/testing',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
    ] as $dir) {
        is_dir($dir) || mkdir($dir, 0777, true);
    }
}

$app = Application::configure(basePath: $basePath)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Override storage path immediately after create()
$app->useStoragePath($storagePath);

// Bind view paths explicitly to /tmp
$app->instance('path.storage', $storagePath);

return $app;