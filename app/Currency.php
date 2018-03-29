<?php

namespace App;

use Carbon\Carbon;

/**
 * Class Currency
 *
 * @property Carbon $date
 * @property float $rate
 * @property string $currency
 *
 * @method static Currency firstOrNew(array $attributes, array $values = [])
 *
 * @package App
 */
class Currency extends BaseModel
{
	const DEFAULT_CURRENCY = 'USD';

	protected $fillable = [
		'date',
		'rate',
		'currency',
	];

	protected $dates = [
		'created_at',
		'updated_at',
		'date',
	];
}