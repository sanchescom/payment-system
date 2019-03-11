<?php

namespace App\Collections;

use App\Payment;
use App\User;

class PaymentsCollection extends BaseCollection
{
    public function getNativeAndDefaultSum()
    {
        $native_sum  = $this->first()[Payment::NATIVE_DYNAMIC_SUM_FIELD];
        $default_sum = $this->first()[Payment::DEFAULT_DYNAMIC_SUM_FIELD];

        return [
            Payment::NATIVE_DYNAMIC_SUM_FIELD  => $native_sum > 0 ? $native_sum / 100 : 0,
            Payment::DEFAULT_DYNAMIC_SUM_FIELD => $default_sum > 0 ? $default_sum / 100 : 0,
        ];
    }


    public function getDataForCsv(User $user)
    {
        return $this->map(function (Payment $payment) use ($user) {
            return [
                $user->name,
                $payment->payee,
                $payment->payer,
                $payment->amount,
                $payment->currency,
                $payment->date->toDateString(),
            ];
        });
    }


    public function getData()
    {
        return $this->map(function (Payment $payment) {
            return [
                'id'       => $payment->id,
                'payee'    => $payment->payee,
                'payer'    => $payment->payer,
                'amount'   => $payment->amount,
                'currency' => $payment->currency,
                'date'     => $payment->date->toDateString(),
            ];
        });
    }
}
