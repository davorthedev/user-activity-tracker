<?php

declare(strict_types=1);

namespace App\Auth;

enum Role: string
{
    case User = 'user';
    case Admin = 'admin';

    public function allows(self $role): bool
    {
        return match ($role) {
            self::User => true,
            self::Admin => self::Admin === $this,
        };
    }
}
