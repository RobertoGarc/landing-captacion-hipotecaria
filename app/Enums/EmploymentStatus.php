<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case Permanent = 'permanent';
    case Temporary = 'temporary';
    case SelfEmployed = 'self_employed';
    case CivilServant = 'civil_servant';
    case Unemployed = 'unemployed';
    case Retired = 'retired';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Permanent => 'Indefinido / fijo',
            self::Temporary => 'Temporal / eventual',
            self::SelfEmployed => 'Autónomo',
            self::CivilServant => 'Funcionario',
            self::Unemployed => 'Desempleado',
            self::Retired => 'Jubilado / pensionista',
            self::Other => 'Otra situación',
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
