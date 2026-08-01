<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $group
 * @property string $key
 * @property mixed $value
 * @property string $type
 * @property string $label
 * @property string|null $help
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SiteSetting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'help',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'json',
            'sort_order' => 'integer',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = static::cached();

        return $settings[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public static function cached(): array
    {
        return Cache::rememberForever('site_settings', function () {
            return static::query()
                ->orderBy('sort_order')
                ->get()
                ->mapWithKeys(fn (self $setting) => [$setting->key => $setting->value])
                ->all();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('site_settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
