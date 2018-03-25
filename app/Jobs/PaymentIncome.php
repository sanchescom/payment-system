<?php

namespace App\Jobs;

use App\Services\CurrencyConverter;
use Carbon\Carbon;

class PaymentIncome extends PaymentProcess
{
    public function handle(CurrencyConverter $converter)
    {
        $amount   = $this->payment->amount;
        $currency = $this->payment->currency;

        if ($this->payment->currency !== $this->payment->payee_user->currency)
        {
            $amount = $converter->convert(Carbon::now(), $currency, $amount);
        }

        $this->payment->payee_user->increaseAmount($amount);

        try
        {
            $this->payment->setSuccessStatus();
            $this->payment->save();
        }
        catch (\Exception $exception)
        {
            $this->payment->payee_user->decreaseAmount($amount);
        }
    }
}