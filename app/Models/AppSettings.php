<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSettings extends Model
{
    protected $fillable = [
        'maintenance_mode',
        'maintenance_message',
        'force_update_required',
        'minimum_version',
        'latest_version',
        'update_message',
        'update_ios_url',
        'update_android_url',
        'show_announcement',
        'announcement_title',
        'announcement_message',
        'announcement_type',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode'      => 'boolean',
            'force_update_required' => 'boolean',
            'show_announcement'     => 'boolean',
        ];
    }

    /**
     * Singleton: sistemde her zaman tek bir kayıt bulunur.
     * 5 dakika cache'lenir; kayıt değişince otomatik temizlenir.
     */
    public static function get(): self
    {
        return Cache::remember('app_settings', 300, function () {
            return self::firstOrCreate([], [
                'maintenance_mode'      => false,
                'force_update_required' => false,
                'minimum_version'       => '1.0.0',
                'latest_version'        => '1.0.0',
                'show_announcement'     => false,
                'announcement_type'     => 'info',
            ]);
        });
    }

    protected static function booted(): void
    {
        static::saved(fn ()   => Cache::forget('app_settings'));
        static::deleted(fn () => Cache::forget('app_settings'));
    }
}
