<?php

namespace App\Enums;

enum Currency: string
{
    case IQD = 'IQD';
    case USD = 'USD';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::IQD => 'Iraqi Dinar (IQD)',
            self::USD => 'US Dollar (USD)',
        };
    }
}
