<?php 

if (!function_exists('set_precision')) { //sets the number in to two decimal places.
    function set_precision($number, $precision = 0) {
        return intval($number * ($p = pow(10, $precision))) / $p;
    }
}