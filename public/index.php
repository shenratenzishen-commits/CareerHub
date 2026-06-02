<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Set /tmp storage BEFORE anything else loads
if (getenv('VERCEL') || isset($_SERVER['VERCEL'])) {
    $storagePath = '/tmp/storage';
    putenv('APP_STORAGE=' . $storagePath);
    $_SERVER['APP_STORAGE'] = $storagePath;
    $_ENV['APP_STORAGE']    = $storagePath;

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

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->handleRequest(Request::capture());