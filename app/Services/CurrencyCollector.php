<?php

namespace App\Services;

use App\Currency;

class CurrencyCollector
{
    public function create($currency)
    {
        $rate = $this->fetch($currency);

        return Currency::query()->updateOrCreate([
            'date'     => $rate->getDate()->format('Y-m-d'),
            'currency' => $currency,
        ], [
            'rate'=> $rate->getValue(),
        ]);
    }


    public function fetch($currency)
    {
        return \Swap::latest($currency . '/' . Currency::DEFAULT_CURRENCY);
    }
}