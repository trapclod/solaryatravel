<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match($this) {
            self::CUSTOMER => 'Cliente',
            self::ADMIN => 'Amministratore',
            self::SUPER_ADMIN => 'Super Amministratore',
        };
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::ADMIN, self::SUPER_ADMIN]);
    }
}
