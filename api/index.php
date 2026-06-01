<?php

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Set correct paths for Vercel serverless
$_ENV['APP_BASE_PATH'] = dirname(__DIR__);

// Fix storage path for Vercel (read-only filesystem)
if (!isset($_ENV['APP_STORAGE_PATH'])) {
    $_ENV['APP_STORAGE_PATH'] = '/tmp/storage';
}

// Create writable storage dirs in /tmp
$dirs = [
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/storage/app',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

require __DIR__ . '/../public/index.php';
