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
        $middleware->web(prepend: [
            \App\Http\Middleware\SocialBotPreviewMiddleware::class,
        ]);
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'logout',
            'api/*',
            'public/*',
            'leave-request',
            'leave-request/*',
            'teacher-leave-request',
            'teacher-leave-request/*',
            'register-teacher',
            'register-teacher/*',
            'register-parent',
            'register-parent/*',
        ]);
        $middleware->alias([
            'protect.route' => \App\Http\Middleware\ProtectRoute::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 429) {
                $headers = $e->getHeaders();
                $retryAfter = (int) ($headers['Retry-After'] ?? 300);
                if ($retryAfter <= 0 || $retryAfter > 300) $retryAfter = 300;
                $minutes = ceil($retryAfter / 60);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'អ្នកបានព្យាយាមចូលខុស ៥ ដងហើយ។ សូមរង់ចាំ (' . $retryAfter . ' វិនាទី) ទៀត ទើបអាចសាកល្បងម្តងទៀត។',
                        'retry_after' => $retryAfter
                    ], 429);
                }

                return redirect()->route('login')
                    ->withInput($request->only('email'))
                    ->with('lockout_seconds', $retryAfter)
                    ->with('error', 'អ្នកបានព្យាយាមចូលខុស ៥ ដងហើយ។ សូមរង់ចាំ ' . $retryAfter . ' វិនាទី (ប្រហែល ' . $minutes . ' នាទី) ទៀត ទើបអាចសាកល្បងម្តងទៀត។');
            }

            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'CSRF token mismatch. Please refresh.'], 419);
                }
                if ($request->is('login') || $request->is('/')) {
                    return redirect()->route('login')->with('error', 'សម័យការរបស់អ្នកបានផុតកំណត់ សូមព្យាយាមចូលម្តងទៀត។');
                }
                return redirect()->back()->with('error', 'សម័យការរបស់អ្នកបានផុតកំណត់ សូមព្យាយាមម្តងទៀត។');
            }
        });
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh.'], 419);
            }
            if ($request->is('login') || $request->is('/')) {
                return redirect()->route('login')->with('error', 'សម័យការរបស់អ្នកបានផុតកំណត់ សូមព្យាយាមចូលម្តងទៀត។');
            }
            return redirect()->back()->with('error', 'សម័យការរបស់អ្នកបានផុតកំណត់ សូមព្យាយាមម្តងទៀត។');
        });
    })->create();

if (isset($_ENV['VERCEL']) || getenv('VERCEL')) {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath)) {
        @mkdir($storagePath, 0777, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;
