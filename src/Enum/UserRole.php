<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Application roles as a backed enum.
 */
enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    public function isAdmin(): bool
    {
        return self::ROLE_ADMIN === $this;
    }

    public function label(): string
    {
        return match ($this) {
            self::ROLE_USER => 'Utilisateur',
            self::ROLE_ADMIN => 'Administrateur',
        };
    }
}
