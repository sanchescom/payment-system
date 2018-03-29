<?php

namespace App\Repositories;

use App\Collections\PaymentsCollection;
use App\Payment;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentRepository
{
	/**
	 * @param User $user
	 * @param Carbon|null $from_date
	 * @param Carbon|null $to_date
	 * @return PaymentsCollection|Payment[]|Builder[]
	 */
	public static function getForUserByPeriod(User $user, Carbon $from_date = null, Carbon $to_date = null)
	{
		$builder = Payment::query();

		$builder = self::getByFilter($builder,
			[
				'account' => $user->getAccount(),
				'date'    => [
					'from' => $from_date ? $from_date->format('Y-m-d') : null,
					'to'   => $to_date ? $to_date->format('Y-m-d') : null,
				],
			]);

		$builder->orderByDesc('created_at');

		return $builder->get();
	}


	/**
	 * @param User $user
	 * @param Carbon|null $from_date
	 * @param Carbon|null $to_date
	 * @return PaymentsCollection|Payment[]|Builder[]
	 */
	public static function getSumForUserByPeriodGroupedByCurrencies(User $user, Carbon $from_date = null, Carbon $to_date = null)
	{
		$builder = Payment::query();

		$builder = self::getByFilter($builder,
			[
				'account' => $user->getAccount(),
				'date'    => [
					'from' => $from_date ? $from_date->format('Y-m-d') : null,
					'to'   => $to_date ? $to_date->format('Y-m-d') : null,
				],
			]);

		return $builder
			->selectRaw('SUM(`default`) AS default_sum, SUM(`native`) AS native_sum')
			->get();
	}


	/**
	 * @param Builder $builder
	 * @param array $filter
	 * @return Builder
	 */
	private static function getByFilter(Builder $builder, array $filter = [])
	{
		foreach ($filter as $key => $value)
		{
			switch ($key)
			{
				case 'account':
					$builder->where(function(Builder $query) use ($value) {
						$query->where('payee', $value)
							->orWhere('payer', $value);
					});
					break;
				case 'date':
					if ($value['from'] && $value['to'])
					{
						$builder->whereBetween('date',
							[
								$value['from'],
								$value['to'],
							]);
					}
					elseif ($value['from'] && !$value['to'])
					{
						$builder->where('date', '>=', $value['from']);
					}
					elseif (!$value['from'] && $value['to'])
					{
						$builder->where('date', '<=', $value['to']);
					}
					break;
			}
		}

		return $builder;
	}
}