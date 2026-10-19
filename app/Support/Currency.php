<?php

namespace App\Support;

final class Currency
{
    /**
     * Currencies currently supported by the application.
     */
    public const SUPPORTED = [
        'KES',
        'USD',
        'CDF',
    ];

    /**
     * Get the display symbol for a currency.
     */
    public static function symbol(string $currency): string
    {
        return match ($currency) {
            'KES' => 'KSh',
            'USD' => '$',
            'CDF' => 'FC',
            default => $currency,
        };
    }

    /**
     * Get the full currency name.
     */
    public static function name(string $currency): string
    {
        return match ($currency) {
            'KES' => 'Kenyan Shilling',
            'USD' => 'US Dollar',
            'CDF' => 'Congolese Franc',
            default => $currency,
        };
    }

    /**
     * Format a monetary amount.
     */
    public static function format(
        float $amount,
        string $currency
    ): string {
        return self::symbol($currency)
            . ' '
            . number_format($amount, 2)
            . ' '
            . $currency;
    }
}