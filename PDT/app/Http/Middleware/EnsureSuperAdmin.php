<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isSuperAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'លោកអ្នកមិនមានសិទ្ធិចូលមើលកំណត់ហេតុប្រព័ន្ធនេះទេ (ទាមទារសិទ្ធិជា Super Admin)',
                ], 403);
            }

            abort(403, 'លោកអ្នកមិនមានសិទ្ធិចូលមើលកំណត់ហេតុប្រព័ន្ធនេះទេ (Super Admin Only)');
        }

        return $next($request);
    }
}

