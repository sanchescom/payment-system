<?php

namespace App\Services;

use App\Currency;
use Carbon\Carbon;

class CurrencyConverter
{
    public function convert(Carbon $date, $currency, $value)
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


    public function convert1(Carbon $date, $currency, $value)
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
}