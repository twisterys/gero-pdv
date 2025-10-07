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
     * Formats a given number to a decimal format based on the configured decimal length.
     *
     * @param float|int $number The number to format.
     *
     * @return string The formatted number as a string.
     */
    function format_decimal($number): string
    {
        $decimal = GlobalService::get_decimal_length();
        return number_format($number, $decimal, ',', ' ');
    }
}
