<?php

namespace App\Support;

final class DemoBrand
{
    public const SESSION_KEY = 'demo_brand';

    public static function currentKey(): string
    {
        $key = session(self::SESSION_KEY, config('demo_brands.default'));

        return self::exists($key) ? $key : (string) config('demo_brands.default');
    }

    public static function set(string $key): void
    {
        if (! self::exists($key)) {
            return;
        }

        session([self::SESSION_KEY => $key]);
    }

    public static function exists(string $key): bool
    {
        return array_key_exists($key, config('demo_brands.brands', []));
    }

    /**
     * @return array{label: string, theme: string, country: string, currency: string, content: array<string, mixed>}
     */
    public static function current(): array
    {
        return config('demo_brands.brands.'.self::currentKey());
    }

    /**
     * @return array<string, mixed>
     */
    public static function content(): array
    {
        return self::current()['content'];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::content()[$key] ?? $default;
    }

    public static function theme(): string
    {
        return self::current()['theme'];
    }

    public static function country(): string
    {
        return self::current()['country'];
    }

    public static function currency(): string
    {
        return self::current()['currency'] ?? Money::DEFAULT_CURRENCY;
    }

    public static function currencySymbol(): string
    {
        return Money::symbol(self::currency());
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(config('demo_brands.brands', []))
            ->mapWithKeys(fn (array $brand, string $key) => [$key => $brand['label']])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function provinces(): array
    {
        return self::country() === 'EC'
            ? EcuadorProvinces::all()
            : SpanishProvinces::all();
    }
}
