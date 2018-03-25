<?php

namespace App\Services;

use App\Currency;
use Carbon\Carbon;
use Exchanger\CurrencyPair;
use Illuminate\Database\Eloquent\Collection;

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
        $currency_pair  = CurrencyPair::createFromString($pair);
        $currency_array = [
            $currency_pair->getBaseCurrency(),
            $currency_pair->getQuoteCurrency(),
        ];

        // "USD/RUR" 2
        // "RUR/USD" 2000
        // "RUB/UAH" 900

        $currency = null;

        if ($currency_pair->getBaseCurrency() == Currency::DEFAULT_CURRENCY)
        {
            $currency = $currency_pair->getQuoteCurrency();
        }
        elseif ($currency_pair->getQuoteCurrency() == Currency::DEFAULT_CURRENCY)
        {
            $currency = $currency_pair->getBaseCurrency();
        }

        if ($currency)
        {
            /**
             * @var Currency $actual_currency
             */
            $actual_currency = Currency::query()->firstOrNew([
                'date'     => $date->format('Y-m-d'),
                'currency' => $currency,
            ]);

            if (!$actual_currency->rate)
            {
                $actual_currency->rate = \Swap::historical(Currency::DEFAULT_CURRENCY . '/' . $currency, $date)->getValue();
                $actual_currency->save();
            }

            if ($currency_pair->getBaseCurrency() == Currency::DEFAULT_CURRENCY)
            {
                return $value * $actual_currency->rate;
            }
            else
            {
                return $value / $actual_currency->rate;
            }
        }
        elseif (!in_array(Currency::DEFAULT_CURRENCY, $currency_array))
        {
            /**
             * @var Currency[]|Collection $actual_currencies
             */
            $actual_currencies = Currency::query()
                ->where('date', $date->format('Y-m-d'))
                ->whereIn('currency', $currency_array)
                ->get()
                ->keyBy('currency');

            if ($actual_currencies->count() < count($currency_array))
            {
                foreach ($currency_array as $currency_item)
                {
                    if (empty($actual_currencies[$currency_item]))
                    {
                        $actual_currencies->offsetSet($currency_item, Currency::create([
                            'date'     => $date->format('Y-m-d'),
                            'currency' => $currency_item,
                            'rate'     => \Swap::historical(Currency::DEFAULT_CURRENCY . '/' . $currency_item, $date)->getValue()
                        ]));
                    }
                }
            }


        }
    }
}