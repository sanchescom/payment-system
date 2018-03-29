<?php

namespace App\Jobs;

use App\Currency;
use App\Services\AccountProcessor;
use App\Services\CurrencyConverter;
use App\User;

/**
 * This is class for handling queue of users transactions between each other
 *
 * Class PaymentSpend
 *
 * @package App\Jobs
 */
class PaymentSpend extends PaymentProcess
{
	public function handle(CurrencyConverter $converter, AccountProcessor $processor)
	{
		$payee = User::findByAccount($this->payment->payee);

		$native_pair    = $this->payment->currency . "/" . $processor->getCurrency($this->payment->payer);
		$default_pair   = $this->payment->currency . "/" . Currency::DEFAULT_CURRENCY;
		$converted_pair = $this->payment->currency . "/" . $payee->currency;

		$native    = $converter->convert($this->payment->date, $native_pair, $this->payment->amount);
		$default   = $converter->convert($this->payment->date, $default_pair, $this->payment->amount);
		$converted = $converter->convert($this->payment->date, $converted_pair, $this->payment->amount);

		$payee->increaseAmount($converted);

		try
		{
			$this->payment->setNative($native);
			$this->payment->setDefault($default);
			$this->payment->setSuccessStatus();
			$this->payment->save();
		}
		catch (\Exception $exception)
		{
			$payee->decreaseAmount($converted);

			$payer = User::findByAccount($this->payment->payer);
			$payer->increaseAmount($native);
		}
	}
}