<?php


use App\Services\GlobalService;

if (!function_exists('round_number')){
    /**
     * Rounds a given number to a specified number of decimal places.
     * The decimal places are retrieved from the GlobalService configuration.
     *
     * @param float|int $number The number to be rounded.
     * @return float The rounded number.
     */
    function round_number($number): float
    {
        $decimal = GlobalService::get_decimal_length();
        return round($number, $decimal);
    }
}

if (!function_exists('format_decimal')){
    /**
     * Formats a given number to a decimal format based on the configured decimal length,
     * but without trailing zeros on the right. If all fractional digits are zeros,
     * the decimal separator is removed.
     *
     * @param float|int $number The number to format.
     *
     * @return string The formatted number as a string.
     */
    function format_decimal($number): string
    {
        $decimal = GlobalService::get_decimal_length();

        // Format with the configured decimal length first (for proper rounding)
        $formatted = number_format($number, $decimal, ',', ' ');

        // If there is no decimal separator or decimal precision is zero, return as-is
        if ($decimal <= 0 || strpos($formatted, ',') === false) {
            return $formatted;
        }

        // Split integer and fractional parts
        [$intPart, $decPart] = explode(',', $formatted, 2);

        // Trim trailing zeros from fractional part
        $decPart = rtrim($decPart, '0');

        // If fractional part becomes empty, return just the integer part
        if ($decPart === '') {
            return $intPart;
        }

        // Otherwise, return the recombined number
        return $intPart . ',' . $decPart;
    }
}
