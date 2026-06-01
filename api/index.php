<?php

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Create all required storage directories in /tmp
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/app/public',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Copy cached config/routes if they exist
$bootstrapCache = dirname(__DIR__) . '/bootstrap/cache';
$tmpCache       = $storagePath . '/bootstrap/cache';
if (!is_dir($tmpCache)) {
    mkdir($tmpCache, 0777, true);
}

// Point Laravel to /tmp storage
$_SERVER['APP_STORAGE'] = $storagePath;
putenv('APP_STORAGE=' . $storagePath);

require __DIR__ . '/../public/index.php';
