<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    public static function getUsersCurrencies()
    {
        return User::query()->select('currency')->distinct()->pluck('currency');
    }
}