<?php
 
if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}
 
// ── 1. Create all required storage directories in /tmp ──────────
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
 
// ── 2. Copy bootstrap cache files to /tmp ───────────────────────
// Vercel filesystem is read-only except /tmp.
// Laravel needs to read/write bootstrap cache, so we copy
// any existing .php cache files into /tmp on every cold start.
$bootstrapCache = dirname(__DIR__) . '/bootstrap/cache';
$tmpBootstrap   = '/tmp/storage/bootstrap/cache';
 
if (!is_dir($tmpBootstrap)) {
    mkdir($tmpBootstrap, 0777, true);
}
 
foreach (glob($bootstrapCache . '/*.php') as $file) {
    $dest = $tmpBootstrap . '/' . basename($file);
    if (!file_exists($dest)) {
        copy($file, $dest);
    }
}
 
// ── 3. Point Laravel to /tmp storage ────────────────────────────
$_SERVER['APP_STORAGE'] = $storagePath;
putenv('APP_STORAGE=' . $storagePath);
 
// ── 4. Boot Laravel ─────────────────────────────────────────────
require __DIR__ . '/../public/index.php';
 