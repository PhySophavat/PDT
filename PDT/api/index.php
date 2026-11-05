<?php

// 0. Set CORS headers so API and assets can be accessed across domains
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Prepare writable /tmp directories on Vercel serverless environment
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Warm up package discovery caches for ultra-fast serverless boots
if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php') && !file_exists('/tmp/bootstrap/cache/packages.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/packages.php', '/tmp/bootstrap/cache/packages.php');
}
if (file_exists(__DIR__ . '/../bootstrap/cache/services.php') && !file_exists('/tmp/bootstrap/cache/services.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/services.php', '/tmp/bootstrap/cache/services.php');
}

// 2. Set environment variables for serverless runtime
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';

putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';

putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';

putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';

putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

// Fallback APP_KEY if not configured in Vercel project settings
if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:9EvsufK6IKWmF5ATdQATWq+eqjUFEp/rO/6sshb3MtI=');
    $_ENV['APP_KEY'] = 'base64:9EvsufK6IKWmF5ATdQATWq+eqjUFEp/rO/6sshb3MtI=';
}

if (!getenv('SESSION_DRIVER') || getenv('SESSION_DRIVER') === 'file') {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

if (!getenv('CACHE_STORE')) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

if (!getenv('LOG_CHANNEL') || getenv('LOG_CHANNEL') === 'stack') {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
}

// 3. Database configuration for Vercel
// If an external MySQL DB is configured in Vercel Environment Variables, use MySQL.
// Otherwise, copy pre-seeded database/database.sqlite to /tmp/database.sqlite for instant out-of-the-box working!
if (!getenv('DB_CONNECTION') || getenv('DB_CONNECTION') === 'sqlite') {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';

    $sqliteDest = '/tmp/database.sqlite';
    $sqliteSrc = __DIR__ . '/../database/database.sqlite';

    if (file_exists($sqliteSrc)) {
        if (!file_exists($sqliteDest) || filesize($sqliteDest) !== filesize($sqliteSrc)) {
            @copy($sqliteSrc, $sqliteDest);
        }
    } elseif (!file_exists($sqliteDest)) {
        @touch($sqliteDest);
    }

    putenv("DB_SQLITE_PATH={$sqliteDest}");
    $_ENV['DB_SQLITE_PATH'] = $sqliteDest;
    $_SERVER['DB_SQLITE_PATH'] = $sqliteDest;

    putenv("DB_DATABASE={$sqliteDest}");
    $_ENV['DB_DATABASE'] = $sqliteDest;
    $_SERVER['DB_DATABASE'] = $sqliteDest;
}

// Show detailed errors on Vercel so we know if database or any specific call fails
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';

// 4. Forward to public index.php
require __DIR__ . '/../public/index.php';
