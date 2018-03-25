<?php

namespace App\Services;

class AccountProcessor
{
    /**
     * This is could be a generator for user's account number
     * with encrypt some special information but now it's just
     * a combination of user currency and primary key
     *
     * @param $currency
     * @param $id
     * @return string
     */
    public function generate($currency, $id)
    {
        return $currency. sprintf("%'011d", $id);
    }


    public function getUserId($account)
    {
        return ltrim(preg_replace("/[^0-9\.]/", '', $account), 0);
    }


    public function getCurrency($account)
    {
        return substr($account, 0, 3);
    }
}