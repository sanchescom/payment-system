<?php

namespace App\Http\Controllers;

use App\Entities\AuthUser;
use App\User;

class BaseController extends Controller
{
    /**
     * @return User
     */
    protected function getCurrentUser()
    {
        return AuthUser::$user;
    }
}