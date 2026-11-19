<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Site-wide details edited on the super admin Settings page and shown in the
 * footer, contact page and legal pages. Cached; the cache is cleared on save.
 */
class SiteSettings
{
    public const CACHE_KEY = 'site-settings';

    public static function defaults(): array
    {
        return [
            'site_title' => config('app.name', 'PluggedIn'),
            'brand_blurb' => 'A safer, smarter place for campus communities to buy, sell, repair, and get more from their tech.',
            'contact_email' => 'support@pluggedin.com',
            'contact_phone' => '+233 594 042 521',
            'contact_address' => 'Accra, Ghana',
            'business_hours' => "Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 10:00 AM - 4:00 PM\nSunday: Closed",
            'whatsapp_number' => '',
            'facebook_url' => '',
            'x_url' => '',
            'instagram_url' => '',
        ];
    }

    public static function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $saved = Schema::hasTable('settings')
                ? Setting::whereIn('key', array_keys(self::defaults()))->pluck('value', 'key')->filter(fn ($v) => filled($v))->all()
                : [];

            return array_merge(self::defaults(), $saved);
        });
    }

    public static function get(string $key): ?string
    {
        return self::all()[$key] ?? null;
    }

    public static function save(array $values): void
    {
        foreach ($values as $key => $value) {
            if (array_key_exists($key, self::defaults())) {
                Setting::set($key, $value);
            }
        }

        Cache::forget(self::CACHE_KEY);
    }
}
