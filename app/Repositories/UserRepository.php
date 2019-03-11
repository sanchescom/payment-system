<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    public static function getUsersCurrencies()
    {
        return User::query()->select('currency')->distinct()->pluck('currency');
    }


    public static function getUsers()
    {
        return User::query()->whereNotNull('account')->get([
            'id',
            'name',
            'account',
            'currency',
        ]);
    }
}
