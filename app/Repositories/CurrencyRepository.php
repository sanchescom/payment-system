<?php

namespace App\Repositories;

use App\Currency;
use Carbon\Carbon;

class CurrencyRepository
{
	public static function getCurrenciesRangeOnDate(array $currency_array, Carbon $date)
	{
		return Currency::query()
			->where('date', $date->format('Y-m-d'))
			->whereIn('currency', $currency_array)
			->get()
			->keyBy('currency');
	}
}