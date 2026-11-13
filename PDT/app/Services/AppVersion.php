<?php

namespace App\Services;

class AppVersion
{
    private static ?string $cachedVersion = null;

    /**
     * Single source of truth for application deployment version.
     * Matches between layouts/app.blade.php and /api/app-version.
     * Changes ONLY when a new commit/build is deployed.
     */
    public static function get(): string
    {
        if (self::$cachedVersion !== null) {
            return self::$cachedVersion;
        }

        // 1. Official Vercel Git commit SHA
        $gitCommit = env('VERCEL_GIT_COMMIT_SHA');
        if (!empty($gitCommit)) {
            return self::$cachedVersion = substr((string) $gitCommit, 0, 12);
        }

        // 2. Hash of Vite manifest.json (generated only during npm run build on deploy)
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            return self::$cachedVersion = substr(md5_file($manifestPath), 0, 16);
        }

        return self::$cachedVersion = 'v1';
    }
}
