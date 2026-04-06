<?php

namespace App\Enums;

enum Role: string
{
    case Employee = 'employee';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Employee => 'موظف',
            self::Admin => 'مدير',
        };
    }
}
