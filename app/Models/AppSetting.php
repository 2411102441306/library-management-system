<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $table = 'app_settings';

    protected static array $cache = [];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (!Schema::hasTable('app_settings')) {
            return $default;
        }

        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        $value = static::query()->where('key', $key)->value('value');
        static::$cache[$key] = $value ?? $default;

        return static::$cache[$key];
    }

    public static function setValue(string $key, mixed $value): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        static::query()->updateOrCreate([
            'key' => $key,
        ], [
            'value' => (string) $value,
        ]);

        static::$cache[$key] = $value;
    }

    public static function borrowingPolicy(): array
    {
        $defaults = config('borrowing');

        return [
            'default_days' => (int) static::getValue('borrowing.default_days', $defaults['default_days']),
            'min_days'     => (int) static::getValue('borrowing.min_days', $defaults['min_days']),
            'max_days'     => (int) static::getValue('borrowing.max_days', $defaults['max_days']),
            'daily_fine'   => (int) static::getValue('borrowing.daily_fine', $defaults['daily_fine']),
            'lost_fee'     => (int) static::getValue('borrowing.lost_fee', 250000),
        ];
    }
}