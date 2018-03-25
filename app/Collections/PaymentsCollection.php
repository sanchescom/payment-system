<?php

namespace App\Collections;

use App\Services\CurrencyConverter;

class PaymentsCollection extends BaseCollection
{
    public function getNativeAndDefaultSum()
    {
        return $this->toArray();
    }
}