<?php

namespace App\Jobs;

use App\Payment;
use App\Services\CurrencyConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PaymentProcess implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $payment;


    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }


    public function handle(CurrencyConverter $converter)
    {
        $amount = $this->payment->amount;
    }
}
