<?php

namespace App;

class Currency extends BaseModel
{
    const DEFAULT_CURRENCY = 'USD';

    protected $fillable = [
        'date',
        'rate',
        'currency',
    ];
}