<?php

if (!function_exists('currency_pair')) {
    /**
     * Returns a currency pair
     *
     * @param $base
     * @param $source
     * @return string a string of currency pair USD/EUR
     *
     */
    function currency_pair($base, $source)
    {
        return sprintf('%s/%s', $base, $source);
    }
}