<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'In attesa',
            self::PROCESSING => 'In elaborazione',
            self::SUCCEEDED => 'Completato',
            self::FAILED => 'Fallito',
            self::CANCELLED => 'Annullato',
            self::REFUNDED => 'Rimborsato',
            self::PARTIALLY_REFUNDED => 'Parzialmente rimborsato',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::SUCCEEDED => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'purple',
            self::PARTIALLY_REFUNDED => 'orange',
        };
    }
}
