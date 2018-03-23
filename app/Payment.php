<?php

namespace App;

class Payment
{
    protected $fillable = [
        'payee',
        'payer',
        'currency',
        'amount',
    ];

    protected $guarded = [
        'name',
        'currency',
        'amount',
        'email',
    ];

    protected $hidden = [
        'secret',
    ];
}