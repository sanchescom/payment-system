<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getUsersCurrencies()
    {
        return User::query()
            ->select('currency')
            ->distinct()
            ->pluck('currency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUsers()
    {
        return User::query()
            ->whereNotNull('account')
            ->get([
                'id',
                'name',
                'account',
                'currency',
            ]);
    }
}
