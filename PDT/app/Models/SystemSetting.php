<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static ?array $memoryCache = null;

    public static function allSettings()
    {
        if (static::$memoryCache !== null) {
            return static::$memoryCache;
        }

        try {
            $tmpFile = '/tmp/portal_sys_settings.json';
            if (file_exists($tmpFile) && (time() - filemtime($tmpFile) < 300)) {
                $cached = json_decode(file_get_contents($tmpFile), true);
                if (is_array($cached) && !empty($cached)) {
                    static::$memoryCache = $cached;
                    return $cached;
                }
            }

            $settings = static::pluck('value', 'key')->toArray();
            static::$memoryCache = $settings;
            @file_put_contents($tmpFile, json_encode($settings));
            return $settings;
        } catch (\Throwable $e) {
            return static::$memoryCache ?? [];
        }
    }

    public static function get($key, $default = null)
    {
        $settings = static::allSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function set($key, $value)
    {
        static::$memoryCache = null;
        @unlink('/tmp/portal_sys_settings.json');
        Cache::forget('portal_all_system_settings');
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
