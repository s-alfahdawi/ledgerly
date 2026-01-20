<?php

namespace App\Helpers;

use App\Enums\Currency;
use NumberFormatter;

class CurrencyHelper
{
    /**
     * Format amount with currency suffix (e.g., "1,000.00 IQD").
     */
    public static function format(float|string $amount, string $currencyCode): string
    {
        $amount = (float) $amount;
        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::GROUPING_USED, 1);
        $formatted = $formatter->format($amount);
        
        return $formatted . ' ' . $currencyCode;
    }

    /**
     * Format amount with currency prefix.
     */
    public static function formatWithPrefix(float|string $amount, string $currencyCode): string
    {
        return self::format($amount, $currencyCode);
    }

    /**
     * Get currency symbol.
     */
    public static function getSymbol(string $currencyCode): string
    {
        return match($currencyCode) {
            'IQD' => 'IQD',
            'USD' => 'USD',
            default => $currencyCode,
        };
    }
}
