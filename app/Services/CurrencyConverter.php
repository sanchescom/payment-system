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

        $day = $date->format('Y-m-d');

        $rate = 0.1;

//        if (isset(self::$cached[$day]) && !empty(self::$cached[$day]))
//        {
//            $rate = self::$cached[$day];
//        }
//        else
//        {
//            /**
//             * @var self $currency
//             */
//            $currency = self::query()->where('date', $date)->get()->first();
//
//            if (!$currency)
//            {
//                return $value;
//            }
//
//            $rate = self::$cached[$day] = $currency->average[$source];
//
//            if (empty($rate))
//            {
//                return $value;
//            }
//        }
//
        return $value / $rate;
    }
}