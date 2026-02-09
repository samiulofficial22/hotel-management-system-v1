<?php

if (! function_exists('money')) {
    /**
     * Format amount as Taka (BDT) with symbol.
     * Used across the system for price, salary, total, etc.
     */
    function money($amount, int $decimals = 2): string
    {
        $symbol = config('app.currency_symbol', '৳');
        return $symbol . ' ' . number_format((float) $amount, $decimals);
    }
}
