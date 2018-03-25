<?php

namespace App\Jobs;

use App\Services\CurrencyConverter;
use App\User;
use Carbon\Carbon;

class PaymentIncome extends PaymentProcess
{
    public function handle(CurrencyConverter $converter)
    {
        $user   = User::findByAccount($this->payment->payee);
        $amount = $converter->convert(Carbon::now(), $this->payment->currency . "/" . $user->currency, $this->payment->amount);

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