<?php

namespace App\Jobs;

use App\Currency;
use App\Services\CurrencyConverter;
use App\User;

/**
 * This is class for handling queue of users transactions for recharge accounts
 *
 * Class PaymentIncome
 *
 * @package App\Jobs
 */
class PaymentIncome extends PaymentProcess
{
	public function handle(CurrencyConverter $converter)
	{
		$user = User::findByAccount($this->payment->payee);

		$native_pair  = $this->payment->currency . "/" . $user->currency;
		$default_pair = $this->payment->currency . "/" . Currency::DEFAULT_CURRENCY;

		$native  = $converter->convert($this->payment->date, $native_pair, $this->payment->amount);
		$default = $converter->convert($this->payment->date, $default_pair, $this->payment->amount);

		$user->increaseAmount($native);

		try
		{
			$this->payment->setNative($native);
			$this->payment->setDefault($default);
			$this->payment->setSuccessStatus();
			$this->payment->save();
		}
		catch (\Exception $exception)
		{
			$user->decreaseAmount($native);
		}
	}
}