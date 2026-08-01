<?php

namespace App\Enums;

enum MortgagePurpose: string
{
    case Purchase = 'purchase';
    case Refinance = 'refinance';
    case Switch = 'switch';
    case Equity = 'equity';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Comprar una vivienda',
            self::Refinance => 'Mejorar mi hipoteca actual',
            self::Switch => 'Cambiar de banco',
            self::Equity => 'Ampliar o sacar liquidez',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}
