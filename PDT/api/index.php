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
    '/tmp/storage/certs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Copy SSL CA certificate to /tmp for TiDB Cloud MySQL connection
$caSrc = __DIR__ . '/../storage/certs/ca.pem';
$caDest = '/tmp/storage/certs/ca.pem';
if (file_exists($caSrc) && !file_exists($caDest)) {
    @copy($caSrc, $caDest);
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
// Production permanently stores data in TiDB Cloud MySQL.
$dbConn = getenv('DB_CONNECTION');
if (!$dbConn || $dbConn === 'mysql') {
    putenv('DB_CONNECTION=mysql');
    $_ENV['DB_CONNECTION'] = 'mysql';

    if (!getenv('DB_HOST')) {
        putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
        $_ENV['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
    }
    if (!getenv('DB_PORT')) {
        putenv('DB_PORT=4000');
        $_ENV['DB_PORT'] = '4000';
    }
    if (!getenv('DB_DATABASE')) {
        putenv('DB_DATABASE=pdt');
        $_ENV['DB_DATABASE'] = 'pdt';
    }
    if (!getenv('DB_USERNAME')) {
        putenv('DB_USERNAME=3mw5PACCeLp6Wb8.root');
        $_ENV['DB_USERNAME'] = '3mw5PACCeLp6Wb8.root';
    }
    if (!getenv('DB_PASSWORD')) {
        putenv('DB_PASSWORD=H5GamPQkivCPJqR9');
        $_ENV['DB_PASSWORD'] = 'H5GamPQkivCPJqR9';
    }
    if (!getenv('DB_SSL')) {
        putenv('DB_SSL=true');
        $_ENV['DB_SSL'] = 'true';
    }
    if (!getenv('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        putenv('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false');
        $_ENV['MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'] = 'false';
    }
    if (file_exists($caDest)) {
        putenv("MYSQL_ATTR_SSL_CA={$caDest}");
        $_ENV['MYSQL_ATTR_SSL_CA'] = $caDest;
    }
} elseif ($dbConn === 'sqlite') {
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
