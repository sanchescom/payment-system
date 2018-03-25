<?php

namespace App\Services;

use App\Currency;
use Carbon\Carbon;
use Exchanger\CurrencyPair;

class CurrencyConverter
{
    public function convertDefault(Carbon $date, $currency, $value)
    {
        if ($currency === Currency::DEFAULT_CURRENCY)
        {
            return $value;
        }

        /**
         * @var Currency $actual_currency
         */
        $actual_currency = Currency::query()->firstOrNew([
            'date'     => $date->format('Y-m-d'),
            'currency' => $currency,
        ]);

        if (!$actual_currency->rate)
        {
            $actual_currency->rate = \Swap::historical($currency . '/' . Currency::DEFAULT_CURRENCY, $date)->getValue();
            $actual_currency->save();
        }

        return $value / $actual_currency->rate;
    }


    public function convert(Carbon $date, $pair, $value)
    {

        $currency = CurrencyPair::createFromString($pair);

        return "";
//        if ($currency === Currency::DEFAULT_CURRENCY)
//        {
//            return $value;
//        }
//
//        /**
//         * @var Currency $actual_currency
//         */
//        $actual_currency = Currency::query()->firstOrNew([
//            'date'     => $date->format('Y-m-d'),
//            'currency' => $currency,
//        ]);
//
//        if (!$actual_currency->rate)
//        {
//            $actual_currency->rate = \Swap::historical($currency . '/' . Currency::DEFAULT_CURRENCY, $date)->getValue();
//            $actual_currency->save();
//        }
//
//        return $value / $actual_currency->rate;
    }
}