<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClassPermission
{
    public function handle(Request $request, Closure $next)
    {
        $classParam = strtolower($request->route('class')); // Example: 7A → 7a
        $permissionName = 'class-' . $classParam; // class-7a

        $user = Auth::user();

        // Admin always allowed
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check if user has permission for this class
        if ($user->can($permissionName)) {
            return $next($request);
        }

        // Otherwise block access
        abort(403, 'You do not have permission to access this class.');
    }
}
