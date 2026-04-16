<?php

namespace App\Enums;

enum BookingType: string
{
    case SEATS = 'seats';
    case EXCLUSIVE = 'exclusive';

    public function label(): string
    {
        return match($this) {
            self::SEATS => 'Posti singoli',
            self::EXCLUSIVE => 'Barca esclusiva',
        };
    }
}
