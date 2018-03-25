<?php

namespace App\Jobs;

use App\Services\CurrencyConverter;

class PaymentSpend extends PaymentProcess
{
    public function handle(CurrencyConverter $converter)
    {
        $amount = $this->payment->amount;

    }
}