<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function allSettings()
    {
        try {
            return Cache::remember('portal_all_system_settings', 300, function () {
                return static::pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function get($key, $default = null)
    {
        $settings = static::allSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function set($key, $value)
    {
        Cache::forget('portal_all_system_settings');
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
