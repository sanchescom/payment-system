<?php

namespace App\Jobs;

use App\Services\CurrencyConverter;
use App\User;

class PaymentSpend extends PaymentProcess
{
    public function handle(CurrencyConverter $converter)
    {
        $payee  = User::findByAccount($this->payment->payee);
        $amount = $converter->convert($this->payment->date, $this->payment->currency . "/" . $payee->currency, $this->payment->amount);

        $payee->increaseAmount($amount);

        try
        {
            $this->payment->setSuccessStatus();
            $this->payment->save();
        }
        catch (\Exception $exception)
        {
            $payee->decreaseAmount($amount);

            $payer  = User::findByAccount($this->payment->payer);
            $amount = $converter->convert($this->payment->date, $this->payment->currency . "/" . $payer->currency, $amount);

            $payer->increaseAmount($amount);
        }
    }
}