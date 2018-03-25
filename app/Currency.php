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
}