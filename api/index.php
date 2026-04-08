<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Redirect writable directories to /tmp on Vercel (read-only filesystem)
$storagePath = '/tmp/storage';
if (!is_dir($storagePath)) {
    $dirs = [
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
        $storagePath . '/app/public',
    ];
    foreach ($dirs as $dir) {
        mkdir($dir, 0755, true);
    }
}

$app->useStoragePath($storagePath);

// Copy SQLite database to /tmp if it exists in the project
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbDest   = '/tmp/database.sqlite';
if (file_exists($dbSource) && !file_exists($dbDest)) {
    copy($dbSource, $dbDest);
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
