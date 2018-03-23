<?php

namespace App\Http\Controllers;

use App\Entities\AuthUser;
use App\User;

class BaseController extends Controller
{
    private static $user;


    /**
     * @return User
     */
    protected function getCurrentUser()
    {
        return self::$user;
    }


    public static function setUser($user)
    {
        self::$user = $user;
    }
}