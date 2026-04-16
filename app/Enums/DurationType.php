<?php

namespace App\Enums;

enum DurationType: string
{
    case HALF_DAY = 'half_day';
    case FULL_DAY = 'full_day';
    case MULTI_DAY = 'multi_day';

    public function label(): string
    {
        return match($this) {
            self::HALF_DAY => 'Mezza giornata',
            self::FULL_DAY => 'Giornata intera',
            self::MULTI_DAY => 'Più giorni',
        };
    }
}
