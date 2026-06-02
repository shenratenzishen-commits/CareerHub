<?php

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

$storagePath = '/tmp/storage';

// Create dirs
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

// Set BEFORE bootstrap/app.php loads
putenv('APP_STORAGE=' . $storagePath);
$_SERVER['APP_STORAGE'] = $storagePath;
$_ENV['APP_STORAGE']    = $storagePath;

require __DIR__ . '/../public/index.php';