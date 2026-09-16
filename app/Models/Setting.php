<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    public $timestamps = false;

    protected $dates = [
        'updated_at',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function set($key, $value)
    {
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        } elseif ($value !== null) {
            $value = (string) $value;
        }

        $setting = static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );

        if (class_exists(\App\Helpers\CacheHelper::class)) {
            \App\Helpers\CacheHelper::clearSettingsCache();
        }

        return $setting;
    }
}
