<?php

namespace App\Http\Controllers;

use App\Entities\AuthUser;

class BaseController extends Controller
{
    protected function getCurrentUser()
    {
        return AuthUser::$user;
    }
}