<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'public/*',
            'leave-request*',
            'teacher-leave-request*',
            'register-*',
            'register/*',
        ]);
        $middleware->alias([
            'protect.route' => \App\Http\Middleware\ProtectRoute::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

if (isset($_ENV['VERCEL']) || getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        @mkdir($storagePath, 0777, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;
