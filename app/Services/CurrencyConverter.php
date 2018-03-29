<?php

namespace App\Services;

use App\Currency;
use App\Repositories\CurrencyRepository;
use Carbon\Carbon;
use Exchanger\CurrencyPair;
use Illuminate\Database\Eloquent\Collection;

class CurrencyConverter
{
	public function convert(Carbon $date, $pair, $value)
	{
		$currency_pair = CurrencyPair::createFromString($pair);

		if ($currency_pair->isIdentical() || $value == 0)
		{
			return $value;
		}

		$currency_array = [
			$currency_pair->getBaseCurrency(),
			$currency_pair->getQuoteCurrency(),
		];

		$currency = null;

		if ($currency_pair->getBaseCurrency() == Currency::DEFAULT_CURRENCY)
		{
			$currency = $currency_pair->getQuoteCurrency();
		}
		elseif ($currency_pair->getQuoteCurrency() == Currency::DEFAULT_CURRENCY)
		{
			$currency = $currency_pair->getBaseCurrency();
		}

		if ($currency)
		{
			$actual_currency = Currency::firstOrNew([
				'date'     => $date->format('Y-m-d'),
				'currency' => $currency,
			]);

			if (!$actual_currency->rate)
			{
				$actual_currency->rate = \Swap::historical(Currency::DEFAULT_CURRENCY . '/' . $currency, $date)->getValue();
				$actual_currency->save();
			}

			if ($currency_pair->getBaseCurrency() == Currency::DEFAULT_CURRENCY)
			{
				return $value * $actual_currency->rate;
			}
			else
			{
				return $value / $actual_currency->rate;
			}
		}
		elseif (!in_array(Currency::DEFAULT_CURRENCY, $currency_array))
		{
			/**
			 * @var Currency[]|Collection $actual_currencies
			 */
			$actual_currencies = CurrencyRepository::getCurrenciesRangeOnDate($currency_array, $date);

			if ($actual_currencies->count() < count($currency_array))
			{
				foreach ($currency_array as $currency_item)
				{
					if (empty($actual_currencies[$currency_item]))
					{
						$actual_currencies->offsetSet($currency_item,
							Currency::create([
								'date'     => $date,
								'currency' => $currency_item,
								'rate'     => $this->fetchRateOnDate($currency_item, $date),
							]));
					}
				}
			}

			$base_rate  = $actual_currencies[$currency_pair->getBaseCurrency()]->rate;
			$quote_rate = $actual_currencies[$currency_pair->getQuoteCurrency()]->rate;


			return ($value / $base_rate) * $quote_rate;
		}
	}


	private function fetchRateOnDate($currency, $date)
	{
		return \Swap::historical(Currency::DEFAULT_CURRENCY . '/' . $currency, $date)->getValue();
	}
}