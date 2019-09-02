<?php 

if (!function_exists('number_format_no_round')) {
    function number_format_no_round($value, $decimals = 0, $dec_point = '.', $thousands_sep = ',') {
        $negation = ($value < 0) ? (-1) : 1;
        $coefficient = pow(10, $decimals);
        $value = $negation * floor((string)(abs($value) * $coefficient)) / $coefficient;
        return number_format($value, $decimals, $dec_point, $thousands_sep);
    }
}