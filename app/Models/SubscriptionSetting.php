<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SubscriptionSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'label', 'description'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) return $default;

        return match($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($setting->value, true) ?? $default,
            default   => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $storedValue = ($type === 'json') ? json_encode($value) : (string) $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type]
        );
    }

    /**
     * Get trial days (default: 14).
     */
    public static function trialDays(): int
    {
        return (int) static::get('trial_days', 14);
    }

    /**
     * Get limited features list (sections locked for non-subscribers).
     */
    public static function limitedFeatures(): array
    {
        return static::get('limited_features', [
            'booking',
            'inventory',
            'appointments',
        ]) ?? [];
    }

    /**
     * Get the notice message shown to users without active plan.
     */
    public static function noticeMessage(): string
    {
        return static::get(
            'notice_message',
            '⚠️ Your subscription is inactive. Some features are limited. Please select a plan to unlock full access.'
        );
    }
}
