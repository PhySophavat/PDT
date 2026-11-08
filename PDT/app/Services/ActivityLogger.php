<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Sensitive fields that should never be stored in plain text in logs.
     */
    protected static array $sensitiveFields = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'secret',
        'api_key',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Log a system activity
     */
    public static function log(
        string $action,
        string $module,
        string|int|null $targetId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        mixed $user = null
    ): ?ActivityLog {
        try {
            $user = $user ?: Auth::user();

            $userName = $user?->name ?? 'ភ្ញៀវ (Guest)';
            $userRole = self::determineKhmerRole($user);

            $request = request();
            $userAgent = $request ? $request->userAgent() : null;
            $ipAddress = $request ? $request->ip() : null;
            $deviceName = self::parseDeviceName($userAgent);

            $payload = null;
            if ($oldValues !== null || $newValues !== null) {
                $payload = [
                    'old_values' => self::sanitizePayload($oldValues ?? []),
                    'new_values' => self::sanitizePayload($newValues ?? []),
                ];
            }

            return ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $userName,
                'user_role' => $userRole,
                'action' => strtoupper(trim($action)),
                'module' => $module,
                'target_id' => $targetId !== null ? (string) $targetId : null,
                'description' => $description,
                'device_name' => $deviceName,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'payload' => $payload,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Activity logging should never disrupt user transactions
            report($e);
            return null;
        }
    }

    /**
     * Parse User-Agent into clean "OS - Browser" (e.g. "Windows 11 - Chrome", "iPhone - Safari")
     */
    public static function parseDeviceName(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown Device';
        }

        $os = 'Unknown OS';
        $ua = $userAgent;

        // Detect OS
        if (preg_match('/Windows NT 10\.0/i', $ua)) {
            // Windows 11 uses NT 10.0 with specific build strings or client hints, fallback Windows 10/11
            $os = 'Windows 11/10';
        } elseif (preg_match('/Windows NT 6\.3/i', $ua)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6\.1/i', $ua)) {
            $os = 'Windows 7';
        } elseif (preg_match('/iPhone/i', $ua)) {
            $os = 'iPhone (iOS)';
        } elseif (preg_match('/iPad/i', $ua)) {
            $os = 'iPad (iPadOS)';
        } elseif (preg_match('/Android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux';
        }

        // Detect Browser
        $browser = 'Browser';
        if (preg_match('/Edg\/([0-9.]+)/i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome\/([0-9.]+)/i', $ua) && !preg_match('/Edg/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari\/([0-9.]+)/i', $ua) && !preg_match('/Chrome/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox\/([0-9.]+)/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/OPR\/|Opera\//i', $ua)) {
            $browser = 'Opera';
        }

        return "{$os} - {$browser}";
    }

    /**
     * Map user role to readable Khmer designation
     */
    public static function determineKhmerRole(mixed $user): string
    {
        if (!$user) {
            return 'ភ្ញៀវ (Guest)';
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return 'Super Admin';
        }

        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('super_admin')) {
                return 'Super Admin';
            }
            if ($user->hasRole('admin')) {
                return 'Admin (អ្នកគ្រប់គ្រង)';
            }
            if ($user->hasRole('teacher')) {
                return 'គ្រូបង្រៀន (Teacher)';
            }
            if ($user->hasRole('student')) {
                return 'សិស្ស (Student)';
            }
        }

        $rawRole = strtolower($user->role ?? '');
        return match ($rawRole) {
            'super_admin', 'superadmin' => 'Super Admin',
            'admin' => 'Admin (អ្នកគ្រប់គ្រង)',
            'teacher' => 'គ្រូបង្រៀន (Teacher)',
            'student' => 'សិស្ស (Student)',
            default => $user->id === 1 ? 'Super Admin' : 'អ្នកប្រើប្រាស់ (User)',
        };
    }

    /**
     * Filter out password and sensitive keys from arrays
     */
    public static function sanitizePayload(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $val) {
            if (in_array(strtolower($key), self::$sensitiveFields, true)) {
                $sanitized[$key] = '********';
            } elseif (is_array($val)) {
                $sanitized[$key] = self::sanitizePayload($val);
            } else {
                $sanitized[$key] = $val;
            }
        }
        return $sanitized;
    }
}

