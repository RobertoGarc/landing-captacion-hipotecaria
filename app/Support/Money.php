<?php

namespace App\Support;

final class Money
{
    public const DEFAULT_CURRENCY = 'EUR';

    public static function symbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'USD' => '$',
            default => '€',
        };
    }

    /**
     * Ecuador uses the US convention ($1,234) while Spain writes the symbol last (1.234 €).
     */
    public static function format(?int $amount, ?string $currency = null): string
    {
        $currency = strtoupper($currency ?: self::DEFAULT_CURRENCY);
        $amount ??= 0;

        return $currency === 'USD'
            ? '$'.number_format($amount, 0, '.', ',')
            : number_format($amount, 0, ',', '.').' €';
    }
}
