<?php

namespace App\Jobs;

use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;

class PaymentIncome extends PaymentProcess
{
    public function handle(CurrencyConverter $converter)
    {
        $amount = $this->payment->amount;

        $user   = User::findByAccount($this->payment->payee);

        if ($this->payment->currency !== $user->currency)
        {
            $amount = $converter->convert(Carbon::now(), $this->payment->currency . "/" . $user->currency, $amount);
        }

        $user->increaseAmount($amount);

        try
        {
            $this->payment->setSuccessStatus();
            $this->payment->save();
        }
        catch (\Exception $exception)
        {
            $user->decreaseAmount($amount);
        }
    }
}